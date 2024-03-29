<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit826ea648cee854c1b17ae7b5c34ed19d
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GetepayCF7\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GetepayCF7\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit826ea648cee854c1b17ae7b5c34ed19d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit826ea648cee854c1b17ae7b5c34ed19d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit826ea648cee854c1b17ae7b5c34ed19d::$classMap;

        }, null, ClassLoader::class);
    }
}
