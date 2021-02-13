<?php

// Require bootstrap
require '../bootstrap.php';

// Init Klein router
$klein = new \Klein\Klein();

// Routing for index page
$klein->respond( 'GET', '/', function (): string {
    Rain\Tpl::configure( [
        "tpl_dir"   => buildpath( COREPATH, 'static', 'tpl' . DIRECTORY_SEPARATOR ),
        "cache_dir" => buildpath( COREPATH, 'cache' . DIRECTORY_SEPARATOR ),
        "debug"     => DEBUG
    ] );
    $t = new Rain\Tpl;
    return (string) $t->draw( 'main', true );
} );

// Routing for APi`s
$klein->respond( 'POST', '/api', function () {
    $server = new JsonRPC\Server();
    $server->getProcedureHandler()
        ->withObject( new Api\Endpoint() );
    return $server->execute();
} );

// Klein routing dispatch
$klein->dispatch();
