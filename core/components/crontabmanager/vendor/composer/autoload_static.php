<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5fdbc512eb551f61b9ce2841e1ba1476
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Webnitros\\CronTabManager\\' => 25,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'L' => 
        array (
            'League\\OAuth2\\Client\\' => 21,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
            'GetPhoto\\Oauth2\\Client\\' => 23,
        ),
        'D' => 
        array (
            'Desarrolla2\\Test\\Cache\\' => 23,
            'Desarrolla2\\Cache\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Webnitros\\CronTabManager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
            1 => __DIR__ . '/..' . '/psr/http-factory/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'League\\OAuth2\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/oauth2-client/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'GetPhoto\\Oauth2\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/fotografde/oauth-client/src',
        ),
        'Desarrolla2\\Test\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/desarrolla2/cache/test',
        ),
        'Desarrolla2\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/desarrolla2/cache/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5fdbc512eb551f61b9ce2841e1ba1476::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5fdbc512eb551f61b9ce2841e1ba1476::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5fdbc512eb551f61b9ce2841e1ba1476::$classMap;

        }, null, ClassLoader::class);
    }
}
