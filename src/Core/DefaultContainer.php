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

use League\Container\Container;
use League\Container\ReflectionContainer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use BlackSpider\Http\Client;
use BlackSpider\Http\ClientInterface;
use BlackSpider\ItemPipeline\ItemPipeline;
use BlackSpider\ItemPipeline\ItemPipelineInterface;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;
use BlackSpider\Scheduling\Timing\ClockInterface;
use BlackSpider\Scheduling\Timing\SystemClock;
use BlackSpider\Shell\Resolver\NamespaceResolverInterface;
use BlackSpider\Shell\Resolver\StaticNamespaceResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class DefaultContainer implements ContainerInterface
{
    private Container $container;

    public function __construct()
    {
        $this->container = (new Container())->delegate(new ReflectionContainer());

        $this->registerDefaultBindings();
    }

    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    private function registerDefaultBindings(): void
    {
        $this->container->addShared(
            LoggerInterface::class,
            static fn () => (new Logger('roach'))->pushHandler(new StreamHandler('php://stdout')),
        );
        $this->container->addShared(EventDispatcher::class, EventDispatcher::class);
        $this->container->addShared(EventDispatcherInterface::class, EventDispatcher::class);
        $this->container->add(ClockInterface::class, SystemClock::class);
        $this->container->addShared(ArrayIteratorRequestScheduler::class, ArrayIteratorRequestScheduler::class);
        $this->container->add(ClientInterface::class, fn (): ClientInterface => $this->container->get(Client::class));
        $this->container->add(
            ItemPipelineInterface::class,
            /** @psalm-suppress MixedReturnStatement, MixedInferredReturnType */
            fn (): ItemPipelineInterface => $this->container->get(ItemPipeline::class),
        );
        $this->container->add(NamespaceResolverInterface::class, StaticNamespaceResolver::class);
        $this->container->add(
            EngineInterface::class,
            /** @psalm-suppress MixedReturnStatement, MixedInferredReturnType */
            fn (): EngineInterface => $this->container->get(Engine::class),
        );
        $this->container->add(
            RunnerInterface::class,
            /** @psalm-suppress MixedArgument */
            fn (): RunnerInterface => new Runner($this->container, $this->container->get(EngineInterface::class)),
        );
    }
}
