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

interface ConditionalItemProcessor extends ItemProcessorInterface
{
    /**
     * Check if the yielded item should get handled by this item processor.
     */
    public function shouldHandle(ItemInterface $item): bool;
}
