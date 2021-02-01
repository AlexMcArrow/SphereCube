<?php

namespace Model;

use DB;
use User;

class Card {
    /**
     * Delete card
     * @param  string $card_id
     * @return true
     */
    public static function DeleteCard( $card_id ) {
        DB::getInstance()->query( "UPDATE
                                        `card`
                                    SET
                                        `user_id` = ?,
                                        `active` = 0
                                    WHERE `card_id` = ?;", User::$id, $card_id );
        return true;
    }

    /**
     * Delete card
     * @param  string   $card_id
     * @param  string   $field_id
     * @return string
     */
    public static function DeleteCardFieldValue( $card_id, $field_id ) {
        DB::getInstance()->query( "UPDATE
                                        `cardfieldvalue`
                                    SET
                                        `user_id` = ?,
                                        `active` = 0
                                    WHERE `cardfieldvalue_id` = ?;", User::$id, $field_id );
        return $card_id;
    }

    /**
     * Create new card
     * @param  string   $name
     * @return string
     */
    public static function InsertCard( $name ) {
        $uuid = gen_uuid();
        DB::getInstance()->query( "INSERT INTO `card`
                                    (`card_id`,`card_name`,`user_id`,`active`,`ts`)
                                    VALUES
                                    (?,?,?,1,UNIX_TIMESTAMP());", $uuid, $name, User::$id );
        return $uuid;
    }

    /**
     * Create new card
     * @param  string   $name
     * @param  string   $type
     * @return string
     */
    public static function InsertField( $name, $type ) {
        $uuid = gen_uuid();
        DB::getInstance()->query( "INSERT INTO `cardfield`
                                        (`cardfield_id`,`cardfield_name`,`cardfield_type`)
                                    VALUES
                                        (?,?,?);", $uuid, $name, $type );
        return $uuid;
    }

    /**
     * Update card Name
     * @param  string   $card_id
     * @param  string   $type_id
     * @param  string   $value
     * @return string
     */
    public static function InsertFieldValue( $card_id, $type_id, $value ) {
        $uuid = gen_uuid();
        DB::getInstance()->query( "INSERT INTO `cardfieldvalue`
                                    (`cardfieldvalue_id`,`card_id`, `cardfield_id`,`user_id`,`value`,`active`,`ts`)
                                    VALUES
                                    (?,?,?,?,?,1,UNIX_TIMESTAMP());", $uuid, $card_id, $type_id, User::$id, $value );
        return $uuid;
    }

    /**
     * Read card by ID
     * @param  string  $card_id
     * @return array
     */
    public static function ReadByID( $card_id ) {
        return DB::getInstance()
            ->query( "SELECT
                            c.`card_id` AS `cid`,
                            c.`card_name` AS `c_name`,
                            c.`ts` AS `c_ts`,
                            c.`active` AS `c_active`,
                            u.`user_name` AS `u_user_name`,
                            u.`active` AS `u_active`
                        FROM
                            card AS c
                        JOIN `user` AS u USING (`user_id`)
                        WHERE c.`card_id` = ?
                        AND c.`active` = 1;", $card_id )
            ->fetchAll( true );
    }

    /**
     * Read card by ID
     * @param  string  $card_id
     * @return array
     */
    public static function ReadFieldsByID( $card_id ) {
        return DB::getInstance()
            ->query( "SELECT
                            cfv.`card_id` AS `cid`,
                            cf.`cardfield_id` AS `cfid`,
                            cf.`cardfield_name` AS `cf_name`,
                            cf.`cardfield_type` AS `cf_type`,
                            cfv.`cardfieldvalue_id` AS `cfvid`,
                            cfv.`value` AS `cfv_value`,
                            cfv.`ts` AS `cfv_ts`,
                            cfv.`active` AS `cfv_active`,
                            u.`user_name` AS `u_user_name`,
                            u.`active` AS `u_active`
                        FROM
                            card AS c
                            JOIN `cardfieldvalue` AS cfv USING (`card_id`)
                            JOIN `cardfield` AS cf USING (`cardfield_id`)
                            JOIN `user` AS u
                                ON (u.`user_id` = cfv.`user_id`)
                        WHERE c.`card_id` = ?
                            AND cfv.`active` = 1
                            AND c.`active` = 1;", $card_id )
            ->fetchAll( 'cfvid' );
    }

    /**
     * Search card
     * @param  string  $query
     * @return array
     */
    public static function Search( $query ) {
        $q = '(*' . implode( '* *', explode( ' ', trim( $query, '*' ) ) ) . '*)';
        if ( strlen( $q ) > 4 ) {
            return DB::getInstance()
                ->query( "SELECT
                                *
                            FROM
                                (SELECT
                                c.`card_id` AS `cid`,
                                c.`card_name` AS `c_name`,
                                c.`card_name` AS `search_value`,
                                0.99 + MATCH (c.card_name) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                FROM
                                `card` AS c
                                WHERE MATCH (c.card_name) AGAINST (? IN BOOLEAN MODE)
                                AND c.`active` = 1
                                UNION
                                ALL
                                SELECT
                                c.`card_id` AS `cid`,
                                c.`card_name` AS `c_name`,
                                cfv.`value` AS `search_value`,
                                MATCH (cfv.`value`) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                FROM
                                `cardfieldvalue` AS cfv
                                JOIN `card` AS c USING (`card_id`)
                                WHERE MATCH (cfv.`value`) AGAINST (? IN BOOLEAN MODE)
                                AND cfv.`active` = 1
                                AND c.`active` = 1) AS result
                            GROUP BY `cid`
                            ORDER BY `search_score` DESC
                            LIMIT 10;", $q, $q, $q, $q )
                ->fetchAll( 'cid' );
        }
        return [];
    }

    /**
     * Search field
     * @param  string  $query
     * @return array
     */
    public static function SearchField( $query ) {
        $q = trim( $query, '*' ) . '*';
        if ( strlen( $q ) > 1 ) {
            return DB::getInstance()->query( "SELECT
                                                    cf.`cardfield_id` AS cfid,
                                                    cf.`cardfield_name` AS cf_name,
                                                    cf.`cardfield_type` AS cf_type
                                                FROM
                                                    `cardfield` cf
                                                WHERE MATCH (cf.`cardfield_name`) AGAINST (? IN BOOLEAN MODE);", $q )->fetchAll( 'cfid' );
        }
        return [];
    }

    /**
     * Update card Name
     * @param  string $card_id
     * @param  string $name
     * @return void
     */
    public static function UpdateCard( $card_id, $name ) {
        DB::getInstance()->query( "UPDATE
                                        `card`
                                    SET
                                        `card_name` = ?,
                                        `user_id` = ?
                                    WHERE `card_id` = ?;", $name, User::$id, $card_id );
    }

    /**
     * Update card Name
     * @param  string $card_id
     * @param  string $field_id
     * @param  string $type_id
     * @param  string $value
     * @return void
     */
    public static function UpdateField( $card_id, $field_id, $type_id, $value ) {
        DB::getInstance()->query( "UPDATE
                                        `cardfieldvalue`
                                    SET
                                        `card_id` = ?,
                                        `cardfield_id` = ?,
                                        `user_id` = ?,
                                        `value` = ?
                                    WHERE `cardfieldvalue_id` = ?;", $card_id, $type_id, User::$id, $value, $field_id );
    }
}
