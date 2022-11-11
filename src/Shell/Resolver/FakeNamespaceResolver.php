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

namespace BlackSpider\Shell\Resolver;

/**
 * @internal
 */
final class FakeNamespaceResolver implements NamespaceResolverInterface
{
    /**
     * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
     */
    public function resolveSpiderNamespace(string $spiderClass): string
    {
        return $spiderClass;
    }
}
