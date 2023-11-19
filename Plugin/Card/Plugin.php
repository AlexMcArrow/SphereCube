<?php

namespace Plugin\Card;

use SphereCube\DB;
use SphereCube\Plugins;

class Plugin
{
    /**
     * Delete card
     * @param  string $card_id
     * @return true
     */
    public static function DeleteCard($card_id)
    {
        // DB::getInstance()->query( "UPDATE
        //                                 card
        //                             SET
        //                                 active = 0
        //                             WHERE card_id = ?;", [$card_id] );
        return true;
    }

    /**
     * Delete card
     * @param  string   $card_id
     * @param  string   $field_id
     * @return string
     */
    public static function DeleteCardFieldValue($card_id, $field_id)
    {
        // DB::getInstance()->query( "UPDATE
        //                                 cardfieldvalue
        //                             SET
        //                                 active = 0
        //                             WHERE cardfieldvalue_id = ?;", [$field_id] );
        return $card_id;
    }

    /**
     * Create new card
     * @param  string   $name
     * @return string
     */
    public static function InsertCard($name)
    {
        $uuid = gen_uuid();
        DB::getInstance()->query("INSERT INTO card
                                    (card_id,card_name)
                                    VALUES
                                    (?,?);", [$uuid, $name]);
        return $uuid;
    }

    /**
     * Create new card
     * @param  string   $name
     * @param  string   $type
     * @return string
     */
    public static function InsertField($name, $type)
    {
        $uuid = gen_uuid();
        DB::getInstance()->query("INSERT INTO cardfield
                                        (cardfield_id,cardfield_name,plugin_code)
                                    VALUES
                                        (?,?,?);", [$uuid, $name, $type]);
        return $uuid;
    }

    /**
     * Update card Name
     * @param  string   $card_id
     * @param  string   $type_id
     * @param  string   $value
     * @return string
     */
    public static function InsertFieldValue($card_id, $type_id, $value)
    {
        $uuid = gen_uuid();
        DB::getInstance()->query("INSERT INTO cardfieldvalue
                                    (cardfieldvalue_id,card_id,cardfield_id,value)
                                    VALUES
                                    (?,?,?,?);", [$uuid, $card_id, $type_id, $value]);
        return $uuid;
    }

    /**
     * Plugin on config call
     * @param  array  $data
     * @return void
     */
    public static function Plugin_On_Config(&$data)
    {
        $data['models']['Card']   = 'card';
        $data['files']['card.js'] = 'Card/card.js';
    }

    /**
     * Read card by ID
     * @param  string  $card_id
     * @return array
     */
    public static function ReadByID($card_id)
    {
        return DB::getInstance()
            ->query("SELECT
                            c.card_id AS cid,
                            c.card_name AS name
                        FROM
                            card AS c
                        WHERE c.card_id = ?;", [$card_id])
            ->fetchAll(true);
    }

    /**
     * Read card by ID
     * @param  string  $card_id
     * @return array
     */
    public static function ReadFieldsByID($card_id)
    {
        return DB::getInstance()
            ->query("SELECT
                            cfv.card_id AS cid,
                            cf.cardfield_id AS cfid,
                            cf.cardfield_name AS name,
                            p.plugin_code AS cf_type,
                            cfv.cardfieldvalue_id AS cfvid,
                            cfv.value AS value
                        FROM
                            card AS c
                            JOIN cardfieldvalue AS cfv USING (card_id)
                            JOIN cardfield AS cf USING (cardfield_id)
                            JOIN plugin as p using (plugin_code,plugin_field)
                        WHERE c.card_id = ?
                            AND p.active = 1
                        ORDER BY cfv.cardfieldvalue_pos ASC;", [$card_id])
            ->fetchAll('cfvid');
    }

    /**
     * Register plugin plugins
     * @return void
     */
    public static function Register()
    {
        Plugins::register('On', 'PluginsConfig', '\Plugin\Card\Plugin::Plugin_On_Config');
        Plugins::register('On', 'PluginsSearch', '\Plugin\Card\Plugin::Search');
        Plugins::register('On', 'PluginsSearchField', '\Plugin\Card\Plugin::SearchField');
    }

    /**
     * Search card
     * @param  array  $data
     * @return void
     */
    public static function Search(&$data)
    {
        Plugins::calling('Before', 'ModelCardSearch', $data);
        $query = $data['query'] ?: '';
        if (strlen($query) > 2) {
            $data['result'] = DB::getInstance()
                ->query(
                    <<<SQL
                SELECT
                    c.card_id AS cid,
                    c.card_name AS name,
                    COALESCE(cfv.value, c.card_name) AS value
                FROM
                    card AS c
                LEFT JOIN cardfieldvalue cfv
                    ON
                    (
                        c.card_id = cfv.card_id
                        AND LOWER(cfv.value) ILIKE LOWER(:search)
                    )
                WHERE
                    cfv.cardfieldvalue_id IS NOT NULL
                    OR c.card_id IN (
                    SELECT
                        c.card_id
                    FROM
                        card AS c
                    WHERE
                        LOWER(c.card_name) ILIKE LOWER(:search)
                    )
                SQL,
                    ['search' => '%' . $query . '%']
                )
                ->fetchAll('cid');
        }
        Plugins::calling('After', 'ModelCardSearch', $data);
    }

    /**
     * Search field
     * @param  array  $data
     * @return void
     */
    public static function SearchField(&$data)
    {
        Plugins::calling('Before', 'ModelCardSearchField', $data);
        // $query = $data['query'] ?: '';
        // $q     = trim($query, '*') . '*';
        // if (strlen($q) > 1) {
        //     $data['result'] = DB::getInstance()
        //         ->query("SELECT
        //                     cf.cardfield_id AS cfid,
        //                     cf.cardfield_name AS name,
        //                     cf.plugin_code AS cf_type
        //                 FROM
        //                     cardfield cf
        //                 WHERE cf.cardfield_id IN (:ids);", ["'" . implode("','", $ids) . "'"])
        //         ->fetchAll('cfid');
        // }
        Plugins::calling('After', 'ModelCardSearchField', $data);
    }

    /**
     * Update card Name
     * @param  string $card_id
     * @param  string $name
     * @return void
     */
    public static function UpdateCard($card_id, $name)
    {
        DB::getInstance()->query("UPDATE
                                        card
                                    SET
                                        card_name = ?
                                    WHERE card_id = ?;", [$name, $card_id]);
    }

    /**
     * Update card Name
     * @param  string $card_id
     * @param  string $field_id
     * @param  string $type_id
     * @param  string $value
     * @return void
     */
    public static function UpdateField($card_id, $field_id, $type_id, $value)
    {
        DB::getInstance()->query("UPDATE
                                        cardfieldvalue
                                    SET
                                        card_id = ?,
                                        cardfield_id = ?,
                                        value = ?
                                    WHERE cardfieldvalue_id = ?;", [$card_id, $type_id, $value, $field_id]);
    }
}
