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

// Routing to plugins static
$klein->respond( 'GET', '/plugin/[*:plugin]/[*:file]', function ( $req ): string {
    $filepath = buildpath( COREPATH, 'Plugin', $req->plugin, $req->file );
    if ( file_exists( $filepath ) ) {
        return file_get_contents( $filepath );
    }
    return false;
} );

// Routing for plugins APi`s
$klein->respond( 'POST', '/model/api/[*:model]', function ( $req ) {
    $filepath = buildpath( COREPATH, 'Plugin', ucwords( $req->model ), 'Endpoint.php' );
    if ( file_exists( $filepath ) ) {
        $class  = 'Plugin\\' . ucwords( $req->model ) . '\\Endpoint';
        $server = new JsonRPC\Server();
        $server->getProcedureHandler()
            ->withObject( new $class() );
        return $server->execute();
    } else {
        return false;
    }
} );

// Routing for Core API
$klein->respond( 'POST', '/api', function () {
    $server = new JsonRPC\Server();
    $server->getProcedureHandler()
        ->withObject( new Endpoint() );
    return $server->execute();
} );

// Klein routing dispatch
$klein->dispatch();
