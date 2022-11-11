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

namespace BlackSpider;

use Psr\Container\ContainerInterface;
use BlackSpider\Core\DefaultContainer;
use BlackSpider\Core\FakeRunner;
use BlackSpider\Core\RunnerInterface;
use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\Spider\Configuration\Overrides;
use BlackSpider\Spider\SpiderInterface;

final class BlackSpider
{
    private static ?ContainerInterface $container = null;

    private static ?FakeRunner $runnerFake = null;

    public static function useContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function fake(): FakeRunner
    {
        if (null === self::$runnerFake) {
            self::$runnerFake = new FakeRunner();
        }

        return self::$runnerFake;
    }

    public static function restore(): void
    {
        self::$runnerFake = null;
    }

    /**
     * Start the spider run without collecting scraped items.
     *
     * @psalm-param class-string<SpiderInterface> $spiderClass
     */
    public static function startSpider(string $spiderClass, ?Overrides $overrides = null, array $context = []): void
    {
        self::getRunner()->startSpider($spiderClass, $overrides, $context);
    }

    /**
     * Start the spider run and collect and return scraped items.
     *
     * @psalm-param class-string<SpiderInterface> $spiderClass
     *
     * @return ItemInterface[]
     */
    public static function collectSpider(string $spiderClass, ?Overrides $overrides = null, array $context = []): array
    {
        return self::getRunner()->collectSpider($spiderClass, $overrides, $context);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $class
     * @psalm-suppress MixedInferredReturnType
     *
     * @return T
     */
    public static function resolve(string $class): mixed
    {
        /** @psalm-suppress MixedReturnStatement */
        return self::getContainer()->get($class);
    }

    private static function getContainer(): ContainerInterface
    {
        if (null === self::$container) {
            self::$container = new DefaultContainer();
        }

        return self::$container;
    }

    private static function getRunner(): RunnerInterface
    {
        return self::$runnerFake ?: self::resolve(RunnerInterface::class);
    }
}
