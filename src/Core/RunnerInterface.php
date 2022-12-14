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
use BlackSpider\Spider\Configuration\Overrides;
use BlackSpider\Spider\SpiderInterface;

interface RunnerInterface
{
    /**
     * @param class-string<SpiderInterface> $spiderClass
     */
    public function startSpider(
        string $spiderClass,
        ?Overrides $overrides = null,
        array $context = [],
    ): void;

    /**
     * @param class-string<SpiderInterface> $spiderClass
     *
     * @return array<int, ItemInterface>
     */
    public function collectSpider(
        string $spiderClass,
        ?Overrides $overrides = null,
        array $context = [],
    ): array;
}
