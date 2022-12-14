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

use BlackSpider\Events\RequestScheduling;
use BlackSpider\Events\RequestSending;
use BlackSpider\Support\Configurable;

final class MaxRequestExtension implements ExtensionInterface
{
    use Configurable;

    private int $sentRequests = 0;

    public static function getSubscribedEvents(): array
    {
        return [
            RequestSending::NAME => ['onRequestSending', 10000],
            RequestScheduling::NAME => ['onRequestScheduling', 0],
        ];
    }

    public function onRequestSending(RequestSending $event): void
    {
        $this->dropRequestIfLimitReached($event);

        if (!$event->request->wasDropped()) {
            ++$this->sentRequests;
        }
    }

    public function onRequestScheduling(RequestScheduling $event): void
    {
        $this->dropRequestIfLimitReached($event);
    }

    private function dropRequestIfLimitReached(RequestSending|RequestScheduling $event): void
    {
        if ($this->option('limit') <= $this->sentRequests) {
            $event->request = $event->request->drop("Reached maximum request limit of {$this->option('limit')}");
        }
    }

    private function defaultOptions(): array
    {
        return [
            'limit' => 10,
        ];
    }
}
