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

namespace BlackSpider\Extensions;

use BlackSpider\Events\ItemScraped;
use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\Support\Configurable;

/**
 * @internal
 */
final class ScrapedItemCollectorExtension implements ExtensionInterface
{
    use Configurable;

    /**
     * @var array<int, ItemInterface>
     */
    private array $scrapedItems = [];

    public static function getSubscribedEvents(): array
    {
        return [
            ItemScraped::NAME => ['onItemScraped', 0],
        ];
    }

    public function onItemScraped(ItemScraped $event): void
    {
        $this->scrapedItems[] = $event->item;
    }

    /**
     * @return array<int, ItemInterface>
     */
    public function getScrapedItems(): array
    {
        return $this->scrapedItems;
    }
}
