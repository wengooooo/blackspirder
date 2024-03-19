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

namespace BlackSpider\Downloader;

use BlackSpider\Exception\Exception;
use BlackSpider\Http\ClientInterface;

use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\Events\ResponseReceived;
use BlackSpider\Events\ResponseReceiving;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Downloader
{
    /**
     * @var DownloaderMiddlewareInterface[]
     */
    private array $middleware = [];

    /**
     * @var list<Request>
     */
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

    public function withConcurrency(int $concurrency): self
    {
        $this->client->concurrency = $concurrency;

        return $this;
    }

    public function execute(?callable $onFullfilled = null): void
    {
        $this->client->pool(
            $this->middleware,
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
        $event = new ResponseReceiving($response);
        $this->eventDispatcher->dispatch($event, ResponseReceiving::NAME);
        $response = $event->response;

        foreach ($this->middleware as $middleware) {
            $response = $middleware->handleResponse($response);

            if ($response->wasDropped()) {
                return;
            }
        }

        $event = new ResponseReceived($response);
        $this->eventDispatcher->dispatch($event, ResponseReceived::NAME);
        $response = $event->response;

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
