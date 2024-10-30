<?php
/*
 * Plugin Name: Callback widget Pulsar
 * Description: The quick connection button for the website
 * Author URI: http://pulsarcallback.com/
 * License: GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Author: Pulsar
 * Text Domain: callback-widget-pulsar
 * Domain Path: /languages
 * Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CW_PULSAR_DIR', plugin_dir_path( __FILE__ ) );

add_action( 'init', 'cw_pulsar_init' );
if ( ! function_exists( 'cw_pulsar_init' ) ) {
	function cw_pulsar_init() {
		$script = get_option( 'cw_pulsar_script_code' );
		$enable = get_option( 'cw_pulsar_script_enable' );

		if ( ! empty( $script ) && $enable ) {
			add_action( 'wp_footer', function () use ( $script ) {
				echo html_entity_decode( $script );
			} );
		}
	}
}

add_action( 'plugins_loaded', 'cw_pulsar_plugin_loaded' );
if ( ! function_exists( 'cw_pulsar_plugin_loaded' ) ) {
	function cw_pulsar_plugin_loaded() {
		load_plugin_textdomain( 'callback-widget-pulsar', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}


add_action( 'admin_init', 'cw_pulsar_plugin_settings_link' );
if ( ! function_exists( 'cw_pulsar_plugin_settings_link' ) ) {
	function cw_pulsar_plugin_settings_link() {
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
			$settings_link = '<a href="/wp-admin/admin.php?page=callback-widget-pulsar">'
			                 . __( 'Settings', 'callback-widget-pulsar' ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		} );
	}
}

add_action( 'admin_init', 'cw_pulsar_register_pulsar_settings' );
if ( ! function_exists( 'cw_pulsar_register_pulsar_settings' ) ) {
	function cw_pulsar_register_pulsar_settings() {
		add_settings_section(
			'cw_pulsar_main_section',
			__( 'Pulsar plugin settings', 'callback-widget-pulsar' ),
			function () {
			},
			'callback-widget-pulsar'
		);

		add_settings_field(
			'cw_pulsar_script_code',
			__( 'Paste here script with tags', 'callback-widget-pulsar' ),
			'cw_pulsar_render_textarea',
			'callback-widget-pulsar',
			'cw_pulsar_main_section'
		);

		add_settings_field(
			'cw_pulsar_script_enable',
			__( 'Enable script on site', 'callback-widget-pulsar' ),
			'cw_pulsar_render_checkbox',
			'callback-widget-pulsar',
			'cw_pulsar_main_section'
		);

		register_setting( 'callback_widget_pulsar', 'cw_pulsar_script_code', 'cw_pulsar_sanitize_code' );
		register_setting( 'callback_widget_pulsar', 'cw_pulsar_script_enable', 'cw_pulsar_sanitize_integer' );
	}
}

if ( ! function_exists( 'cw_pulsar_render_textarea' ) ) {
	function cw_pulsar_render_textarea() {
		$value = get_option( 'cw_pulsar_script_code' );
		echo '<textarea name="cw_pulsar_script_code" class="large-text code" '
		     . ' rows="10">' . html_entity_decode( $value ) . '</textarea>';
	}
}

if ( ! function_exists( 'cw_pulsar_render_checkbox' ) ) {
	function cw_pulsar_render_checkbox() {
		$enabled = esc_attr( get_option( 'cw_pulsar_script_enable' ) );
		echo '<input type="checkbox" name="cw_pulsar_script_enable" value="1" '
		     . checked( $enabled, 1, false ) . '/>';
	}
}

/**
 * Validate inserted code, it must be  like:
 * <script src="//pulsarcallback.com/api/pulsar.js?pulsar_code=82"></script>
 *
 * @param mixed $input Input value
 *
 * @return string escaped input value
 */
if ( ! function_exists( 'cw_pulsar_sanitize_code' ) ) {
	function cw_pulsar_sanitize_code( $input ) {
		if ( empty( $input ) ) {
			return '';
		}

		if ( ! strpos( $input, 'pulsarcallback.com' )
		     || ! preg_match( '/<script[\s\S]*?>[\s\S]*?<\/script>/', $input )
		) {
			$input = '';

			add_settings_error(
				'login_button_primary_color',
				'login_button_primary_color_texterror',
				__( 'Code must have a link to Pulsar API with your personal ID', 'callback-widget-pulsar' ),
				'error'
			);

		}

		return htmlentities( wp_kses( $input, array(
			'script' => array(
				'src' => array()
			)
		) ) );
	}
}

if ( ! function_exists( 'cw_pulsar_sanitize_integer' ) ) {
	function cw_pulsar_sanitize_integer( $input ) {
		return absint( $input );
	}
}

add_action( 'admin_menu', 'cw_pulsar_admin_menu_callback' );
if ( ! function_exists( 'cw_pulsar_admin_menu_callback' ) ) {
	function cw_pulsar_admin_menu_callback() {
		add_options_page( 'Callback widget Pulsar', 'Callback Pulsar',
			'manage_options', 'callback-widget-pulsar', 'cw_pulsar_render_page' );
	}
}

if ( ! function_exists( 'cw_pulsar_render_page' ) ) {
	function cw_pulsar_render_page() {
		require_once CW_PULSAR_DIR . '/page.php';
	}
}
