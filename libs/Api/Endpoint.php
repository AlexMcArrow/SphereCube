<?php

namespace Api;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint {
    /**
     * Read card
     * @param  string        $cid
     * @return array|false
     */
    public function CardRead( string $cid ) {
        $metadata = \Model\Card::ReadByID( $cid );
        if ( key_exists( 'c_id', $metadata ) ) {
            $fieldsdata = \Model\Card::ReadFieldsByID( $cid );
            // Hooks
            // Link
            \Hook\Link::Hook_Card_After_ReadFieldsByID( $metadata, $fieldsdata );

            // Return
            return [
                'meta'   => $metadata,
                'fields' => $fieldsdata
            ];
        }
        return false;
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
