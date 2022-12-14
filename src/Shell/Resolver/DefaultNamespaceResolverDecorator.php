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

use BlackSpider\Shell\InvalidSpiderException;
use BlackSpider\Spider\SpiderInterface;

final class DefaultNamespaceResolverDecorator implements NamespaceResolverInterface
{
    private string $defaultNamespace;

    public function __construct(
        private NamespaceResolverInterface $wrapped,
        string $defaultNamespace,
    ) {
        $this->defaultNamespace = \trim($defaultNamespace, " \t\n\r\0\x0B\\");
    }

    /**
     * @throws InvalidSpiderException
     *
     * @return class-string<SpiderInterface>
     */
    public function resolveSpiderNamespace(string $spiderClass): string
    {
        $spiderClass = \trim($spiderClass);

        if (
            \str_starts_with($spiderClass, '\\')
            || \str_starts_with($spiderClass, $this->defaultNamespace)
            || \class_exists($spiderClass)
        ) {
            return $this->wrapped->resolveSpiderNamespace($spiderClass);
        }

        return $this->wrapped->resolveSpiderNamespace($this->defaultNamespace . '\\' . $spiderClass);
    }
}
