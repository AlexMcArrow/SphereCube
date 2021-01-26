<?php

namespace Api;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint {
    /**
     * Read card
     * @param  string  $cid
     * @return array
     */
    public function CardRead( string $cid ) {
        return \Model\Card::ReadByID( $cid );
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
     * Search Object by $query string
     * @param  string  $query
     * @return array
     */
    public function Search( string $query ) {
        return \Model\Card::Search( $query );
    }
}
