<?php

namespace Plugin\User;

use Plugins;

class Plugin
{
    /**
     * @var string|bool
     */
    public static $id = false;

    /**
     * Plugin for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Plugin_On_Config(&$data)
    {
        $data['metas']['user'] = 'meta-user';
        $data['files']['meta-user.js'] = 'User/meta-user.js';
    }

    /**
     * Register plugin plugins
     * @return void
     */
    public static function Register()
    {
        Plugins::register('On', 'PluginsConfig', '\Plugin\User\Plugin::Plugin_On_Config');
    }

    /**
     * @return void
     */
    public function __construct()
    {
        self::$id = '1111111-1111-1111-1111-111111111111';
    }
}
