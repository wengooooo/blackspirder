<?php

declare(strict_types=1);

namespace BlackSpider\Downloader\Middleware;

use BlackSpider\Downloader\Middleware\RequestMiddlewareInterface;
use BlackSpider\Support\Configurable;
use BlackSpider\Http\Request;

class ProxyMiddleware implements RequestMiddlewareInterface
{
    use Configurable;

    public function handleRequest(Request $request): Request
    {
        return $request->addOption('proxy', $this->option('proxy'));
    }

    private function defaultOptions(): array
    {
        return [
            'proxy' => [],
        ];
    }
}