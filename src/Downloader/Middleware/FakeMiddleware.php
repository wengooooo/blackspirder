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

namespace BlackSpider\Downloader\Middleware;

use Closure;
use PHPUnit\Framework\Assert;
use BlackSpider\Downloader\DownloaderMiddlewareInterface;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\Support\Configurable;
use BlackSpider\Exception\Exception;
/**
 * @internal
 */
final class FakeMiddleware implements DownloaderMiddlewareInterface
{
    use Configurable;

    /**
     * @var array Request[]
     */
    private array $requestsHandled = [];

    /**
     * @var array Response[]
     */
    private array $responsesHandled = [];

    /**
     * @param ?Closure(Request): Request   $requestHandler
     * @param ?Closure(Response): Response $responseHandler
     */
    public function __construct(private ?Closure $requestHandler = null, private ?Closure $responseHandler = null, private ?Closure $exceptionHandler = null)
    {
    }

    public function handleRequest(Request $request): Request
    {
        $this->requestsHandled[] = $request;

        if (null !== $this->requestHandler) {
            return ($this->requestHandler)($request);
        }

        return $request;
    }

    public function handleException(Exception $exception): Exception
    {
        $this->exceptionsHandled[] = $exception;

        if (null !== $this->exceptionHandler) {
            return ($this->exceptionHandler)($exception);
        }

        return $exception;
    }


    public function handleResponse(Response $response): Response
    {
        $this->responsesHandled[] = $response;

        if (null !== $this->responseHandler) {
            return ($this->responseHandler)($response);
        }

        return $response;
    }

    public function assertRequestHandled(Request $request): void
    {
        Assert::assertContains($request, $this->requestsHandled);
    }

    public function assertRequestNotHandled(Request $request): void
    {
        Assert::assertNotContains($request, $this->requestsHandled);
    }

    public function assertNoRequestsHandled(): void
    {
        Assert::assertEmpty($this->requestsHandled);
    }

    public function assertResponseHandled(Response $response): void
    {
        Assert::assertContains($response, $this->responsesHandled);
    }

    public function assertResponseNotHandled(Response $response): void
    {
        Assert::assertNotContains($response, $this->responsesHandled);
    }

    public function assertNoResponseHandled(): void
    {
        Assert::assertEmpty($this->responsesHandled);
    }
}
