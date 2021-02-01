<?php

namespace Hook\Text;

use Hooks;

class Hook {
    /**
     * Hook for Card After ReadByID
     * @param  array  $data
     * @return void
     */
    public static function Hook_On_Config( &$data ) {
        $data['models']['Card'] = 'card';
        $data['metas']['ts']    = 'meta-ts';
        $data['metas']['user']  = 'meta-user';
        $data['types']['text']  = 'type-simple-text';
    }

    /**
     * Register hook hooks
     * @return void
     */
    public static function Register() {
        Hooks::register( 'On', 'Config', '\Hook\Text\Hook::Hook_On_Config' );
    }
}
