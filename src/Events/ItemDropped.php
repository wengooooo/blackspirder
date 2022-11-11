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

namespace BlackSpider\Events;

use BlackSpider\ItemPipeline\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ItemDropped extends Event
{
    public const NAME = 'item.dropped';

    public function __construct(public ItemInterface $item)
    {
    }
}
