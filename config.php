<?php

define( 'DOMAIN', 'spherecube.local' );
define( 'APP_PATH', '/' );
define( 'DEBUG', true );
if ( DEBUG ) {
    define( 'STATICVERSION', time() );
} else {
    define( 'STATICVERSION', '20210126' );
}

define( 'DB_CONNECT', [
    'host' => 'localhost',
    'port' => 3306,
    'user' => 'mysql',
    'pass' => 'mysql',
    'name' => 'spherecube',
    'char' => 'utf8'
] );

define( 'JWT_ISSUE', DOMAIN );
define( 'JWT_KEY', '4396f7ed7cfebe1129b183dd7cfabca9fd47f99fe076d8397c74a0f3376402ae' );
define( 'JWT_HEADER', 'X-JWT-Token' );
