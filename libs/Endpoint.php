<?php

use Plugins;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint {
    /**
     * Config
     * @return array
     */
    public function Config() {
        $plugindata = [
            'models' => [],
            'metas'  => [],
            'types'  => [],
            'files'  => []
        ];
        Plugins::calling( 'On', 'Config', $plugindata );
        return $plugindata;
    }

    /**
     * Check API enabled and get server params
     * @return array
     */
    public function Ping() {
        return [
            'time'    => time(),
            'domain'  => DOMAIN,
            'version' => STATICVERSION,
            'debug'   => DEBUG
        ];
    }

    /**
     * Search Cards by $query string
     * @param  string  $query
     * @return array
     */
    public function Search( string $query ) {
        $plugindata = [];
        Plugins::calling( 'Before', 'Search', $plugindata );
        $plugindata = array_merge( $plugindata, Plugins::Search( $query ) );
        Plugins::calling( 'After', 'Search', $plugindata );
        return $plugindata;
    }

    /**
     * Search fields by $query string
     * @param  string  $query
     * @return array
     */
    public function SearchField( string $query ) {
        $plugindata = [];
        Plugins::calling( 'Before', 'SearchField', $plugindata );
        $plugindata = array_merge( $plugindata, Plugins::SearchField( $query ) );
        Plugins::calling( 'After', 'SearchField', $plugindata );
        return $plugindata;
    }
}
