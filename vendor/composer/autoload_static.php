<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc3905b0ee4eebc9bf5d1e938fa88ff15
{
    public static $files = array (
        '3ebad73468414805a4c86071a39c0490' => __DIR__ . '/../..' . '/src/app/CommonHelper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stegback\\Ratepay\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stegback\\Ratepay\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitc3905b0ee4eebc9bf5d1e938fa88ff15::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc3905b0ee4eebc9bf5d1e938fa88ff15::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc3905b0ee4eebc9bf5d1e938fa88ff15::$classMap;

        }, null, ClassLoader::class);
    }
}
