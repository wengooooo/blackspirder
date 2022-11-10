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

namespace BlackSpider\Shell\Resolver;

use BlackSpider\Shell\InvalidSpiderException;
use BlackSpider\Spider\SpiderInterface;

interface NamespaceResolverInterface
{
    /**
     * @throws InvalidSpiderException Thrown if the provided class does not exist
     * @throws InvalidSpiderException thrown if the provided class does not implement SpiderInterface
     *
     * @return class-string<SpiderInterface>
     */
    public function resolveSpiderNamespace(string $spiderClass): string;
}
