<?php

class Hooks {
    /**
     * @var array
     */
    public static $types = [];

    /**
     * @var array
     */
    private static $hooks = [];

    public function __construct() {
        $hooklist = Cache::readorwrite( 'HooksConstruct', 1200, function () {
            return DB::getInstance()->query( "SELECT
                                                    h.`hook_name` AS `name`,
                                                    h.`hook_type` AS `h_type`,
                                                    h.`hook_class` AS `h_class`,
                                                    h.`hook_desc` AS `h_desc`
                                                FROM
                                                    `hook` h
                                                WHERE h.`active` = 1;" )->fetchAll( 'name' );
        } );
        foreach ( $hooklist as $hook_data ) {
            try {
                if ( method_exists( $hook_data['h_class'], 'Register' ) ) {
                    call_user_func( $hook_data['h_class'] . '::Register' );
                    self::$types[$hook_data['h_type']] = $hook_data;
                }
            } catch ( \Throwable $th ) {
                continue;
            }
        }
    }

    /**
     * Calling hooks
     * @param  string $when
     * @param  string $about
     * @param  array  $data
     * @return void
     */
    public static function calling( $when, $about, &$data ) {
        $calling = self::_get_calling_name( $when, $about );
        if ( isset( self::$hooks[$calling] ) ) {
            foreach ( self::$hooks[$calling] as $hook ) {
                try {
                    call_user_func_array( $hook, array( &$data ) );
                } catch ( \Throwable $th ) {
                    return;
                }
            }
        }
    }

    /**
     * Register hooks
     * @param  string $when
     * @param  string $about
     * @param  string $call
     * @return void
     */
    public static function register( $when, $about, $call ) {
        $calling = self::_get_calling_name( $when, $about );
        if ( !isset( self::$hooks[$calling] ) ) {
            self::$hooks[$calling] = [];
        }
        self::$hooks[$calling][] = $call;
    }

    /**
     * Register hooks
     * @param  string   $when
     * @param  string   $about
     * @return string
     */
    private static function _get_calling_name( $when, $about ) {
        return hash( 'sha256', strtolower( trim( $when ) ) . '.' . strtolower( trim( $about ) ) );
    }
}
