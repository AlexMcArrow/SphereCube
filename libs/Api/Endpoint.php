<?php

namespace Api;

use Hooks;
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
        $cid = Card::InsertCard( $value );
        return $this->CardRead( $cid );
    }

    /**
     * Create Field type
     * @param  string   $value
     * @param  string   $type
     * @return string
     */
    public function CardCreateField( string $value, string $type ) {
        return Card::InsertField( $value, $type );
    }

    /**
     * Create Card
     * @param  string        $cid
     * @param  string        $cfid
     * @param  string        $value
     * @return array|false
     */
    public function CardCreateFieldValue( string $cid, string $cfid, string $value ) {
        Card::InsertFieldValue( $cid, $cfid, $value );
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
    public function CardDeleteFieldValue( string $cid, string $cfvid ) {
        Card::DeleteCardFieldValue( $cid, $cfvid );
        return $this->CardRead( $cid );
    }

    /**
     * Read card
     * @param  string        $cid
     * @return array|false
     */
    public function CardRead( string $cid ) {
        $hookdata = [
            'meta'   => [],
            'fields' => []
        ];
        Hooks::calling( 'Before', 'ReadByID', $hookdata );
        $hookdata = array_merge( $hookdata, ['meta' => Card::ReadByID( $cid )] );
        Hooks::calling( 'After', 'ReadByID', $hookdata );
        if ( key_exists( 'cid', $hookdata['meta'] ) ) {
            Hooks::calling( 'Before', 'CardRead', $hookdata );
            $hookdata = array_merge( $hookdata, ['fields' => Card::ReadFieldsByID( $cid )] );
            Hooks::calling( 'After', 'CardRead', $hookdata );
            return $hookdata;
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
    public function CardUpdateFieldValue( string $cid, string $cfvid, string $cfid, string $value ) {
        Card::UpdateField( $cid, $cfvid, $cfid, $value );
        return $this->CardRead( $cid );
    }

    /**
     * Config
     * @return array
     */
    public function Config() {
        $hookdata = [
            'models' => [],
            'metas'  => [],
            'types'  => []
        ];
        Hooks::calling( 'On', 'Config', $hookdata );
        return $hookdata;
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
        $hookdata = [];
        Hooks::calling( 'Before', 'Search', $hookdata );
        $hookdata = array_merge( $hookdata, Card::Search( $query ) );
        Hooks::calling( 'After', 'Search', $hookdata );
        return $hookdata;
    }

    /**
     * Search fields by $query string
     * @param  string  $query
     * @return array
     */
    public function SearchField( string $query ) {
        $hookdata = [];
        Hooks::calling( 'Before', 'SearchField', $hookdata );
        $hookdata = array_merge( $hookdata, Card::SearchField( $query ) );
        Hooks::calling( 'After', 'SearchField', $hookdata );
        return $hookdata;
    }
}
