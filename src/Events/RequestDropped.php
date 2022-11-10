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

namespace BlackSpider\Events;

use BlackSpider\Http\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class RequestDropped extends Event
{
    public const NAME = 'request.dropped';

    public function __construct(public Request $request)
    {
    }
}
