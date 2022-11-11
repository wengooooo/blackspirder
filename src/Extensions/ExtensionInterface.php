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

namespace BlackSpider\Extensions;

use BlackSpider\Support\ConfigurableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface ExtensionInterface extends ConfigurableInterface, EventSubscriberInterface
{
}
