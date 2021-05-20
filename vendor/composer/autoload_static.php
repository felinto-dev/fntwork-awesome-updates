<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit05a60bde0cd587a276d9baa4d136f332
{
    public static $files = array (
        '689b08b7620712b04324ecd7ed167c6b' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p10.php',
    );

    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MOD\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MOD\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit05a60bde0cd587a276d9baa4d136f332::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit05a60bde0cd587a276d9baa4d136f332::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
