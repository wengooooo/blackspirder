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

use BlackSpider\Downloader\DownloaderMiddlewareInterface;
use GuzzleHttp\Client as GuzzleClient;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;

interface ClientInterface
{

    /**
     * @param Request[]                         $requests
     * @param ?callable(Response): void         $onFulfilled
     * @param ?callable(RequestException): void $onRejected
     */
    public function pool(
//        ArrayIteratorRequestScheduler $scheduler,
//        array $requests,
        array $middleware,
        ?callable $onFulfilled = null,
        ?callable $onRejected = null,
    ): void;
}
