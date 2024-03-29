<?php

namespace Plugin\Card;

use SphereCube\Plugins;
use Plugin\Card\Plugin as Card;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint
{
    /**
     * Create Card
     * @param  string        $value
     * @return array|false
     */
    public function CardCreate(string $value)
    {
        $cid = Card::InsertCard($value);
        return $this->CardRead($cid);
    }

    /**
     * Create Field type
     * @param  string   $value
     * @param  string   $type
     * @return string
     */
    public function CardCreateField(string $value, string $type)
    {
        return Card::InsertField($value, $type);
    }

    /**
     * Create Card
     * @param  string        $cid
     * @param  string        $cfid
     * @param  string        $value
     * @return array|false
     */
    public function CardCreateFieldValue(string $cid, string $cfid, string $value)
    {
        Card::InsertFieldValue($cid, $cfid, $value);
        return $this->CardRead($cid);
    }

    /**
     * Delete Card
     * @param  string $cid
     * @return true
     */
    public function CardDelete(string $cid)
    {
        return Card::DeleteCard($cid);
    }

    /**
     * Delete Card
     * @param  string        $cid
     * @return array|false
     */
    public function CardDeleteFieldValue(string $cid, string $cfvid)
    {
        Card::DeleteCardFieldValue($cid, $cfvid);
        return $this->CardRead($cid);
    }

    /**
     * Read card
     * @param  string        $cid
     * @return array|false
     */
    public function CardRead(string $cid)
    {
        $plugindata = [
            'meta'   => [],
            'fields' => []
        ];
        Plugins::calling('Before', 'ModelCardReadByID', $plugindata);
        $plugindata = array_merge($plugindata, ['meta' => Card::ReadByID($cid)]);
        Plugins::calling('After', 'ModelCardReadByID', $plugindata);
        if (key_exists('cid', $plugindata['meta'])) {
            Plugins::calling('Before', 'ModelCardRead', $plugindata);
            $plugindata = array_merge($plugindata, ['fields' => Card::ReadFieldsByID($cid)]);
            Plugins::calling('After', 'ModelCardRead', $plugindata);
            return $plugindata;
        }
        return false;
    }

    /**
     * Update Card
     * @param  string        $cid
     * @param  string        $value
     * @return array|false
     */
    public function CardUpdate(string $cid, string $value)
    {
        Card::UpdateCard($cid, $value);
        return $this->CardRead($cid);
    }

    /**
     * Update Card field
     * @param  string        $cid
     * @param  string        $cfvid
     * @param  string        $cfid
     * @param  string        $value
     * @return array|false
     */
    public function CardUpdateFieldValue(string $cid, string $cfvid, string $cfid, string $value)
    {
        Card::UpdateField($cid, $cfvid, $cfid, $value);
        return $this->CardRead($cid);
    }

    /**
     * Search Cards by $query string
     * @param  string  $query
     * @return array
     */
    public function Search(string $query)
    {
        return Card::Search($query);
    }

    /**
     * Search fields by $query string
     * @param  string  $query
     * @return array
     */
    public function SearchField(string $query)
    {
        return Card::SearchField($query);
    }
}
