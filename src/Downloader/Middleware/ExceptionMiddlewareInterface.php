<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 Pavlo Komarov
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/wengooooo/blackspirder
 */

namespace BlackSpider\Downloader\Middleware;

use BlackSpider\Exception\Exception;
use BlackSpider\Support\ConfigurableInterface;

interface ExceptionMiddlewareInterface extends ConfigurableInterface
{
    public function handleException(Exception $exception): Exception;
}