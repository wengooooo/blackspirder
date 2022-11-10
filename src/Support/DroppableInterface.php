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

namespace BlackSpider\Support;

interface DroppableInterface
{
    public function drop(string $reason): static;

    public function wasDropped(): bool;

    public function getDropReason(): string;
}
