<?php

class Cache
{
    /**
     * @var object
     */
    private static $pool;

    /**
     * @return void
     */
    public function __construct()
    {
        self::$pool = new Memcached(MEMCACHED_PCON);
        self::$pool->addServers(MEMCACHED_POOL);
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public static function read($key)
    {
        $res = self::$pool->get(self::key($key));
        if ($res) {
            return json_decode($res, true, 512, JSON_OBJECT_AS_ARRAY);
        }
        return false;
    }

    /**
     * @param  string   $key
     * @param  int      $ttl
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public static function readorwrite($key, $ttl, $callback, ...$args)
    {
        $res = self::read($key);
        if ($res === false) {
            $res = call_user_func_array($callback, $args);
            self::write($key, $res, $ttl);
        }
        return $res;
    }

    /**
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $ttl
     * @return mixed
     */
    public static function write($key, $value, $ttl)
    {
        return self::$pool->set(self::key($key), json_encode($value, JSON_UNESCAPED_UNICODE), $ttl);
    }

    /**
     * @param  string   $key
     * @return string
     */
    private static function key($key)
    {
        return hash('sha256', strtolower(trim(MEMCACHED_PCON . $key)));
    }
}
