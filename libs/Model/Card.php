<?php

namespace Model;

class Card {
    /**
     * Read card by ID
     * @params string  $card_id
     * @return array
     */
    public static function ReadByID( $card_id ) {
        return \DB::getInstance()->query( "SELECT
                                        ofv.`card_id` AS `o_id`,
                                        of.`cardfield_id` AS `of_id`,
                                        of.`cardfield_name` AS `of_name`,
                                        of.`cardfield_type` AS `of_type`,
                                        ofv.`cardfieldvalue_id` AS `ofv_id`,
                                        ofv.`value` AS `ofv_value`,
                                        ofv.`active` AS `ofv_active`,
                                        u.`user_name` AS `u_user_name`,
                                        u.`active` AS `u_active`
                                    FROM
                                        card AS o
                                        JOIN `cardfieldvalue` AS ofv USING (`card_id`)
                                        JOIN `cardfield` AS of USING (`cardfield_id`)
                                        JOIN `user` AS u USING (`user_id`)
                                    WHERE o.`card_id` = ?
                                        AND ofv.`active` = 1;", $card_id )->fetchAll();
    }

    /**
     * Search card
     * @params string  $query
     * @return array
     */
    public static function Search( $query ) {
        $q = '(*' . implode( '* *', explode( ' ', trim( $query, '*' ) ) ) . '*)';
        return \DB::getInstance()->query( "SELECT
                                                *
                                            FROM
                                                (SELECT
                                                o.`card_id` AS `o_id`,
                                                o.`card_name` AS `o_name`,
                                                o.`card_name` AS `search_value`,
                                                0.99 + MATCH (o.card_name) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                                FROM
                                                `card` AS o
                                                WHERE MATCH (o.card_name) AGAINST (? IN BOOLEAN MODE)
                                                UNION
                                                ALL
                                                SELECT
                                                o.`card_id` AS `o_id`,
                                                o.`card_name` AS `o_name`,
                                                ofv.`value` AS `search_value`,
                                                MATCH (ofv.`value`) AGAINST (? IN BOOLEAN MODE) AS `search_score`
                                                FROM
                                                `cardfieldvalue` AS ofv
                                                JOIN `card` AS o USING (`card_id`)
                                                WHERE MATCH (ofv.`value`) AGAINST (? IN BOOLEAN MODE)
                                                AND ofv.`active` = 1) AS result
                                            GROUP BY `o_id`
                                            ORDER BY `search_score` DESC
                                            LIMIT 10;", $q, $q, $q, $q )->fetchAll();
    }

    /**
     * Update card Name
     * @params string  $card_id
     * @params string  $field_id
     * @params string  $type
     * @params string  $value
     * @return array
     */
    public static function UpdateField( $card_id, $field_id, $type, $value ) {
        // TODO: update field
        return self::ReadByID( $card_id );
    }

    /**
     * Update card Name
     * @params string  $card_id
     * @params string  $name
     * @return array
     */
    public static function UpdateName( $card_id, $name ) {
        // TODO: update name
        return self::ReadByID( $card_id );
    }
}
