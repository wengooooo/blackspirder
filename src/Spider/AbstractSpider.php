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

namespace BlackSpider\Spider;

use Generator;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\Spider\Configuration\Configuration;

abstract class AbstractSpider implements SpiderInterface
{
    protected Configuration $configuration;

    protected array $context = [];

    public function __construct(ConfigurationLoaderStrategy $loaderStrategy)
    {
        $this->configuration = $loaderStrategy->load();
    }

    /**
     * @psalm-return Generator<ParseResult>
     */
    abstract public function parse(Response $response): Generator;

    /**
     * @return Request[]|\Generator
     */
    final public function getInitialRequests(): array|\Generator
    {
        return $this->initialRequests();
    }

    final public function withConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    final public function withContext(array $context): void
    {
        $this->context = $context;
    }

    final public function loadConfiguration(): Configuration
    {
        return $this->configuration;
    }

    protected function request(
        string $method,
        string $url,
        string $parseMethod = 'parse',
        array $options = [],
    ): ParseResult {
        return ParseResult::request($method, $url, [$this, $parseMethod], $options);
    }

    protected function item(ItemInterface|array $item): ParseResult
    {
        if ($item instanceof ItemInterface) {
            return ParseResult::fromValue($item);
        }

        return ParseResult::item($item);
    }

    /**
     * @return Request[]|\Generator
     */
    protected function initialRequests(): array|\Generator
    {
        return \array_map(function (string $url) {
            return new Request('GET', $url, [$this, 'parse']);
        }, $this->configuration->startUrls);
    }
}
