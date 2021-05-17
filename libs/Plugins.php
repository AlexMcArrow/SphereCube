<?php

class Plugins {
    /**
     * @var array
     */
    public static $types = [];

    /**
     * @var string
     */
    private static $cache_key = 'PluginsList';

    /**
     * @var int
     */
    private static $cache_ttl = 900;

    /**
     * @var array
     */
    private static $plugins = [];

    /**
     * Build config array
     * @return array
     */
    public static function Config() {
        $plugindata = [
            'models' => [],
            'metas'  => [],
            'types'  => [],
            'files'  => []
        ];
        self::calling( 'On', 'PluginsConfig', $plugindata );
        return $plugindata;
    }

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
        Plugins::calling( 'On', 'PluginsSearch', $plugindata );
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
        Plugins::calling( 'On', 'PluginsSearchField', $plugindata );
        return $plugindata['result'];
    }

    public function __construct() {
        $pluginlist = Cache::readorwrite( self::$cache_key, self::$cache_ttl, function (): array{
            return self::_read_plugin_list();
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
     * Recache plugin list
     * @return void
     */
    public static function plugin_list_recache() {
        $pluginlist = self::_read_plugin_list();
        Cache::write( self::$cache_key, $pluginlist, self::$cache_ttl );
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

    /**
     * Read plugin list
     * @return array
     */
    private static function _read_plugin_list() {
        return DB::getInstance()->query( "SELECT
                                                p.`plugin_name` AS `name`,
                                                p.`plugin_code` AS `code`,
                                                p.`plugin_class` AS `class`,
                                                p.`plugin_desc` AS `desc`
                                            FROM
                                                `plugin` p
                                            WHERE  p.`active` = 1;" )->fetchAll( 'name' );
    }
}
