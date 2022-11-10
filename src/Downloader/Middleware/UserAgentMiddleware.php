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

namespace BlackSpider\Downloader\Middleware;

use BlackSpider\Http\Request;
use BlackSpider\Support\Configurable;

final class UserAgentMiddleware implements RequestMiddlewareInterface
{
    use Configurable;

    public function handleRequest(Request $request): Request
    {
        /** @psalm-suppress MixedArgument */
        $version = rand(60, 600);
        $ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$version}.0.0.0 Safari/537.36";
        return $request->addHeader('User-Agent', $ua);
//        return $request->addHeader('User-Agent', $this->option('userAgent'));
    }

    private function defaultOptions(): array
    {
        return [
            'userAgent' => 'roach-php',
        ];
    }
}
