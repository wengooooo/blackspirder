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

namespace BlackSpider\Core;

use SebastianBergmann\Version as VersionID;

final class Version
{
    private static string $version = '1.0.0';

    public static function id(): string
    {
        return (new VersionID(self::$version, \dirname(__DIR__, 2)))->getVersion();
    }

    public static function getVersionString(): string
    {
        return 'BlackSpider PHP ' . self::id() . ' by Kai Sassnowski and contributors.';
    }
}
