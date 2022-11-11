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
use BlackSpider\Support\ConfigurableInterface;

interface ResponseMiddlewareInterface extends ConfigurableInterface
{
    /**
     * Handles a response before the parse callback gets
     * invoked.
     */
    public function handleResponse(Response $response): Response;
}
