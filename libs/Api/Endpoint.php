<?php

namespace Api;

use Hook\Link;
use Model\Card;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint {
    /**
     * Create Card
     * @param  string        $value
     * @return array|false
     */
    public function CardCreate( string $value ) {
        $cid = (string) Card::InsertCard( $value );
        return $this->CardRead( $cid );
    }

    /**
     * Create Card
     * @param  string        $cid
     * @param  string        $cfid
     * @param  string        $value
     * @return array|false
     */
    public function CardCreateField( string $cid, string $cfid, string $value ) {
        Card::InsertField( $cid, $cfid, $value );
        return $this->CardRead( $cid );
    }

    /**
     * Delete Card
     * @param  string $cid
     * @return true
     */
    public function CardDelete( string $cid ) {
        return Card::DeleteCard( $cid );
    }

    /**
     * Delete Card
     * @param  string        $cid
     * @return array|false
     */
    public function CardDeleteField( string $cid, string $cfvid ) {
        Card::DeleteCardField( $cid, $cfvid );
        return $this->CardRead( $cid );
    }

    /**
     * Read card
     * @param  string        $cid
     * @return array|false
     */
    public function CardRead( string $cid ) {
        $metadata = Card::ReadByID( $cid );
        if ( key_exists( 'c_id', $metadata ) ) {
            $fieldsdata = Card::ReadFieldsByID( $cid );
            // Hooks
            // Link
            Link::Hook_Card_After_ReadFieldsByID( $metadata, $fieldsdata );

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
        Card::UpdateCard( $cid, $value );
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
        Card::UpdateField( $cid, $cfvid, $cfid, $value );
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
        return Card::Search( $query );
    }
}
