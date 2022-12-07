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

use BlackSpider\Events\RequestRetry;
use BlackSpider\Events\ResponseReceive;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Predis\Response\ServerException;
use Psr\Log\LoggerInterface;
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
            ResponseReceive::NAME => ['onResponseReceive', 100],
            RequestDropped::NAME => ['onRequestDropped', 100],
            ItemScraped::NAME => ['onItemScraped', 100],
            ItemDropped::NAME => ['onItemDropped', 100],
            RequestRetry::NAME => ['onRequestRetry', 100],
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
//        $this->logger->info('Dispatching request', [
//            'uri' => $event->request->getUri(),
//        ]);
    }

    public function onResponseReceive(ResponseReceive $event): void
    {

        $context = [];
        if ($event->response->getRequest()->getMethod() == 'POST') {
            $options = $event->response->getRequest()->getOptions();
            if (isset($options['body'])) {
                $context = is_array($options['body']) ? $options['body'] : [$options['body']];
            }

            if(isset($options['form_params'])) {
                $context = is_array($options['form_params']) ? $options['form_params'] : [$options['form_params']];
            }
        }

        $this->logger->info(sprintf('Crawled (%s) <%s %s>', $event->response->getStatus(), $event->response->getRequest()->getMethod(), $event->response->getUri()), $context);
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

    public function onRequestRetry(RequestRetry $event): void
    {
        $request = $event->request;
        $response = $event->response;
        $reason = $event->reason;
        $retryCount = $request->getMeta('retry_count', 0);
        $maxRetry = $request->getMeta('max_retry_attempts');
        if($retryCount > $maxRetry) {
            $this->logger->error(sprintf('Give Up <%s %s>', $request->getMethod(), $request->getUri()), [$reason->getMessage()]);
        } else if($response == null) {
            $this->logger->info(sprintf('Retry <%s %s>', $request->getMethod(), $request->getUri()), [$reason->getMessage()]);
        } else {
            $this->logger->info(sprintf('Retry (%s) <%s %s>', $response->getStatus(), $request->getMethod(), $request->getUri()), []);
        }

    }
}
