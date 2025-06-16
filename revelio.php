<?php
/**
 * Plugin Name: Revelio Post Meta
 * Description: Reveal and inspect all hidden post meta and custom fields for any post type in a handy metabox.
 * Version: 1.0.0
 * Author: Tom de Visser
 * Author URI: https://tomdevisser.dev/
 * Tested up to: 6.8
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * License: GNU General Public License v2.0 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: revelio
 */

/**
 * Exit if accessed directly.
 */
defined( 'ABSPATH' ) or die;

/**
 * Plugin version, used for cache-busting assets.
 */
define( 'REVELIO_VERSION', '1.0.0' );

/**
 * Absolute path to the plugin directory.
 */
define( 'REVELIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'REVELIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin settings.
 */
require_once REVELIO_PLUGIN_DIR . 'includes/settings.php';

/**
 * Load editor-specific functionality — classic or block editor.
 */
if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
	require_once REVELIO_PLUGIN_DIR . 'includes/classic-editor.php';
} else {
	require_once REVELIO_PLUGIN_DIR . 'includes/block-editor.php';
}
