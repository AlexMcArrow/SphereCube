<?php

namespace SphereCube;

use SphereCube\DBInstance;

class DB
{
    private static DBInstance $DB;
    function __construct(string $db = '', string  $host = '', string  $user = '', string  $pass = '', int $port = 5432, string  $scheme = 'public')
    {
        self::$DB = new DBInstance($db, $host, $user, $pass, $port, $scheme);
    }

    public static function getInstance(): DBInstance
    {
        return self::$DB;
    }
}
