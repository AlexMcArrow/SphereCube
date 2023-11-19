<?php

namespace Plugin\Ts;

use SphereCube\Plugins;

class Plugin
{
    /**
     * Plugin for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Plugin_On_Config(&$data)
    {
        $data['metas']['ts']         = 'meta-ts';
        $data['files']['meta-ts.js'] = 'Ts/meta-ts.js';
    }

    /**
     * Register plugin plugins
     * @return void
     */
    public static function Register()
    {
        Plugins::register('On', 'PluginsConfig', '\Plugin\Ts\Plugin::Plugin_On_Config');
    }
}
