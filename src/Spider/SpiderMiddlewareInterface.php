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

namespace BlackSpider\Spider;

use BlackSpider\Spider\Middleware\ItemMiddlewareInterface;
use BlackSpider\Spider\Middleware\RequestMiddlewareInterface;
use BlackSpider\Spider\Middleware\ResponseMiddlewareInterface;

interface SpiderMiddlewareInterface extends ItemMiddlewareInterface, RequestMiddlewareInterface, ResponseMiddlewareInterface
{
}
