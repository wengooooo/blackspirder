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
use Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use BlackSpider\Exception\Exception;
use BlackSpider\Iterators\ExpectingIterator;
use BlackSpider\Iterators\MapIterator;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;

final class Client implements ClientInterface
{
    private GuzzleClient $client;
    public function __construct(?GuzzleClient $client = null, private ArrayIteratorRequestScheduler $scheduler, private EventDispatcherInterface $eventDispatcher)
    {
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
                         if ($reason instanceof BadResponseException) {
                             $onFulfilled(new Response($reason->getResponse(), $request));
                         } else {
                             $onRejected(new Exception($request, $reason));
                         }
                    }
                );
            }
        );

        $generator = new ExpectingIterator($generator);

        $promise = \GuzzleHttp\Promise\each_limit($generator, 5);

        $promise->wait();
    }
}
