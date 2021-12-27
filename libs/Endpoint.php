<?php

use Plugins;

/**
 * API proxy-class for JSONRPC-server
 */
class Endpoint
{
    /**
     * Config
     * @return array
     */
    public function Config()
    {
        return Plugins::Config();
    }

    /**
     * Check API enabled and get server params
     * @return array
     */
    public function Ping()
    {
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
    public function Search(string $query)
    {
        return Plugins::Search($query);
    }

    /**
     * Search fields by $query string
     * @param  string  $query
     * @return array
     */
    public function SearchField(string $query)
    {
        return Plugins::SearchField($query);
    }
}
