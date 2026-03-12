<?php
/**
 * Plugin Name: NS Podcast Manager
 * Plugin URI:  https://github.com/nscott/ns-podcast-manager
 * Description: Reusable podcast CPT with ACF fields and a settings page. Drop in, configure, go.
 * Version:     1.0.3
 * Author:      Nic Scott
 * License:     GPL-2.0-or-later
 * Text Domain: ns-podcast-manager
 */

defined( 'ABSPATH' ) || exit;

define( 'NS_PM_VERSION', '1.0.3' );
define( 'NS_PM_DIR', plugin_dir_path( __FILE__ ) );
define( 'NS_PM_URL', plugin_dir_url( __FILE__ ) );

require_once NS_PM_DIR . 'includes/class-settings.php';
require_once NS_PM_DIR . 'includes/class-rewrites.php';
require_once NS_PM_DIR . 'includes/class-cpt.php';
require_once NS_PM_DIR . 'includes/class-acf-fields.php';
require_once NS_PM_DIR . 'includes/class-embeds.php';
require_once NS_PM_DIR . 'includes/class-dynamic-data.php';

/**
 * Returns the plugin options with defaults merged in.
 */
function ns_pm_options(): array {
	$defaults = [
		'cpt_slug'               => 'podcasts',
		'singular_label'         => 'Episode',
		'plural_label'           => 'Episodes',
		'show_name'              => 'Podcast',
		'enable_archive'         => '1',
		'enable_rest'            => '1',
		'menu_icon'              => 'dashicons-microphone',
		'enable_season'          => '0',
		'enable_transcript'      => '0',
		'enable_guests'          => '1',
		'show_url_spotify'       => '',
		'show_url_apple'         => '',
		'show_url_youtube'       => '',
		'show_url_amazon'        => '',
		'show_url_iheart'        => '',
		'show_url_rss'           => '',
		'show_url_other_label'   => '',
		'show_url_other'         => '',
	];

	$saved = get_option( 'ns_podcast_manager_options', [] );

	return wp_parse_args( $saved, $defaults );
}

register_activation_hook( __FILE__, function () {
	// init has already run in this request, so the CPT isn't registered yet.
	// Register it manually first so the flush captures its rewrite rules.
	NS_PM_CPT::register();
	flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, [ 'NS_PM_Rewrites', 'flush' ] );

add_action( 'plugins_loaded', function () {
	NS_PM_Settings::init();
	NS_PM_CPT::init();
	NS_PM_ACF_Fields::init();
	NS_PM_Embeds::init();
} );

add_action( 'init', [ 'NS_PM_Dynamic_Data', 'init' ] );
