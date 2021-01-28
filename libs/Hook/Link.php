<?php

namespace Hook;

use DB;

class Link {
    /**
     * @var array
     */
    private static $name_ids = [];

    /**
     * Hook for Card After ReadByID
     * @param  array  $metadata
     * @param  array  $fieldsdata
     * @return void
     */
    public static function Hook_Card_After_ReadFieldsByID( &$metadata, &$fieldsdata ) {
        self::$name_ids = [];
        foreach ( $fieldsdata as $cardfield ) {
            self::NeedName( $cardfield['cfv_value'], $cardfield['c_id'], $cardfield['cfv_id'] );
        }
        self::GetData( $metadata, $fieldsdata );
    }

    /**
     * Get data from storage
     * @param  array  $metadata
     * @param  array  $fieldsdata
     * @return void
     */
    private static function GetData( &$metadata, &$fieldsdata ) {
        // Get form storage linked cards
        $links = DB::getInstance()
            ->query( "SELECT
                            `card_id` AS `c_id`,
                            `card_name` AS `c_name`
                        FROM
                            card
                        WHERE `card_id` IN ('" . implode( "','", array_keys( self::$name_ids ) ) . "');" )
            ->fetchAll( 'c_id' );
        $c_ids = [];
        array_walk_recursive( self::$name_ids, function ( string $v, string $k ) use ( &$c_ids ): void {
            if ( $k == 'c_id' ) {$c_ids[$v] = $v;}
        } );
        // Get form storage parents for cards
        $parent = DB::getInstance()
            ->query( "SELECT
                            cfv.`value` AS `child_id`,
                            c.`card_id` AS `c_id`,
                            c.`card_name` AS `c_name`
                        FROM
                            `cardfieldvalue` AS cfv
                            JOIN `cardfield` AS cf USING (`cardfield_id`)
                            JOIN `card` AS c USING (`card_id`)
                        WHERE cfv.`value` IN ('" . implode( "','", array_keys( $c_ids ) ) . "')
                            AND cf.`cardfield_type` = 'link'
                            AND cfv.`active` = 1" )
            ->fetchAll( 'child_id', 'c_id' );
        // Enrichment card data
        foreach ( self::$name_ids as $c_id => $ids ) {
            if ( isset( $links[$c_id] ) ) {
                $fieldsdata[$ids['cfv_id']]['link'] = $links[$c_id];
            }
            if ( isset( $parent[$ids['c_id']] ) ) {
                if ( !isset( $metadata['parent'] ) ) {
                    $metadata['parent'] = [];
                }
                $metadata['parent'] = $parent[$ids['c_id']];
            }
        }
    }

    /**
     * Store card id for get card data from storage
     * @param  string $child_id
     * @param  string $c_id
     * @param  string $cfv_id
     * @return void
     */
    private static function NeedName( $child_id, $c_id, $cfv_id ) {
        if ( !isset( self::$name_ids[$child_id] ) ) {
            self::$name_ids[$child_id] = [];
        }
        self::$name_ids[$child_id] = [
            'c_id'   => $c_id,
            'cfv_id' => $cfv_id
        ];
    }
}
