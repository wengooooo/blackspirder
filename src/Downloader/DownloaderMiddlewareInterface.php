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

use BlackSpider\Downloader\Middleware\RequestMiddlewareInterface;
use BlackSpider\Downloader\Middleware\ExceptionMiddlewareInterface;
use BlackSpider\Downloader\Middleware\ResponseMiddlewareInterface;

interface DownloaderMiddlewareInterface extends RequestMiddlewareInterface, ResponseMiddlewareInterface, ExceptionMiddlewareInterface
{
}
