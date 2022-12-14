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

namespace BlackSpider\Core;

use BlackSpider\ItemPipeline\ItemInterface;

interface EngineInterface
{
    /**
     * Start a new run based on the configuration defined in $run.
     */
    public function start(Run $run): void;

    /**
     * Start a new run based on the configuration defined in $run and
     * return all scraped items at the end.
     *
     * @return array<int, ItemInterface>
     */
    public function collect(Run $run): array;
}
