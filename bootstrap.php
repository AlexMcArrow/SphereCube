<?php

// Define COREPATH
define( 'COREPATH', __DIR__ );

/**
 * AutoloadClassNotFound
 */
class AutoloadClassNotFound extends Exception {
}

/**
 * Build path to folder
 * @param  string   $_
 * @return string
 */
function buildpath(): string {
    return implode( DIRECTORY_SEPARATOR, func_get_args() );
}

/**
 * Local class autoloader
 * @param  string $class_name
 * @return void
 */
function autoloadclass( string $class_name ): void {
    if ( file_exists( buildpath( COREPATH, 'libs', str_ireplace( '_', DIRECTORY_SEPARATOR, $class_name ) ) . '.php' ) ) {
        require_once buildpath( COREPATH, 'libs', str_ireplace( '_', DIRECTORY_SEPARATOR, $class_name ) ) . '.php';
    } else {
        throw new AutoloadClassNotFound( "Class $class_name not found" );
    }
}

// Register local class autoloader
spl_autoload_register( 'autoloadclass' );

// Require Composer autoload
require buildpath( COREPATH, 'vendor', 'autoload.php' );

// Require config
require buildpath( COREPATH, 'config.php' );
