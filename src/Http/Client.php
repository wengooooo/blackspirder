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

namespace BlackSpider\Http;

use Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use BlackSpider\Exception\Exception;
use BlackSpider\Iterators\ExpectingIterator;
use BlackSpider\Iterators\MapIterator;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;
use BlackSpider\Scheduling\RequestSchedulerInterface;

final class Client implements ClientInterface
{
    private GuzzleClient $client;
    private ArrayIteratorRequestScheduler $scheduler;
    public function __construct(?GuzzleClient $client = null, ArrayIteratorRequestScheduler $scheduler)
    {
        $this->client = $client ?? new GuzzleClient();
        $this->scheduler = $scheduler;
    }

    public function pool(?callable $onFulfilled = null, ?callable $onRejected = null): void {
//    public function pool(array $requests, ?callable $onFulfilled = null, ?callable $onRejected = null): void {

        $generator = new MapIterator(
            $this->scheduler,
//            new \ArrayIterator($requests),
            function (Request $request, $array) use($onFulfilled, $onRejected) {
//                return $this->client->requestAsync('GET', $request)
//                    ->then(function (GuzzleResponse $response) use ($request, $array) {
//                        // The status code for example.
//                        echo $request . ': ' . $response->getStatusCode() . PHP_EOL;
//
//                        // New requests.
//                        $array->append(sprintf('https://httpbin.org/delay/%d', rand(1, 20)));
//                    }, function () {var_dump(1111);});

                var_dump($request->getUri());
                return $this->client->sendAsync($request->getPsrRequest(), $request->getOptions())->then(
//                return $this->client->requestAsync($request->getPsrRequest(), $request->getUri())->then(
                    function (GuzzleResponse $response) use ($request, $array) {
//                        var_dump($request);
//                        var_dump(new Response($response, $request));
//                        return static fn (ResponseInterface $response) => new Response($response, $request);
                        return new Response($response, $request);
//                        return $response;
                    }
                )->then($onFulfilled);
            }
        );

        $generator = new ExpectingIterator($generator);

        $promise = \GuzzleHttp\Promise\each_limit($generator, 5);

        $promise->wait();
    }

    /**
     * @param Request[] $requests
     */
    public function pool2(
        array $requests,
        ?callable $onFulfilled = null,
        ?callable $onRejected = null,
    ): void {
        $makeRequests = function () use ($requests): Generator {
            foreach ($requests as $request) {
                yield function () use ($request) {
                    return $this->client
                        ->sendAsync($request->getPsrRequest(), $request->getOptions())
                        ->then(
                            static fn (ResponseInterface $response) => new Response($response, $request),
//                            static fn (GuzzleException $e) => throw new Exception(
//                                $request,
//                                null,
//                                $e->getMessage(),
//                                $e->getCode(),
//                                $e
//                            ),
                            static function (GuzzleException $reason) use ($request) {
                                throw new Exception($request, $reason);

                                // If we got back a response, we want to return a Response object
                                // so it can get sent through the middleware stack.
//                                if ($reason instanceof BadResponseException) {
//                                    return new Response($reason->getResponse(), $request);
//                                }
//
//                                // For all other cases, we'll wrap the exception in our own
//                                // exception so it can be handled by any request exception middleware.
//                                throw new RequestException($request, $reason);
                            },
                        );
                };
            }
        };

        $pool = new Pool($this->client, $makeRequests(), [
            'concurrency' => 0,
            'fulfilled' => $onFulfilled,
            'rejected' => $onRejected,
        ]);

        $pool->promise()->wait();
    }
}
