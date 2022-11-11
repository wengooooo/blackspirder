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

use BlackSpider\Exception\Exception;
use BlackSpider\Http\ClientInterface;

use BlackSpider\Http\Response;

final class Downloader
{
    /**
     * @var DownloaderMiddlewareInterface[]
     */
    private array $middleware = [];


    public function __construct(
        private ClientInterface $client,
    ) {

    }

    public function withMiddleware(DownloaderMiddlewareInterface ...$middleware): self
    {
        $this->middleware = $middleware;

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
