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

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use BlackSpider\Http\Request;
use BlackSpider\Support\Configurable;

final class CookieMiddleware implements RequestMiddlewareInterface
{
    use Configurable;

    private CookieJarInterface $cookieJar;

    public function __construct(?CookieJarInterface $cookieJar = null)
    {
        $this->cookieJar = $cookieJar ?: new CookieJar();
    }

    public function handleRequest(Request $request): Request
    {
        return $request->addOption('cookies', $this->cookieJar);
    }
}
