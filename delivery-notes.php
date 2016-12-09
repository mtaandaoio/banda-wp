<?php
/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Base class
 */
if ( !class_exists( 'Banda_Delivery_Notes' ) ) {

	final class Banda_Delivery_Notes {

		/**
		 * The single instance of the class
		 */
		protected static $_instance = null;
	
		/**
		 * Default properties
		 */
		public static $plugin_version;
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $plugin_basefile_path;
		public static $plugin_text_domain;
		
		/**
		 * Sub class instances
		 */
		public $writepanel;
		public $settings;
		public $print;
		public $theme;

		/**
		 * Main Instance
		 */
		public static function instance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	
		/**
		 * Cloning is forbidden
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'banda-delivery-notes' ), '4.1' );
		}
	
		/**
		 * Unserializing instances of this class is forbidden
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'banda-delivery-notes' ), '4.1' );
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->define_constants();
			$this->init_hooks();
			
			// Send out the load action
			do_action( 'wcdn_load');
		}

		/**
		 * Hook into actions and filters
		 */
		public function init_hooks() {
			add_action( 'init', array( $this, 'localise' ) );
			add_action( 'banda_init', array( $this, 'load' ) );
		}

		/**
		 * Define WC Constants
		 */
		private function define_constants() {
			self::$plugin_version = '4.1.5';
			self::$plugin_prefix = 'wcdn_';
			self::$plugin_basefile_path = __FILE__;
			self::$plugin_basefile = plugin_basename( self::$plugin_basefile_path );
			self::$plugin_url = plugin_dir_url( self::$plugin_basefile );
			self::$plugin_path = trailingslashit( dirname( self::$plugin_basefile_path ) );	
			self::$plugin_text_domain = trim( dirname( self::$plugin_basefile ) );
		}
		
		/**
		 * Include the main plugin classes and functions
		 */
		public function include_classes() {
			include_once( 'includes/class-wcdn-print.php' );
			include_once( 'includes/class-wcdn-settings.php' );
			include_once( 'includes/class-wcdn-writepanel.php' );
			include_once( 'includes/class-wcdn-theme.php' );
		}

		/**
		 * Function used to init Template Functions.
		 * This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			include_once( 'includes/wcdn-template-functions.php' );
			include_once( 'includes/wcdn-template-hooks.php' );
		}
		
		/**
		 * Load the localisation 
		 */
		public function localise() {							
			// Load language files from the wp-content/languages/plugins folder
			$mo_file = WP_LANG_DIR . '/plugins/' . self::$plugin_text_domain . '-' . get_locale() . '.mo';
			if( is_readable( $mo_file ) ) {
				load_textdomain( self::$plugin_text_domain, $mo_file );
			}

			// Otherwise load them from the plugin folder
			load_plugin_textdomain( self::$plugin_text_domain, false, dirname( self::$plugin_basefile ) . '/languages/' );
		}
		
		/**
		 * Load the main plugin classes and functions
		 */
		public function load() {
			// Banda activation required
			if ( $this->is_banda_activated() ) {	
				// Include the classes	
				$this->include_classes();
							
				// Create the instances
				$this->print = new Banda_Delivery_Notes_Print();
				$this->settings = new Banda_Delivery_Notes_Settings();
				$this->writepanel = new Banda_Delivery_Notes_Writepanel();
				$this->theme = new Banda_Delivery_Notes_Theme();

				// Check for database updates
				$this->update();

				// Load the hooks for the template after the objetcs.
				// Like this the template has full access to all objects.
				add_filter( 'plugin_action_links_' . self::$plugin_basefile, array( $this, 'add_settings_link') );
				add_action( 'admin_init', array( $this, 'update' ) );
				add_action( 'init', array( $this, 'include_template_functions' ) );
				
				// Send out the init action
				do_action( 'wcdn_init');
			}
		}
				
		/**
		 * Install or update the default settings
		 */
		public function update() {
			// Define default settings
			if( get_option( self::$plugin_prefix . 'version' ) != self::$plugin_version ) {
				// Print slug for the frontend
				$endpoint = get_option( self::$plugin_prefix . 'print_order_page_endpoint' );
				if( !$endpoint ) {
					update_option( self::$plugin_prefix . 'print_order_page_endpoint', 'print-order' );
	
					// Flush the rewrite rules when a fresh install
					set_transient( self::$plugin_prefix . 'flush_rewrite_rules', true );
				}
				
				// Template types
				foreach( Banda_Delivery_Notes_Print::$templates as $template ) {
					// Enable 'invoice' and 'delivery_note' by default
					if( $template['type'] == 'invoice' || $template['type'] == 'delivery-note' ) {
						$option = get_option( self::$plugin_prefix . 'template_type_' . $template['type'] );
						if( !$option ) {
							update_option( self::$plugin_prefix . 'template_type_' . $template['type'], 1 );
						}
					}
				}
				
				// Update the settings to the latest version
				update_option( self::$plugin_prefix . 'version', self::$plugin_version );
			}
		}
		
		/**
		 * Add settings link to plugin page
		 */
		public function add_settings_link( $links ) {
			$url = esc_url( admin_url( add_query_arg( array( 'page' => 'wc-settings', 'tab' => $this->settings->tab_name ), 'admin.php' ) ) );
			$settings = sprintf( '<a href="%s" title="%s">%s</a>' , $url, __( 'Go to the settings page', 'banda-delivery-notes' ) , __( 'Settings', 'banda-delivery-notes' ) );
			array_unshift( $links, $settings );
			return $links;	
		}
				
		/**
		 * Check if banda is activated
		 */
		public function is_banda_activated() {
			$blog_plugins = get_option( 'active_plugins', array() );
			$site_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$banda_basename = plugin_basename( WC_PLUGIN_FILE );
					
			if( ( in_array( $banda_basename, $blog_plugins ) || isset( $site_plugins[$banda_basename] ) ) && version_compare( WC_VERSION, '2.1', '>=' )) {
				return true;
			} else {
				return false;
			}
		}
		
	}
}

/**
 * Returns the main instance of teh plugin to prevent the need to use globals
 */
function WCDN() {
	return Banda_Delivery_Notes::instance();
}

/**
 * Global for backwards compatibility
 */
$GLOBALS['wcdn'] = WCDN();

?>