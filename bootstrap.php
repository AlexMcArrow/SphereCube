<?php

// Define COREPATH
define('COREPATH', __DIR__);

/**
 * AutoloadClassNotFound
 */
class AutoloadClassNotFound extends Exception
{
}

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

/**
 * Local class autoloader
 * @param  string $class_name
 * @return void
 */
function autoloadclass(string $class_name): void
{
    if (file_exists(buildpath(COREPATH, 'libs', str_ireplace('\\', DIRECTORY_SEPARATOR, $class_name)) . '.php')) {
        require_once buildpath(COREPATH, 'libs', str_ireplace('\\', DIRECTORY_SEPARATOR, $class_name)) . '.php';
    } elseif (file_exists(buildpath(COREPATH, 'Plugin', str_ireplace('\\', DIRECTORY_SEPARATOR, str_ireplace('Plugin\\', '', $class_name))) . '.php')) {
        require_once buildpath(COREPATH, 'Plugin', str_ireplace('\\', DIRECTORY_SEPARATOR, str_ireplace('Plugin\\', '', $class_name))) . '.php';
    }
}

// Register local class autoloader
spl_autoload_register('autoloadclass');

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

// Initilize DB
$DB = new DB();
// DEBUG variation
if (DEBUG === true) {
    $DB::show_errors(true);
} else {
    $DB::show_errors(false);
}

if (!file_exists(buildpath(COREPATH, 'version'))) {
    file_put_contents(buildpath(COREPATH, 'version'), time());
}
define('STATICVERSION', file_get_contents(buildpath(COREPATH, 'version')));

// DB open connection
$DB::getInstance()->connect(DB_CONNECT['host'], DB_CONNECT['port'], DB_CONNECT['user'], DB_CONNECT['pass'], DB_CONNECT['name'], DB_CONNECT['char']);

// Initilize Cache
$Cache = new Cache();

// Initilize Plugins
$Plugins = new Plugins();
