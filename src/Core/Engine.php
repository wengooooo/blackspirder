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

namespace BlackSpider\Core;

use BlackSpider\Downloader\Downloader;
use BlackSpider\Events\RequestDropped;
use BlackSpider\Events\RequestScheduling;
use BlackSpider\Events\RunFinished;
use BlackSpider\Events\RunStarting;
use BlackSpider\Extensions\ScrapedItemCollectorExtension;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\ItemPipeline\ItemPipelineInterface;
use BlackSpider\Scheduling\ArrayIteratorRequestScheduler;
use BlackSpider\Spider\ParseResult;
use BlackSpider\Spider\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Engine implements EngineInterface
{
    public function __construct(
        private ArrayIteratorRequestScheduler $scheduler,
        private Downloader $downloader,
        private ItemPipelineInterface $itemPipeline,
        private Processor $responseProcessor,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<int, ItemInterface>
     */
    public function collect(Run $run): array
    {
        $extension = new ScrapedItemCollectorExtension();
        $this->eventDispatcher->addSubscriber($extension);

        $this->start($run);

        return $extension->getScrapedItems();
    }

    public function start(Run $run): void
    {
        $this->configure($run);

        $this->eventDispatcher->dispatch(
            new RunStarting($run),
            RunStarting::NAME,
        );

        foreach ($run->startRequests as $request) {
            $this->scheduleRequest($request);
        }

        $this->work($run);
    }

    private function work(Run $run): void
    {

        if(!$this->scheduler->empty()) {
            $this->downloader->execute(
                fn (Response $response) => $this->onFulfilled($response),
            );
        }

        $this->eventDispatcher->dispatch(
            new RunFinished($run),
            RunFinished::NAME,
        );
    }

    private function onFulfilled(Response $response): void
    {
        /** @var ParseResult[] $parseResults */
        $parseResults = $this->responseProcessor->handle($response);

        foreach ($parseResults as $result) {
            $result->apply(
                fn (Request $request) => $this->scheduleRequest($request),
                fn (ItemInterface $item) => $this->itemPipeline->sendItem($item),
            );
        }
    }

    private function scheduleRequest(Request $request): void
    {
        $this->eventDispatcher->dispatch(
            new RequestScheduling($request),
            RequestScheduling::NAME,
        );

        if ($request->wasDropped()) {
            $this->eventDispatcher->dispatch(
                new RequestDropped($request),
                RequestDropped::NAME,
            );

            return;
        }

        $this->scheduler->schedule($request);
    }

    private function configure(Run $run): void
    {
        $this->itemPipeline->setProcessors(...$run->itemProcessors);
        $this->downloader->withMiddleware(...$run->downloaderMiddleware);
        $this->responseProcessor->withMiddleware(...$run->responseMiddleware);

        foreach ($run->extensions as $extension) {
            $this->eventDispatcher->addSubscriber($extension);
        }
    }
}
