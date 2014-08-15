<?php
/**
 * IFTTT Bridge for WordPress
 *
 * @package   Ifttt_Bridge
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @package Ifttt_Bridge
 * @author  Björn Weinbrenner <info@bjoerne.com>
 */
class Ifttt_Bridge {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Log levels.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	static $log_levels = array( 'debug', 'info', 'warn', 'error', 'off' );

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $plugin_slug = 'ifttt-bridge';

	/**
	 * Instance of this class.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since   1.0.0
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'xmlrpc_call', array( $this, 'bridge' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since   1.0.0
	 *
	 * @return  Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since   1.0.0
	 *
	 * @return  object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Receives an incoming xmlrpc call, extract the payload data and performs 'ifttt_bridge' action.
	 *
	 * @since   1.0.0
	 */
	public function bridge( $method ) {
		$options = get_option( 'ifttt_bridge_options' );
		$this->init_log_level( $options );
		$this->log( 'info', 'xmlrpc call received' );
		$ifttt_bridge_request = false;
		try {
			if ( $method != 'metaWeblog.newPost' ) {
				$this->log( 'info', "Method $method not relevant" );
				return;
			}
			$message = $this->create_message();
			$content_struct = $message->params[3];
			if ( ! $this->contains_ifttt_bridge_tag( $content_struct ) ) {
				$this->log( 'info', "Tag 'ifttt_bridge' not found" );
				return;
			}
			$ifttt_bridge_request = true;
			if ( $this->log_level_enabled( 'info' ) ) {
				$content_struct_log  = "Received data:\n";
				$content_struct_log .= '  title: ' . $content_struct['title'] . "\n";
				$content_struct_log .= '  description: ' . $content_struct['description'] . "\n";
				$content_struct_log .= '  post_status: ' . $content_struct['post_status'] . "\n";
				$content_struct_log .= '  categories: ' . implode( ', ', $content_struct['categories'] ) . "\n";
				$content_struct_log .= '  mt_keywords: ' . implode( ', ', $content_struct['mt_keywords'] );
				$this->log( 'info', $content_struct_log );
			}
			do_action( 'ifttt_bridge', $content_struct );
			$this->log( 'info', "Successfully called 'ifttt_bridge' actions" );
		} catch (Exception $e) {
			$this->log( 'error', 'An error occurred: ' . $e->getMessage() );
		}
		if ( $ifttt_bridge_request ) {
			header( 'Content-Type: text/xml; charset=UTF-8' );
			readfile( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'default_response.xml' );
			die();
		}
	}

	/**
	 * Creates a IXR_Message from the incoming data.
	 *
	 * @since   1.0.0
	 */
	private function create_message() {
		global $HTTP_RAW_POST_DATA;
		if ( empty( $HTTP_RAW_POST_DATA ) ) {
			// workaround for a bug in PHP 5.2.2 - http://bugs.php.net/bug.php?id=41293
			$data = file_get_contents( 'php://input' );
		} else {
			$data =& $HTTP_RAW_POST_DATA;
		}
		if ( $this->log_level_enabled( 'debug' ) ) {
			$this->log( 'debug', "Received request:\n" . htmlspecialchars( $data ) );
		}
		$message = new IXR_Message( $data );
		$message->parse();
		return $message;
	}

	/**
	 * Decides if the incoming request if relevant. A tag 'ifttt_bridge' must be used.
	 *
	 * @since   1.0.0
	 */
	private function contains_ifttt_bridge_tag( $content_struct ) {
		if ( ! array_key_exists( 'mt_keywords', $content_struct ) ) {
			return false;
		}
		$tags = $content_struct['mt_keywords'];
		foreach ( $tags as $tag ) {
			if ( $tag == 'ifttt_bridge' ) {
				return true;
			}
		}
		return false;
	}

	private function init_log_level( $options ) {
		$this->log_level = $options && array_key_exists( 'log_level', $options ) ? $options['log_level'] : 'off';
		foreach ( self::$log_levels as $i => $level ) {
			if ( $level == $this->log_level ) {
				$this->log_level_index = $i;
				break;
			}
		}
	}

	/**
	 * Returns if the logging is enabled for the given log level.
	 *
	 * @since   1.0.0
	 */
	private function log_level_enabled( $level ) {
		foreach ( self::$log_levels as $i => $available_level ) {
			if ( $level == $available_level ) {
				return $i >= $this->log_level_index;
			}
		}
		throw new Exception( 'Log level not found ' . $level );
	}

	/**
	 * Logs the given message to the log of this plugin.
	 *
	 * @since   1.0.0
	 */
	private function log( $level, $message ) {
		if ( ! $this->log_level_enabled( $level ) ) {
			return;
		}
		$log_entry = array(
			'time'    => time(),
			'level'   => $level,
			'message' => $message,
		);
		$log_entries = get_option( 'ifttt_bridge_log' );
		if ( $log_entries ) {
			if ( count( $log_entries ) == 30 ) {
				array_shift( $log_entries );
			}
			array_push( $log_entries, $log_entry );
			update_option( 'ifttt_bridge_log', $log_entries );
		} else {
			$log_entries = array( $log_entry );
			add_option( 'ifttt_bridge_log', $log_entries );
		}
	}
}
