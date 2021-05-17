<?php

namespace Plugin\Text;

use Plugins;

class Plugin {
    /**
     * Plugin for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Plugin_On_Config( &$data ) {
        $data['types']['text']                = 'type-simple-text';
        $data['files']['type-simple-text.js'] = 'Text/type-simple-text.js';
    }

    /**
     * Register plugin plugins
     * @return void
     */
    public static function Register() {
        Plugins::register( 'On', 'PluginsConfig', '\Plugin\Text\Plugin::Plugin_On_Config' );
    }
}
