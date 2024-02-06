<?php

namespace GetepayCF7\Admin;

class EnqueueScripts
{
    private const SCREEN_ID = 'contact_page_getepay-cf7';

    public function register()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'scripts' ) );
    }

    public function scripts()
    {
        $screen = get_current_screen();
        if ( $screen->id === self::SCREEN_ID ) {
            wp_enqueue_script( 'general-settings', GETEPAY_CF7_ASSETS_URL. 'js/general-settings.js', array(), filemtime( GETEPAY_CF7_PLUGIN_PATH. 'assets/js/general-settings.js' ) );
        }
    }
}