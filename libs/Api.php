<?php

/**
 * API proxy-class for JSONRPC-server
 */
class Api {
    /**
     * Check API enabled and get server params
     * @return array
     */
    public function check() {
        return [
            'domain' => DOMAIN
        ];
    }

    /**
     * Search Object by $query string
     * @param  string  $query
     * @return array
     */
    public function search( string $query ) {
        return "search-result for [${query}]";
    }
}
