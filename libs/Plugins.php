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
                                                    h.`plugin_name` AS `name`,
                                                    h.`plugin_type` AS `h_type`,
                                                    h.`plugin_class` AS `h_class`,
                                                    h.`plugin_desc` AS `h_desc`
                                                FROM
                                                    `plugin` h
                                                WHERE h.`active` = 1;" )->fetchAll( 'name' );
        } );
        foreach ( $pluginlist as $plugin_data ) {
            $plugin_data['h_class'] = 'Plugin\\' . $plugin_data['h_class'] . '\\Plugin';
            try {
                if ( method_exists( $plugin_data['h_class'], 'Register' ) ) {
                    call_user_func( $plugin_data['h_class'] . '::Register' );
                    self::$types[$plugin_data['h_type']] = $plugin_data;
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
     * EndPoint for plugin
     * @param  string                    $params
     * @return true|false|array|string
     */
    public static function endpoint( $params ) {
        //TODO: return static
        return $params;
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
     * Route plugin static
     * @param  string   $type
     * @param  string   $name
     * @return string
     */
    public static function route( $type, $name ) {
        //TODO: return static
        return $type . '_' . $name;
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
