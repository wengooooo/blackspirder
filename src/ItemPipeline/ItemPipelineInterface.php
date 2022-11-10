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

namespace BlackSpider\ItemPipeline;

use BlackSpider\ItemPipeline\Processors\ItemProcessorInterface;

interface ItemPipelineInterface
{
    public function setProcessors(ItemProcessorInterface ...$processors): self;

    public function sendItem(ItemInterface $item): ItemInterface;
}
