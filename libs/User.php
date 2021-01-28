<?php

class User {
    /**
     * @var string|bool
     */
    public static $id = false;

    /**
     * @return void
     */
    public function __construct() {
        self::$id = '1111111-1111-1111-1111-111111111111';
    }
}
