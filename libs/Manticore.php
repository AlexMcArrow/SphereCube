<?php

class Manticore {
    /**
     * @var mixed
     */
    protected static $connection;

    /**
     * @var mixed
     */
    protected static $prefname;

    /**
     * @var mixed
     */
    private static $_instance = null;

    /**
     * @return void
     */
    function __construct() {}
    /**
     * @param  string $host
     * @param  int    $port
     * @param  string $prefname
     * @return void
     */
    public function connect( $host, $port, $prefname ) {
        self::$prefname   = $prefname;
        self::$connection = new \Manticoresearch\Client( [
            'host' => $host,
            'port' => $port
        ] );
    }

    /**
     * @return object
     */
    public function getConnection() {
        return self::$connection;
    }

    /**
     * @param  string   $name
     * @return object
     */
    public function getIndex( $name ) {
        return self::$connection->index( self::$prefname . $name );
    }

    /**
     * Return instance for chain style
     * @return Manticore
     */
    public static function getInstance() {
        if ( self::$_instance === null ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}
