<?php

namespace Api;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint {
    /**
     * Read card
     * @param  string         $cid
     * @return array|object
     */
    public function CardRead( string $cid ) {
        $rawdata = \Model\Card::ReadByID( $cid );
        // Hook: Link
        \Hook\Link::Hook_Card_After_ReadByID( $rawdata );
        // Return
        return $rawdata;
    }

    /**
     * Check API enabled and get server params
     * @return array|object
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
     * Search Object by $query string
     * @param  string         $query
     * @return array|object
     */
    public function Search( string $query ) {
        return \Model\Card::Search( $query );
    }
}
