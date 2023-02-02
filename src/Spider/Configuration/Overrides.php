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

namespace BlackSpider\Spider\Configuration;

use BlackSpider\Downloader\DownloaderMiddlewareInterface;
use BlackSpider\Extensions\ExtensionInterface;
use BlackSpider\ItemPipeline\Processors\ItemProcessorInterface;
use BlackSpider\Spider\SpiderMiddlewareInterface;

/**
 * @psalm-immutable
 */
final class Overrides
{
    /**
     * @param null|string[] $startUrls
     * @param class-string<DownloaderMiddlewareInterface>[]|null $downloaderMiddleware
     * @param class-string<SpiderMiddlewareInterface>[]|null $spiderMiddleware
     * @param class-string<ItemProcessorInterface>[]|null $itemProcessors
     * @param class-string<ExtensionInterface>[]|null $extensions
     */
    public function __construct(
        public ?array $startUrls = null,
        public ?array $downloaderMiddleware = null,
        public ?array $spiderMiddleware = null,
        public ?array $itemProcessors = null,
        public ?array $extensions = null,
        public ?int $concurrency = null,
        public ?int $requestDelay = null,
        public ?int $queue = null,
    ) {
    }

    /**
     * @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement
     *
     * @return array{
     *     startUrls?: string[],
     *     downloaderMiddleware?: class-string<DownloaderMiddlewareInterface>[],
     *     spiderMiddleware?: class-string<SpiderMiddlewareInterface>[],
     *     itemProcessors?: class-string<ItemProcessorInterface>[],
     *     extensions?: class-string<ExtensionInterface>[],
     *     concurrency?: int,
     *     requestDelay?: int,
     *     queue?: int,
     * }
     */
    public function toArray(): array
    {
        return \array_filter([
            'startUrls' => $this->startUrls,
            'downloaderMiddleware' => $this->downloaderMiddleware,
            'spiderMiddleware' => $this->spiderMiddleware,
            'itemProcessors' => $this->itemProcessors,
            'extensions' => $this->extensions,
            'concurrency' => $this->concurrency,
            'requestDelay' => $this->requestDelay,
            'queue' => $this->queue,
        ], static fn ($value) => null !== $value);
    }
}
