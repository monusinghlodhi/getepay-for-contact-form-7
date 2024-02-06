<?php

namespace GetepayCF7\Settings;

class API {

	public static $options;

	public function __construct() {
		self::$options = get_option( 'getepay_cf7_api_options' );
	}

	public function register() {
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	public function init() {
		register_setting( 'getepay_cf7_api', 'getepay_cf7_api_options' );

		add_settings_section(
			'getepay_cf7_live_section',
			"<h3>Live Credentials</h3>
        <p class='description'>Contact Us <a href='https://getepay.in/contact/' target='_blank'><code>https://getepay.in/contact/</code></a> to get your live credentials.</p>",
			null,
			'getepay_cf7_live_settings'
		);

		add_settings_field(
			'getepay_cf7_live_request_url',
			'Getepay Request Url',
			array( $this, 'request_url_callback' ),
			'getepay_cf7_live_settings',
			'getepay_cf7_live_section',
		);

		add_settings_field(
			'getepay_cf7_live_mid',
			'Getepay MID',
			array( $this, 'mid_callback' ),
			'getepay_cf7_live_settings',
			'getepay_cf7_live_section',
		);

		add_settings_field(
			'getepay_cf7_live_terminal_id',
			'Getepay Terminal Id',
			array( $this, 'terminal_id_callback' ),
			'getepay_cf7_live_settings',
			'getepay_cf7_live_section',
		);

		add_settings_field(
			'getepay_cf7_live_secret_key',
			'Getepay Key',
			array( $this, 'secret_key_callback' ),
			'getepay_cf7_live_settings',
			'getepay_cf7_live_section',
		);

		add_settings_field(
			'getepay_cf7_live_iv',
			'Getepay IV',
			array( $this, 'getepay_iv_callback' ),
			'getepay_cf7_live_settings',
			'getepay_cf7_live_section',
		);

		add_settings_section(
			'getepay_cf7_sandbox_section',
			"<h3>Test Credentials</h3>
        <p class='description'>Contact Us <a href='https://getepay.in/contact/' target='_blank'><code>https://getepay.in/contact/</code></a> to get your test credentials.</p>",
			null,
			'getepay_cf7_sandbox_settings',
		);

		add_settings_field(
			'getepay_cf7_sandbox_request_url',
			'Getepay Request Url',
			array( $this, 'sandbox_request_url_callback' ),
			'getepay_cf7_sandbox_settings',
			'getepay_cf7_sandbox_section',
		);

		add_settings_field(
			'getepay_cf7_sandbox_mid',
			'Getepay MID',
			array( $this, 'sandbox_mid_callback' ),
			'getepay_cf7_sandbox_settings',
			'getepay_cf7_sandbox_section',
		);

		add_settings_field(
			'getepay_cf7_sandbox_terminal_id',
			'Getepay Terminal Id',
			array( $this, 'sandbox_terminal_id_callback' ),
			'getepay_cf7_sandbox_settings',
			'getepay_cf7_sandbox_section',
		);

		add_settings_field(
			'getepay_cf7_sandbox_secret_key',
			'Getepay Key',
			array( $this, 'sandbox_secret_key_callback' ),
			'getepay_cf7_sandbox_settings',
			'getepay_cf7_sandbox_section',
		);

		add_settings_field(
			'getepay_cf7_sandbox_iv',
			'Getepay IV',
			array( $this, 'sandbox_getepay_iv_callback' ),
			'getepay_cf7_sandbox_settings',
			'getepay_cf7_sandbox_section',
		);
	}
	
	public function request_url_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_live_request_url]" id="getepay_cf7_live_request_url" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_live_request_url'] ) ? self::$options['getepay_cf7_live_request_url'] : '' ); ?>">
		<?php
	}
	
	public function mid_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_live_mid]" id="getepay_cf7_live_mid" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_live_mid'] ) ? self::$options['getepay_cf7_live_mid'] : '' ); ?>">
		<?php
	}

	public function terminal_id_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_live_terminal_id]" id="getepay_cf7_live_terminal_id" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_live_terminal_id'] ) ? self::$options['getepay_cf7_live_terminal_id'] : '' ); ?>">
		<?php
	}

	public function secret_key_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_live_secret_key]" id="getepay_cf7_live_secret_key" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_live_secret_key'] ) ? self::$options['getepay_cf7_live_secret_key'] : '' ); ?>">
		<?php
	}

	public function getepay_iv_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_live_iv]" id="getepay_cf7_live_iv" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_live_iv'] ) ? self::$options['getepay_cf7_live_iv'] : '' ); ?>">
		<?php
	}

	public function sandbox_request_url_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_sandbox_request_url]" id="getepay_cf7_sandbox_request_url" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_sandbox_request_url'] ) ? self::$options['getepay_cf7_sandbox_request_url'] : '' ); ?>">
		<?php
	}
	
	public function sandbox_mid_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_sandbox_mid]" id="getepay_cf7_sandbox_mid" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_sandbox_mid'] ) ? self::$options['getepay_cf7_sandbox_mid'] : '' ); ?>">
		<?php
	}

	public function sandbox_terminal_id_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_sandbox_terminal_id]" id="getepay_cf7_sandbox_terminal_id" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_sandbox_terminal_id'] ) ? self::$options['getepay_cf7_sandbox_terminal_id'] : '' ); ?>">
		<?php
	}

	public function sandbox_secret_key_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_sandbox_secret_key]" id="getepay_cf7_sandbox_secret_key" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_sandbox_secret_key'] ) ? self::$options['getepay_cf7_sandbox_secret_key'] : '' ); ?>">
		<?php
	}

	public function sandbox_getepay_iv_callback() {
		?>
	<input class="regular-text" type="text" name="getepay_cf7_api_options[getepay_cf7_sandbox_iv]" id="getepay_cf7_sandbox_iv" value="<?php echo esc_attr( isset( self::$options['getepay_cf7_sandbox_iv'] ) ? self::$options['getepay_cf7_sandbox_iv'] : '' ); ?>">
		<?php
	}

}
