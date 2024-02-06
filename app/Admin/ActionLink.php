<?php
/**
 * The ActionLink class defines the action links for the Getepay CF7 plugin.
 *
 * @package GetepayCF7\Admin
 */

namespace GetepayCF7\Admin;

/**
 * Class ActionLink
 */
class ActionLink {

	/**
	 * Registers the action links.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'plugin_action_links', array( $this, 'links' ), 10, 2 );
	}

	/**
	 * Adds action links to the plugin.
	 *
	 * @param array  $links An array of existing plugin action links.
	 * @param string $file  The path to the plugin file relative to the plugins directory.
	 *
	 * @return array An array of modified plugin action links.
	 */
	public function links( $links, $file ) {
		if ( $file == GETEPAY_CF7_PLUGIN_FILE ) {
			$general_link    = '<a href="' . admin_url( 'admin.php?page=getepay-cf7&tab=general-settings' ) . '">' . __( 'General Settings', GETEPAY_CF7_TEXT_DOMAIN ) . '</a>';
			$api_link        = '<a href="' . admin_url( 'admin.php?page=getepay-cf7&tab=api-settings' ) . '">' . __( 'API Settings', GETEPAY_CF7_TEXT_DOMAIN ) . '</a>';
			array_unshift( $links, $general_link, $api_link );
			$deactivate_link = array_pop( $links );
			array_push( $links, $deactivate_link );
		}
		return $links;
	}
}
