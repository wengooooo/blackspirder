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

use BlackSpider\Scheduling\RedisRequestScheduler;
use BlackSpider\Scheduling\SchedulerInterface;
use gfaugere\Monolog\Formatter\ColoredLineFormatter;
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
            static function  () {
                $format = "[%datetime%] %color_start%%channel%.%level_name%: %message%%color_end% %context% %extra%\n";
                $scheme = [
                    Logger::DEBUG     => "\033[38;5;206m",
                    Logger::INFO      => "\033[38;5;34m",
                    Logger::NOTICE    => "\033[38;5;202m",
                    Logger::WARNING   => "\033[38;5;226m",
                    Logger::ERROR     => "\033[38;5;196m",
                    Logger::CRITICAL  => "\033[38;5;81m",
                    Logger::ALERT     => "\033[38;5;53m",
                    Logger::EMERGENCY => "\033[38;5;129m"
                ];
                $formatter = new ColoredLineFormatter($format, null, false, false, $scheme);
                $stream = new StreamHandler('php://stdout', Logger::DEBUG);
                $stream->setFormatter($formatter);
                return (new Logger('blackspider'))->pushHandler($stream);
            },
//            static fn () => (new Logger('blackspider'))->pushHandler(new StreamHandler('php://stdout')),
        );
        $this->container->addShared(EventDispatcher::class, EventDispatcher::class);
        $this->container->addShared(EventDispatcherInterface::class, EventDispatcher::class);
        $this->container->add(ClockInterface::class, SystemClock::class);
//        $this->container->addShared(ArrayIteratorRequestScheduler::class, ArrayIteratorRequestScheduler::class);
//        $this->container->addShared(SchedulerInterface::class, ArrayIteratorRequestScheduler::class);
//        $this->container->addShared(SchedulerInterface::class, RedisRequestScheduler::class);

        $type = 2;
        $this->container->addShared(
            SchedulerInterface::class,
            /** @psalm-suppress MixedReturnStatement, MixedInferredReturnType */
//            fn (): SchedulerInterface => $this->container->get(ArrayIteratorRequestScheduler::class)
            function() use($type) {
                if($type == 1) {
                    return $this->container->get(RedisRequestScheduler::class);

                } else {
                    return $this->container->get(ArrayIteratorRequestScheduler::class);
                }
            }
        );

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
