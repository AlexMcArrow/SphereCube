<?php

class Plugins {
    /**
     * @var array
     */
    public static $types = [];

    /**
     * @var array
     */
    private static $plugins = [];

    /**
     * Search by $query string
     * @param  string  $query
     * @return array
     */
    public static function Search( string $query ) {
        $plugindata = [
            'query'  => $query,
            'result' => []
        ];
        Plugins::calling( 'On', 'Search', $plugindata );
        return $plugindata['result'];
    }

    /**
     * Search fields by $query string
     * @param  string  $query
     * @return array
     */
    public static function SearchField( string $query ) {
        $plugindata = [
            'query'  => $query,
            'result' => []
        ];
        Plugins::calling( 'On', 'SearchField', $plugindata );
        return $plugindata['result'];
    }

    public function __construct() {
        $pluginlist = Cache::readorwrite( 'PluginsList', 900, function () {
            return DB::getInstance()->query( "SELECT
                                                    p.`plugin_name` AS `name`,
                                                    p.`plugin_code` AS `code`,
                                                    p.`plugin_class` AS `class`,
                                                    p.`plugin_desc` AS `desc`
                                                FROM
                                                    `plugin` p
                                                WHERE  p.`active` = 1;" )->fetchAll( 'name' );
        } );
        foreach ( $pluginlist as $plugin_data ) {
            $plugin_data['class'] = 'Plugin\\' . $plugin_data['class'] . '\\Plugin';
            try {
                if ( method_exists( $plugin_data['class'], 'Register' ) ) {
                    call_user_func( $plugin_data['class'] . '::Register' );
                    self::$types[$plugin_data['code']] = $plugin_data;
                }
            } catch ( \Throwable $th ) {
                continue;
            }
        }
    }

    /**
     * Calling plugins
     * @param  string $when
     * @param  string $about
     * @param  array  $data
     * @return void
     */
    public static function calling( $when, $about, &$data ) {
        $calling = self::_get_calling_name( $when, $about );
        if ( isset( self::$plugins[$calling] ) ) {
            foreach ( self::$plugins[$calling] as $plugin ) {
                try {
                    call_user_func_array( $plugin, array( &$data ) );
                } catch ( \Throwable $th ) {
                    return;
                }
            }
        }
    }

    /**
     * Register plugins
     * @param  string $when
     * @param  string $about
     * @param  string $call
     * @return void
     */
    public static function register( $when, $about, $call ) {
        $calling = self::_get_calling_name( $when, $about );
        if ( !isset( self::$plugins[$calling] ) ) {
            self::$plugins[$calling] = [];
        }
        self::$plugins[$calling][] = $call;
    }

    /**
     * Register plugins
     * @param  string   $when
     * @param  string   $about
     * @return string
     */
    private static function _get_calling_name( $when, $about ) {
        return hash( 'sha256', strtolower( trim( $when ) ) . '.' . strtolower( trim( $about ) ) );
    }
}
