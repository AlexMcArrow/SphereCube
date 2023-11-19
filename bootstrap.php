<?php

use SphereCube\DB;
use SphereCube\Cache;
use SphereCube\Plugins;

// Define COREPATH
define('COREPATH', __DIR__);

/**
 * Build path to folder
 * @param  string   $_
 * @return string
 */
function buildpath(): string
{
    return implode(DIRECTORY_SEPARATOR, func_get_args());
}

/**
 * Generate UUID
 * @return string
 */
function gen_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// check autoload.php and config.php
if (!file_exists(buildpath(COREPATH, 'vendor', 'autoload.php'))) {
    die('run <code>composer install</code>');
}
if (!file_exists(buildpath(COREPATH, 'config.php'))) {
    die('<code>config.php</code> not exist');
}

// Require Composer autoload
require buildpath(COREPATH, 'vendor', 'autoload.php');

// Require config
require buildpath(COREPATH, 'config.php');

if (!file_exists(buildpath(COREPATH, 'version'))) {
    file_put_contents(buildpath(COREPATH, 'version'), strval(time()));
}
define('STATICVERSION', file_get_contents(buildpath(COREPATH, 'version')));

// Initilize DB
new DB(DB_CONNECT['base'], DB_CONNECT['host'], DB_CONNECT['user'], DB_CONNECT['pass'], DB_CONNECT['port'], DB_CONNECT['scheme']);

// Initilize Cache
new Cache();

// Initilize Plugins
new Plugins(PLUGIN_CACHE_TTL);
