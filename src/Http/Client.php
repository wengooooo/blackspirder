<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 WenGo
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/wengooooo/blackspirder
 */

namespace BlackSpider\Http;

use BlackSpider\Events\RequestDropped;
use BlackSpider\Events\RequestSending;
use BlackSpider\Scheduling\SchedulerInterface;
use BlackSpider\Scheduling\Timing\ClockInterface;
use BlackSpider\Spider\Configuration\Configuration;
use BlackSpider\Spider\Configuration\Overrides;
use BlackSpider\Spider\ConfigurationLoaderStrategy;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Each;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use League\Container\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BlackSpider\Exception\Exception;
use BlackSpider\Iterators\ExpectingIterator;
use BlackSpider\Iterators\MapIterator;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;

final class Client implements ClientInterface
{
    private GuzzleClient $client;
    public int $concurrency;

    public function __construct(private SchedulerInterface $scheduler, private EventDispatcherInterface $eventDispatcher, ?GuzzleClient $client = null)    {
        $this->client = $client ?? new GuzzleClient();
    }

    public function pool(array $middlewares, ?callable $onFulfilled = null, ?callable $onRejected = null): void {
        $generator = new MapIterator(
            $this->scheduler,
            function (Request $request) use($middlewares, $onFulfilled, $onRejected) {
                foreach ($middlewares as $middleware) {
                    $request = $middleware->handleRequest($request);

                    if ($request->wasDropped()) {
                        $this->eventDispatcher->dispatch(
                            new RequestDropped($request),
                            RequestDropped::NAME,
                        );

                        return ;
                    }
                }

                /**
                 * @psalm-suppress UnnecessaryVarAnnotation
                 *
                 * @var RequestSending $event
                 */
                $event = $this->eventDispatcher->dispatch(
                    new RequestSending($request),
                    RequestSending::NAME,
                );

                if ($event->request->wasDropped()) {
                    $this->eventDispatcher->dispatch(
                        new RequestDropped($event->request),
                        RequestDropped::NAME,
                    );

                    return ;
                }

                return $this->client->sendAsync($request->getPsrRequest(), $request->getOptions())->then(
                    function (GuzzleResponse $response) use ($request, $onFulfilled) {
                        $onFulfilled(new Response($response, $request));
                    },
                    function (GuzzleException $reason) use ($request, $onFulfilled, $onRejected) {
                        // ClientException 当返回4xx的时候触发
                        // ServerException 当返回5xx的时候触发
                        // ConnectException 当无法连接的时候触发
                         if ($reason instanceof ClientException) {
                             $onFulfilled(new Response($reason->getResponse(), $request));
                         } else {
                             $onRejected(new Exception($request, $reason));
                         }
                    }
                );
            }
        );

        $generator = new ExpectingIterator($generator);

        $promise = Each::ofLimit($generator, $this->concurrency, null, function(\Throwable $reason) use($onRejected) {
            throw $reason;
        });

        $promise->wait();
    }
}
