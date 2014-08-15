<?php
/**
 * @package   Ifttt_Bridge
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 *
 * @wordpress-plugin
 * Plugin Name:       IFTTT Bridge for WordPress
 * Plugin URI:        http://www.bjoerne.com
 * Description:       IFTTT Bridge for WordPress is a plugin that allows you to display IFTTT-processed data on your WordPress site in any way you like.
 * Version:           1.0.0
 * Author:            Björn Weinbrenner
 * Author URI:        http://www.bjoerne.com/
 * Text Domain:       ifttt-bridge
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/bjoerne2/ifttt-bridge
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/class-ifttt-bridge.php' );

add_action( 'plugins_loaded', array( 'Ifttt_Bridge', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-ifttt-bridge-admin.php' );
	add_action( 'plugins_loaded', array( 'Ifttt_Bridge_Admin', 'get_instance' ) );
}
