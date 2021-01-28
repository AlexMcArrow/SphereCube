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
     * Update Card
     * @param  string        $cid
     * @param  string        $value
     * @return array|false
     */
    public function CardUpdate( string $cid, string $value ) {
        \Model\Card::UpdateCard( $cid, $value );
        return $this->CardRead( $cid );
    }

    /**
     * Update Card field
     * @param  string        $cid
     * @param  string        $cfvid
     * @param  string        $cfid
     * @param  string        $value
     * @return array|false
     */
    public function CardUpdateField( string $cid, string $cfvid, string $cfid, string $value ) {
        \Model\Card::UpdateField( $cid, $cfvid, $cfid, $value );
        return $this->CardRead( $cid );
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
        return \Model\Card::Search( $query );
    }
}
