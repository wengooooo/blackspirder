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
use BlackSpider\Spider\ConfigurationLoaderStrategy;
use BlackSpider\Spider\SpiderMiddlewareInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArrayLoader implements ConfigurationLoaderStrategy
{
    /**
     * @var array{
     *             startUrls: string[],
     *             downloaderMiddleware: class-string<DownloaderMiddlewareInterface>[],
     *             spiderMiddleware: class-string<SpiderMiddlewareInterface>[],
     *             itemProcessors: class-string<ItemProcessorInterface>[],
     *             extensions: class-string<ExtensionInterface>[],
     *             concurrency: int,
     *             requestDelay: int
     *             }
     */
    private array $config;

    public function __construct(array $configuration)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'startUrls' => [],
            'downloaderMiddleware' => [],
            'itemProcessors' => [],
            'spiderMiddleware' => [],
            'extensions' => [],
            'concurrency' => 5,
            'requestDelay' => 0,
        ]);

        /** @psalm-suppress MixedPropertyTypeCoercion */
        $this->config = $resolver->resolve($configuration);
    }

    public function load(): Configuration
    {
        return new Configuration(
            $this->config['startUrls'],
            $this->config['downloaderMiddleware'],
            $this->config['itemProcessors'],
            $this->config['spiderMiddleware'],
            $this->config['extensions'],
            $this->config['concurrency'],
            $this->config['requestDelay'],
        );
    }
}
