<?php

namespace Plugin\Link;

use DB;
use Plugins;

class Plugin
{
    /**
     * @var array
     */
    private static $name_ids = [];

    /**
     * Plugin for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Plugin_Card_After_ReadFieldsByID(&$data)
    {
        self::$name_ids = [];
        foreach ($data['fields'] as $cardfield) {
            self::NeedName($cardfield['value'], $cardfield['cid'], $cardfield['cfvid']);
        }
        self::GetData($data);
    }

    /**
     * Plugin for Card After Search
     * @param  array  $data
     * @return void
     */
    public static function Plugin_Card_After_Search(&$data)
    {
        $data['result'] = array_filter($data['result'], function (array $item) {
            return (preg_match('/^([0-9A-Fa-f]{8}[-][0-9A-Fa-f]{4}[-][0-9A-Fa-f]{4}[-][0-9A-Fa-f]{4}[-][0-9A-Fa-f]{12})$/', $item['value']) == false) ? $item : false;
        });
    }

    /**
     * Plugin for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Plugin_On_Config(&$data)
    {
        $data['metas']['parent']            = 'meta-parent';
        $data['types']['link']              = 'type-card-link';
        $data['files']['meta-parent.js']    = 'Link/meta-parent.js';
        $data['files']['type-card-link.js'] = 'Link/type-card-link.js';
    }

    /**
     * Register plugin plugins
     * @return void
     */
    public static function Register()
    {
        Plugins::register('After', 'ModelCardRead', '\Plugin\Link\Plugin::Plugin_Card_After_ReadFieldsByID');
        Plugins::register('After', 'ModelCardSearch', '\Plugin\Link\Plugin::Plugin_Card_After_Search');
        Plugins::register('On', 'PluginsConfig', '\Plugin\Link\Plugin::Plugin_On_Config');
    }

    /**
     * Get data from storage
     * @param  array  $data
     * @return void
     */
    private static function GetData(&$data)
    {
        // Get form storage linked cards
        $links = DB::getInstance()
            ->query("SELECT
                            c.`card_id` AS `cid`,
                            c.`card_name` AS `name`
                        FROM
                            card AS c
                        WHERE c.`card_id` IN ('" . implode("','", array_keys(self::$name_ids)) . "');")
            ->fetchAll('cid');
        $cids = [];
        array_walk_recursive(self::$name_ids, function (string $v, string $k) use (&$cids): void {
            if ($k == 'cid') {
                $cids[$v] = $v;
            }
        });
        // Get form storage parents for cards
        $parent = DB::getInstance()
            ->query("SELECT
                            cfv.`value` AS `child_id`,
                            c.`card_id` AS `cid`,
                            c.`card_name` AS `name`
                        FROM
                            `cardfieldvalue` AS cfv
                            JOIN `cardfield` AS cf USING (`cardfield_id`)
                            JOIN `card` AS c USING (`card_id`)
                        WHERE cfv.`value` IN ('" . implode("','", array_keys($cids)) . "')
                            AND cf.`plugin_code` = 'link';")
            ->fetchAll('child_id', 'cid');
        // Enrichment card data
        foreach (self::$name_ids as $cid => $ids) {
            if (isset($links[$cid])) {
                $data['fields'][$ids['cfvid']]['link'] = $links[$cid];
            }
            if (isset($parent[$ids['cid']])) {
                if (!isset($data['meta']['parent'])) {
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
    private static function NeedName($child_id, $cid, $cfvid)
    {
        if (!isset(self::$name_ids[$child_id])) {
            self::$name_ids[$child_id] = [];
        }
        self::$name_ids[$child_id] = [
            'cid'   => $cid,
            'cfvid' => $cfvid
        ];
    }
}
