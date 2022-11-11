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

namespace BlackSpider\Extensions;

use Psr\Log\LoggerInterface;
use BlackSpider\Events\Exception;
use BlackSpider\Events\ItemDropped;
use BlackSpider\Events\ItemScraped;
use BlackSpider\Events\RequestDropped;
use BlackSpider\Events\RequestSending;
use BlackSpider\Events\RunFinished;
use BlackSpider\Events\RunStarting;
use BlackSpider\Support\Configurable;

final class LoggerExtension implements ExtensionInterface
{
    use Configurable;

    public function __construct(private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RunStarting::NAME => ['onRunStarting', 100],
            RunFinished::NAME => ['onRunFinished', 100],
            RequestSending::NAME => ['onRequestSending', 100],
            RequestDropped::NAME => ['onRequestDropped', 100],
            ItemScraped::NAME => ['onItemScraped', 100],
            ItemDropped::NAME => ['onItemDropped', 100],
            Exception::NAME => ['onException', 100],
        ];
    }

    public function onRunStarting(RunStarting $event): void
    {
        $this->logger->info('Run starting');
    }

    public function onRunFinished(RunFinished $event): void
    {
        $this->logger->info('Run finished');
    }

    public function onRequestSending(RequestSending $event): void
    {
        $this->logger->info('Dispatching request', [
            'uri' => $event->request->getUri(),
        ]);
    }

    public function onRequestDropped(RequestDropped $event): void
    {
        $request = $event->request;

        $this->logger->info('Request dropped', [
            'uri' => $request->getUri(),
            'reason' => $request->getDropReason(),
        ]);
    }

    public function onItemScraped(ItemScraped $event): void
    {
        $this->logger->info('Item scraped', $event->item->all());
    }

    public function onItemDropped(ItemDropped $event): void
    {
        $this->logger->info('Item dropped', [
            'item' => $event->item->all(),
            'reason' => $event->item->getDropReason(),
        ]);
    }

    public function onException(Exception $event): void
    {
        $this->logger->info('Exception', [
            'url' => $event->request->getUri(),
            'class' => get_class($event->reason),
            'reason' => $event->reason->getMessage()
        ]);
    }
}
