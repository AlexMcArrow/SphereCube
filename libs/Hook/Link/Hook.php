<?php

namespace Hook\Link;

use DB;
use Hooks;

class Hook {
    /**
     * @var array
     */
    private static $name_ids = [];

    /**
     * Hook for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Hook_Card_After_ReadFieldsByID( &$data ) {
        self::$name_ids = [];
        foreach ( $data['fields'] as $cardfield ) {
            self::NeedName( $cardfield['cfv_value'], $cardfield['cid'], $cardfield['cfvid'] );
        }
        self::GetData( $data );
    }

    /**
     * Register hook hooks
     * @return void
     */
    public static function Register() {
        Hooks::register( 'After', 'CardRead', '\Hook\Link\Hook::Hook_Card_After_ReadFieldsByID' );
    }

    /**
     * Get data from storage
     * @param  array  $data
     * @return void
     */
    private static function GetData( &$data ) {
        // Get form storage linked cards
        $links = DB::getInstance()
            ->query( "SELECT
                            c.`card_id` AS `cid`,
                            c.`card_name` AS `c_name`,
                            c.`ts` AS `c_ts`,
                            u.`user_name` AS `u_user_name`,
                            u.`active` AS `u_active`
                        FROM
                            card AS c
                            JOIN `user` AS u USING (`user_id`)
                        WHERE c.`card_id` IN ('" . implode( "','", array_keys( self::$name_ids ) ) . "')
                        AND c.`active` = 1;" )
            ->fetchAll( 'cid' );
        $cids = [];
        array_walk_recursive( self::$name_ids, function ( string $v, string $k ) use ( &$cids ): void {
            if ( $k == 'cid' ) {$cids[$v] = $v;}
        } );
        // Get form storage parents for cards
        $parent = DB::getInstance()
            ->query( "SELECT
                            cfv.`value` AS `child_id`,
                            c.`card_id` AS `cid`,
                            c.`card_name` AS `c_name`,
                            c.`ts` AS `c_ts`,
                            u.`user_name` AS `u_user_name`,
                            u.`active` AS `u_active`
                        FROM
                            `cardfieldvalue` AS cfv
                            JOIN `cardfield` AS cf USING (`cardfield_id`)
                            JOIN `card` AS c USING (`card_id`)
                            JOIN `user` AS u
                                ON (u.`user_id` = c.`user_id`)
                        WHERE cfv.`value` IN ('" . implode( "','", array_keys( $cids ) ) . "')
                            AND cf.`cardfield_type` = 'link'
                            AND cfv.`active` = 1
                            AND c.`active` = 1" )
            ->fetchAll( 'child_id', 'cid' );
        // Enrichment card data
        foreach ( self::$name_ids as $cid => $ids ) {
            if ( isset( $links[$cid] ) ) {
                $data['fields'][$ids['cfvid']]['link'] = $links[$cid];
            }
            if ( isset( $parent[$ids['cid']] ) ) {
                if ( !isset( $data['meta']['parent'] ) ) {
                    $data['meta']['parent'] = [];
                }
                $data['meta']['parent'] = $parent[$ids['cid']];
            }
        }
    }

    /**
     * Store card id for get card data from storage
     * @param  string $child_id
     * @param  string $cid
     * @param  string $cfvid
     * @return void
     */
    private static function NeedName( $child_id, $cid, $cfvid ) {
        if ( !isset( self::$name_ids[$child_id] ) ) {
            self::$name_ids[$child_id] = [];
        }
        self::$name_ids[$child_id] = [
            'cid'   => $cid,
            'cfvid' => $cfvid
        ];
    }
}
