<?php
/**
 * IFTTT Bridge for WordPress
 *
 * @package   Ifttt_Bridge_Admin
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package Ifttt_Bridge_Admin
 * @author  Björn Weinbrenner <info@bjoerne.com>
 */
class Ifttt_Bridge_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Ifttt_Bridge::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_options_setting' ) );
		add_action( 'admin_post_sent_post_request', array( $this, 'send_test_request' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'IFTTT Bridge for WordPress', $this->plugin_slug ),
			__( 'IFTTT Bridge for WordPress', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		$options           = get_option( 'ifttt_bridge_options' );
		$log               = get_option( 'ifttt_bridge_log', array() );
		$this->log_level   = $options && array_key_exists( 'log_level', $options ) ? $options['log_level'] : 'off';
		$this->log_entries = array();
		foreach ( $log as $log_entry ) {
			$this->log_entries[] = array(
				'time' => date_i18n( _x( 'Y/m/d h:i:s A', 'Date time pattern', $this->plugin_slug ) ),
				'level' => $log_entry['level'],
				'message' => $log_entry['message'],
			);
		}
		$this->send_test_request_url = get_site_url() . '/wp-content/plugins/ifttt-bridge/send_test_request.php';
		include_once( 'views/admin.php' );
	}

	/**
	 * Registers the settings.
	 *
	 * @since    1.0.0
	 */
	public function register_options_setting() {
		register_setting( 'ifttt_bridge_options_group', 'ifttt_bridge_options', array( $this, 'validate_options' ) );
	}

	/**
	 * Validates the options. Clears the log if log has been disabled.
	 *
	 * @since    1.0.0
	 */
	public function validate_options( $options ) {
		$log_entries = get_option( 'ifttt_bridge_log' );
		if ( $log_entries ) {
			$option_log_level = $options['log_level'];
			$new_log_entries  = array();
			foreach ( $log_entries as $log_entry ) {
				$entry_log_level = $log_entry['level'];
				foreach ( Ifttt_Bridge::$log_levels as $available_level ) {
					if ( $available_level == $option_log_level ) {
						$new_log_entries[] = $log_entry;
						continue;
					}
					if ( $available_level == $entry_log_level ) {
						break;
					}
				}
			}
			update_option( 'ifttt_bridge_log', $new_log_entries );
		}
		return $options;
	}

	/**
	 * Send a test request to this WordPress instance.
	 */
	public function send_test_request() {
		$url = get_site_url() . '/xmlrpc.php';
		$variables = array(
			'username' => stripslashes( $_POST['test-request-username'] ),
			'password' => stripslashes( $_POST['test-request-password'] ),
			'title' => stripslashes( $_POST['test-request-title'] ),
			'description' => stripslashes( $_POST['test-request-body'] ),
			'post_status' => @$_POST['test-request-draft'] == 1 ? 'draft' : 'publish',
			'categories' => stripslashes( $_POST['test-request-categories'] ),
			'tags' => stripslashes( $_POST['test-request-tags'] ),
		);
		$template = file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'test_request_template.xml' );
		$options = array( 'body' => $this->create_xml( $template, $variables ) );
		$response = wp_safe_remote_post( $url, $options );
		add_settings_error( null, null, _x( 'Test request sent', 'Success message', $this->plugin_slug ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		$goback = add_query_arg( 'settings-updated', 'true',  wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Creates an xml from a template by replacing placeholders and duplicating nodes if necessary.
	 */
	private function create_xml( $xml_template, $variables ) {
		$doc = new DOMDocument();
		$doc->loadXML( $xml_template );
		$xpath = new DOMXPath( $doc );
		$xpath->query( '/methodCall/params/param[2]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['username'];
		$xpath->query( '/methodCall/params/param[3]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['password'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="title"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['title'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="description"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['description'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="post_status"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['post_status'];
		$categories = array_map( 'trim', explode( ',', $variables['categories'] ) );
		if ( ! empty( $categories ) && $categories[0] != '' ) {
			$categories_data = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data' )->item( 0 );
			$category_value  = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data/value', $categories_data )->item( 0 );
			for ( $i = 1; $i < count( $categories ); $i++ ) {
				$new_category_value = $category_value->cloneNode( true );
				$categories_data->appendChild( $new_category_value );
			}
			for ( $i = 0; $i < count( $categories ); $i++ ) { 
				$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data/value[' . ($i + 1) . ']/string' )->item( 0 )->firstChild->nodeValue = $categories[$i];
			}
		} else {
			$categories = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]' )->item( 0 );
			$categories->parentNode->removeChild( $categories );
		}
		$tags = array_map( 'trim', explode( ',', $variables['tags'] ) );
		if ( ! empty( $tags ) && $tags[0] != '' ) {
			array_unshift( $tags, 'ifttt_bridge' );
		} else {
			$tags = array( 'ifttt_bridge' );
		}
		$mt_keywords_data = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data' )->item( 0 );
		$tag_value = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data/value' )->item( 0 );
		for ( $i = 1; $i < count( $tags ); $i++ ) {
			$new_tag_value = $tag_value->cloneNode( true );
			$mt_keywords_data->appendChild( $new_tag_value );
		}
		for ( $i = 0; $i < count( $tags ); $i++ ) { 
			$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data/value[' . ($i + 1) . ']/string' )->item( 0 )->firstChild->nodeValue = $tags[$i];
		}
		return $doc->saveXML();
	}
}
