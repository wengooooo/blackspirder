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

namespace BlackSpider\Spider\Middleware;

use BlackSpider\Http\Response;
use BlackSpider\ItemPipeline\ItemInterface;
use BlackSpider\Support\ConfigurableInterface;

interface ItemMiddlewareInterface extends ConfigurableInterface
{
    /**
     * Handles an item that got emitted while parsing $response.
     */
    public function handleItem(
        ItemInterface $item,
        Response $response,
    ): ItemInterface;
}
