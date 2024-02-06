<?php
/**
 * The Menu class defines the admin menu for the Getepay CF7 plugin.
 *
 * @package GetepayCF7\Admin
 */

namespace GetepayCF7\Admin;

/**
 * Class Menu
 */
class Menu {
	
	/**
	 * Registers the admin menu.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Adds the submenu page.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'wpcf7',
			__( 'Getepay for Contact Form 7', GETEPAY_CF7_TEXT_DOMAIN ),
			__( 'Getepay', GETEPAY_CF7_TEXT_DOMAIN ),
			'manage_options',
			'getepay-cf7',
			array( $this, 'callback' )
		);
	}

	/**
	 * Renders the page callback.
	 *
	 * @return void
	 */
	public function callback() {
		require_once GETEPAY_CF7_PLUGIN_PATH .
			'app/views/page-callback.php';
	}
}