<?php

namespace Model;

class Card {
    /**
     * Read card by ID
     * @params string         $card_id
     * @return array|object
     */
    public static function ReadByID( $card_id ) {
        return ['card' =>
            \DB::getInstance()
                ->query( "SELECT
                                cfv.`card_id` AS `c_id`,
                                cf.`cardfield_id` AS `cf_id`,
                                cf.`cardfield_name` AS `cf_name`,
                                cf.`cardfield_type` AS `cf_type`,
                                cfv.`cardfieldvalue_id` AS `cfv_id`,
                                cfv.`value` AS `cfv_value`,
                                cfv.`active` AS `cfv_active`,
                                u.`user_name` AS `u_user_name`,
                                u.`active` AS `u_active`
                            FROM
                                card AS c
                                JOIN `cardfieldvalue` AS cfv USING (`card_id`)
                                JOIN `cardfield` AS cf USING (`cardfield_id`)
                                JOIN `user` AS u USING (`user_id`)
                            WHERE c.`card_id` = ?
                                AND cfv.`active` = 1;", $card_id )
                ->fetchAll( 'c_id', 'cfv_id' )];
    }

    /**
     * Search card
     * @params string         $query
     * @return array|object
     */
    public static function Search( $query ) {
        $q = '(*' . implode( '* *', explode( ' ', trim( $query, '*' ) ) ) . '*)';
        return ['list' =>
            \DB::getInstance()
                ->query( "SELECT
                                *
                            FROM
                                (SELECT
                                c.`card_id` AS `c_id`,
                                c.`card_name` AS `c_name`,
                                c.`card_name` AS `search_value`,
                                0.99 + MATCH (c.card_name) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                FROM
                                `card` AS c
                                WHERE MATCH (c.card_name) AGAINST (? IN BOOLEAN MODE)
                                UNION
                                ALL
                                SELECT
                                c.`card_id` AS `c_id`,
                                c.`card_name` AS `c_name`,
                                cfv.`value` AS `search_value`,
                                MATCH (cfv.`value`) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                FROM
                                `cardfieldvalue` AS cfv
                                JOIN `card` AS c USING (`card_id`)
                                WHERE MATCH (cfv.`value`) AGAINST (? IN BOOLEAN MODE)
                                AND cfv.`active` = 1) AS result
                            GROUP BY `c_id`
                            ORDER BY `search_score` DESC
                            LIMIT 10;", $q, $q, $q, $q )
                ->fetchAll( 'c_id' )];
    }

    /**
     * Update card Name
     * @params string         $card_id
     * @params string         $field_id
     * @params string         $type
     * @params string         $value
     * @return array|object
     */
    public static function UpdateField( $card_id, $field_id, $type, $value ) {
        // TODO: update field
        return self::ReadByID( $card_id );
    }

    /**
     * Update card Name
     * @params string         $card_id
     * @params string         $name
     * @return array|object
     */
    public static function UpdateName( $card_id, $name ) {
        // TODO: update name
        return self::ReadByID( $card_id );
    }
}
