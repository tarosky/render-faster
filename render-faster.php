<?php
/**
Plugin Name: Render Faster
Plugin URI: https://wordpress.org/plugins/render-faster/
Description: Render your page faster.
Author: Tarosky INC.
Version: nightly
Author URI: https://tarosky.co.jp/
License: GPL3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: render-faster
Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

/**
 * Initializer.
 */
function render_faster_init() {
	// Load text domain.
	load_plugin_textdomain( 'render-faster', false, basename( __DIR__ ) . '/languages' );
	// Initialize.
	$autoloader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoloader ) ) {
		require $autoloader;
		Tarosky\RenderFaster\Bootstrap::get_instance();
	}
}
add_action( 'plugin_loaded', 'render_faster_init' );

/**
 * Always separate block assets.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
