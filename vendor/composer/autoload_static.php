<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit13d485ec6b05c752dc3e38bd361343cc
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Lisab\\Blackspirder\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Lisab\\Blackspirder\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit13d485ec6b05c752dc3e38bd361343cc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit13d485ec6b05c752dc3e38bd361343cc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit13d485ec6b05c752dc3e38bd361343cc::$classMap;

        }, null, ClassLoader::class);
    }
}
