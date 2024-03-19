<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Kai Sassnowski
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/roach-php/roach
 */

namespace BlackSpider\Events;

use BlackSpider\Http\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class ResponseReceiving extends Event
{
    public const NAME = 'response.received';

    public function __construct(public Response $response)
    {
    }
}
