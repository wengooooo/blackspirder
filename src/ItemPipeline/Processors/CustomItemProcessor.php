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

namespace BlackSpider\ItemPipeline\Processors;

use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\Support\Configurable;

abstract class CustomItemProcessor implements ConditionalItemProcessor
{
    use Configurable;

    final public function shouldHandle(ItemInterface $item): bool
    {
        return \in_array($item::class, $this->getHandledItemClasses(), true);
    }

    /**
     * @return array<int, class-string<ItemInterface>>
     */
    abstract protected function getHandledItemClasses(): array;
}
