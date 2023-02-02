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

namespace BlackSpider\Core;

use BlackSpider\Downloader\DownloaderMiddlewareInterface;
use BlackSpider\Extensions\ExtensionInterface;
use BlackSpider\Http\Request;
use BlackSpider\ItemPipeline\Processors\ItemProcessorInterface;
use BlackSpider\Spider\SpiderMiddlewareInterface;

/**
 * @psalm-immutable
 */
final class Run
{
    /**
     * @param Request[]                       $startRequests
     * @param DownloaderMiddlewareInterface[] $downloaderMiddleware
     * @param ItemProcessorInterface[]        $itemProcessors
     * @param SpiderMiddlewareInterface[]     $responseMiddleware
     * @param ExtensionInterface[]            $extensions
     */
    public function __construct(
        public array|\Generator $startRequests,
        public array $downloaderMiddleware = [],
        public array $itemProcessors = [],
        public array $responseMiddleware = [],
        public array $extensions = [],
        public int $concurrency = 25,
        public int $requestDelay = 0,
        public int $queue = 10000,
    ) {
    }
}
