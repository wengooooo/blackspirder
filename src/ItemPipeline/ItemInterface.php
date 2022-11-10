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

use ArrayAccess;
use BlackSpider\Support\DroppableInterface;

/**
 * @extends ArrayAccess<string, mixed>
 */
interface ItemInterface extends ArrayAccess, DroppableInterface
{
    public function all(): array;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): self;

    public function has(string $key): bool;
}
