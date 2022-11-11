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
use BlackSpider\Events\ItemDropped;
use BlackSpider\Events\RequestDropped;
use BlackSpider\Events\ResponseDropped;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\ItemPipeline\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Processor
{
    /**
     * @var SpiderMiddlewareInterface[]
     */
    private array $middleware = [];

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function withMiddleware(SpiderMiddlewareInterface ...$middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function handle(Response $response): Generator
    {
        foreach ($this->middleware as $handler) {
            $response = $handler->handleResponse($response);

            if ($response->wasDropped()) {
                $this->eventDispatcher->dispatch(
                    new ResponseDropped($response),
                    ResponseDropped::NAME,
                );

                return;
            }
        }

        /** @var ParseResult[] $results */
        $results = $response->getRequest()->callback($response);

        foreach ($results as $result) {
            $value = $result->value();
            $handleMethod = $value instanceof Request
                ? 'handleRequest'
                : 'handleItem';

            foreach ($this->middleware as $handler) {
                /** @var ItemInterface|Request $value */
                $value = $handler->{$handleMethod}($value, $response);

                if ($value->wasDropped()) {
                    if ($value instanceof Request) {
                        $this->eventDispatcher->dispatch(
                            new RequestDropped($value),
                            RequestDropped::NAME,
                        );
                    } else {
                        $this->eventDispatcher->dispatch(
                            new ItemDropped($value),
                            ItemDropped::NAME,
                        );
                    }

                    break;
                }
            }

            if (!$value->wasDropped()) {
                yield ParseResult::fromValue($value);
            }
        }
    }
}
