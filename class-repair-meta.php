<?php
/**
 * Pilau Repair Meta
 *
 * @package   Pilau_Repair_Meta
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2016 Steve Taylor
 */

/**
 * Plugin class
 *
 * @package Pilau_Repair_Meta
 * @author  Steve Taylor
 */
class Pilau_Repair_Meta {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1
	 *
	 * @var     string
	 */
	protected $version = '0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'repair-meta';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * The plugin's settings.
	 *
	 * @since    0.1
	 *
	 * @var      array
	 */
	protected $settings = null;

	/**
	 * Capability needed to use this plugin
	 *
	 * @since    0.1
	 *
	 * @var      array
	 */
	protected $required_cap = 'update_core';

	/**
	 * The path of the file to store old values (temporarily)
	 *
	 * @since    0.1
	 *
	 * @var      array
	 */
	protected $backup_file_path = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		// Set the settings
		//$this->settings = $this->get_settings();

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the settings page and menu item.
		//add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		//add_action( 'admin_init', array( $this, 'process_plugin_admin_settings' ) );

		// Load admin styles and scripts
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing scripts and styles
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Define custom functionality
		add_action( 'admin_init', array( $this, 'admin_init' ), 0 );
		add_action( 'admin_init', array( $this, 'process_repair_request' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

	}

	/**
	 * Admin init
	 */
	public function admin_init() {

		// Set the path for the backup file
		$this->backup_file_path = trailingslashit( WP_CONTENT_DIR ) . 'uploads/' . 'pilau_repair_meta_old_value.bak';

	}

	/**
	 * Output admin notices
	 */
	public function admin_notices() {

		if ( ! empty( $_GET['prm'] ) ) {
			$post_id = ! empty( $_GET['id'] ) ? (int) $_GET['id'] : null;
			$post_title = get_the_title( $post_id );
			$filename = basename( $this->backup_file_path );
			?>
			<div class="updated">
				<p><?php printf( __( "The meta for post ID %d - '%s' - was repaired. The old values can be found in the file <code>%s</code> in the uploads directory." ), $post_id, $post_title, $filename ); ?></p>
			</div>
			<?php
		}

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Repair Meta', $this->plugin_slug ),
			__( 'Repair Meta', $this->plugin_slug ),
			$this->required_cap,
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Get the plugin's settings
	 *
	 * @since    0.1
	 */
	public function get_settings() {

		$settings = get_option( $this->plugin_slug . '_settings' );

		// Defaults
		$defaults = array(
		);
		$settings = array_merge( $defaults, $settings );

		return $settings;
	}

	/**
	 * Set the plugin's settings
	 *
	 * @since    0.1
	 */
	public function set_settings( $settings ) {
		return update_option( $this->plugin_slug . '_settings', $settings );
	}

	/**
	 * Process the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function process_plugin_admin_settings() {

		// Submitted?
		if ( isset( $_POST[ $this->plugin_slug . '_settings_admin_nonce' ] ) && check_admin_referer( $this->plugin_slug . '_settings', $this->plugin_slug . '_settings_admin_nonce' ) ) {

			// Gather into array
			$settings = array();

			// Save as option
			$this->set_settings( $settings );

			// Redirect
			wp_redirect( admin_url( 'options-general.php?page=' . $this->plugin_slug . '&done=1' ) );

		}

	}

	/**
	 * Add actions to admin lists
	 *
	 * @param	array	$actions
	 * @param	object	$post
	 * @return	array
	 */
	public function post_row_actions( $actions, $post ) {
		if ( current_user_can( $this->required_cap ) ) {
			$request_url = wp_nonce_url( get_admin_url( null, '/?pilau_repair_meta=1&amp;id=' . $post->ID ), 'pilau_repair_meta', 'prm_nonce' );
			$actions['repair_meta'] = '<a href="' . esc_url( $request_url ) . '">' . __( 'Repair meta' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Process requests to repair meta
	 */
	public function process_repair_request() {
		global $wpdb;

		// Verify nonce and user capability
		if ( ! empty( $_REQUEST['pilau_repair_meta'] ) && ( ! empty( $_REQUEST['prm_nonce'] ) && wp_verify_nonce( $_REQUEST['prm_nonce'], 'pilau_repair_meta' ) ) && current_user_can( $this->required_cap ) ) {
			$post_id = (int) $_REQUEST['id'];

			// Remove last backup file
			if ( file_exists( $this->backup_file_path ) ) {
				unlink( $this->backup_file_path );
			}

			// Get all of the post's meta
			$all_meta = $wpdb->get_results( $wpdb->prepare("
				SELECT		*
				FROM		$wpdb->postmeta
				WHERE		post_id		= %d
			", $post_id) );

			// Go through each item
			foreach ( $all_meta as $item ) {

				// Only bother with serialized arrays
				if ( strlen( $item->meta_value ) > 2 && substr( $item->meta_value, 0, 2 ) == 'a:' ) {
					// See if it's corrupted
					$data = @unserialize( $item->meta_value );
					if ( $data === false ) {
						// Store backup of old value
						file_put_contents( $this->backup_file_path, $item->meta_key . ":\n\n" . $item->meta_value . "\n\n", FILE_APPEND );
						// Update
						$new_value = $this->repair_serialized_array( $item->meta_value );
						update_post_meta( $item->post_id, $item->meta_key, $new_value );
					}
				}

			}

			// Redirect with query var to display message
			wp_redirect(
				add_query_arg(
					array(
						'prm'	=> 1,
						'id'	=> $post_id
					),
					wp_get_referer()
				)
			);
			exit;

		}

	}


	/**
	 * Extract what remains from an unintentionally truncated serialized string
	 *
	 * Based on:
	 * @link	http://www.thecodify.com/php/repair-a-serialized-array/
	 *
	 * Example Usage:
	 *
	 * the native unserialize() function returns false on failure
	 * $data = @unserialize($serialized); // @ silences the default PHP failure notice
	 * if ($data === false) // could not unserialize
	 * {
	 *   $data = repairSerializedArray($serialized); // salvage what we can
	 * }
	 *
	 * $data contains your original array (or what remains of it).
	 *
	 * @param string $serialized The serialized array
	 */
	public function repair_serialized_array( $serialized ) {
		$tmp = preg_replace( '/^a:\d+:\{/', '', $serialized );
		return $this->repair_serialized_array_r( $tmp ); // operates on and whittles down the actual argument
	}


	/**
	 * The recursive function that does all of the heavy lifing. Do not call directly.
	 * @param string $broken The broken serialized array
	 * @return string Returns the repaired string
	 */
	public function repair_serialized_array_r( &$broken ) {
		// array and string length can be ignored
		// sample serialized data
		// a:0:{}
		// s:4:"four";
		// i:1;
		// b:0;
		// N;
		$data       = array();
		$index      = null;
		$len        = strlen( $broken );
		$i          = 0;

		while ( strlen( $broken ) ) {
			$i++;
			if ( $i > $len ) {
				break;
			}

			if ( substr( $broken, 0, 1 ) == '}' ) {
				// end of array
				$broken = substr( $broken, 1 );
				return $data;
			} else {
				$bite = substr( $broken, 0, 2 );
				switch( $bite ) {
					case 's:': // key or value
						$re = '/^s:\d+:"([^\"]*)";/';
						if ( preg_match( $re, $broken, $m ) ) {
							if ( $index === null ) {
								$index = $m[1];
							} else {
								$data[$index] = $m[1];
								$index = null;
							}
							$broken = preg_replace( $re, '', $broken );
						}
						break;

					case 'i:': // key or value
						$re = '/^i:(\d+);/';
						if ( preg_match( $re, $broken, $m ) ) {
							if ( $index === null ) {
								$index = (int) $m[1];
							} else {
								$data[$index] = (int) $m[1];
								$index = null;
							}
							$broken = preg_replace( $re, '', $broken );
						}
						break;

					case 'b:': // value only
						$re = '/^b:[01];/';
						if ( preg_match( $re, $broken, $m ) ) {
							$data[$index] = (bool) $m[1];
							$index = null;
							$broken = preg_replace( $re, '', $broken );
						}
						break;

					case 'a:': // value only
						$re = '/^a:\d+:\{/';
						if ( preg_match( $re, $broken, $m ) ) {
							$broken         = preg_replace( '/^a:\d+:\{/', '', $broken );
							$data[$index]   = $this->repair_serialized_array_r( $broken );
							$index			= null;
						}
						break;

					case 'N;': // value only
						$broken			= substr( $broken, 2 );
						$data[$index]	= null;
						$index			= null;
						break;
				}
			}
		}

		return $data;
	}

}