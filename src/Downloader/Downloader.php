<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 Kai Sassnowski
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/wengooooo/blackspirder
 */

namespace BlackSpider\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use BlackSpider\Iterators\ExpectingIterator;
use BlackSpider\Iterators\MapIterator;
use BlackSpider\Events\RequestDropped;
use BlackSpider\Events\RequestSending;
use BlackSpider\Exception\Exception;
use BlackSpider\Http\ClientInterface;
use BlackSpider\Http\Request;

use BlackSpider\Http\Response;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Downloader
{
    /**
     * @var DownloaderMiddlewareInterface[]
     */
    private array $middleware = [];

    /**
     * @var Request[]
     */
//    private \ArrayIterator $requests;
    private array $requests = [];

    public function __construct(
        private ClientInterface $client,
        private EventDispatcherInterface $eventDispatcher,
    ) {

    }

    public function withMiddleware(DownloaderMiddlewareInterface ...$middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function scheduledRequests(): int
    {
        return \count($this->requests);
    }

    public function prepare(Request $request): void
    {
        foreach ($this->middleware as $middleware) {
            $request = $middleware->handleRequest($request);

            if ($request->wasDropped()) {
                $this->eventDispatcher->dispatch(
                    new RequestDropped($request),
                    RequestDropped::NAME,
                );

                return;
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

            return;
        }


        $this->requests[] = $event->request;
    }

    public function flush(?callable $onFullfilled = null): void
    {
//        $httpClient = new Client(['debug' => false]);
//        // MapIterator is just better for readability.
//        $generator = new MapIterator(
//            // Initial data. This object will be always passed as the second parameter to the callback below
//            new \ArrayIterator($this->requests),
////            new \ArrayIterator($this->requests),
//            function ($request, $array) use($httpClient) {
//                return $httpClient->requestAsync('GET', $request)
//                    ->then(function (GuzzleResponse $response) use ($request, $array) {
//                        // The status code for example.
//                        echo $request . ': ' . $response->getStatusCode() . PHP_EOL;
//
//                        // New requests.
//                        $array->append(sprintf('https://httpbin.org/delay/%d', rand(1, 20)));
//                    }, function () {var_dump(1111);});
//            }
//        );
//
//        $generator = new ExpectingIterator($generator);
//
//        $promise = \GuzzleHttp\Promise\each_limit($generator, 5);
//
//        $promise->wait();

        $requests = $this->requests;
        $this->requests = [];
        $this->client->pool(
//        $this->client->pool($requests,
            function (Response $response) use ($onFullfilled): void {
                $this->onResponseReceived($response, $onFullfilled);
            },
            function (Exception $exception): void {
                $this->onExceptionHandled($exception);
            }
        );
    }

    private function onResponseReceived(Response $response, ?callable $callback): void
    {
        foreach ($this->middleware as $middleware) {
            $response = $middleware->handleResponse($response);

            if ($response->wasDropped()) {
                return;
            }
        }

        if (null !== $callback) {
            $callback($response);
        }
    }

    private function onExceptionHandled(Exception $exception): void
    {
        foreach ($this->middleware as $middleware) {
            $exception = $middleware->handleException($exception);
        }
    }
}
