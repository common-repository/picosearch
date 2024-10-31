<?php
/**
 * Picosearch
 *
 * A better WordPress search plugin
 *
 * @since             1.0.0
 *
 * Plugin Name:     Picosearch
 * Plugin URI:      https://github.com/picocodes/picosearch
 * Description:     Shows your users relevant search results, highlights search terms, suggests search terms and logs search queries among other features to your in-built search engine
 * Author:          Picocodes
 * Author URI:      https://github.com/picocodes
 * Version:         1.0.5
 * Text Domain:     picosearch
 * License:         GPL3+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     /languages
 *
 * @author          Picocodes
 * @author          Kaz
 * @license         GNU General Public License, version 3
 * @copyright       Picocodes
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    die;
}

    /**
     * picosearch main class
     *
     * @since       1.0.0
     */

    if ( ! class_exists( 'picosearch' ) ) {

        /**
         * Main picosearch class
         *
         * @since       1.0.0
         */
        class picosearch {

            /**
             * @var       Plugin version
             * @since       1.0.0
             */

            public $version = '1.0.5';

            /**
             * @access      private
             * @var        obj $instance The one true picosearch
             * @since       1.0.0
             */
            private static $instance = null;
			
			 /**
			 * Reference to the core search class
             * @access      public
             * @since       1.0.0
             */
            public $search = null;
			
			/**
			 * Local path to this plugins root directory
             * @access      public
             * @since       1.0.0
             */
            public $plugin_path = null;
			
			/**
			 * Web path to this plugins root directory
             * @access      public
             * @since       1.0.0
             */
            public $plugin_url = null;
			
			/**
			 * Index table
             * @access      public
             * @since       1.0.0
             */
            public $index_table = null;
			
			/**
			 * Query log table
             * @access      public
             * @since       1.0.0
             */
            public $log_table = null;
			
			/**
			 * Doc map table
             * @access      public
             * @since       1.0.0
             */
            public $doc_table = null;
			
            /**
             * Get active instance
             *
             * @access      public
             * @since       1.0.0
             * @return      self::$instance The one true picosearch
             */
            public static function instance() {
				
                if ( is_null( self::$instance ) )
                    self::$instance = new self();									
				
                return self::$instance;
            }
			
			/**
			 * Class Constructor.
			 */
			public function __construct() {
				global $wpdb;
				
				//Set global variables
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
				$this->plugin_url = plugins_url( '/', __FILE__ );
				$this->index_table = $wpdb->prefix . "picosearch_index";
				$this->log_table = $wpdb->prefix . "picosearch_log";
				$this->doc_table = $wpdb->prefix . "picosearch_docs";
				
				
				// Include core files
				$this->includes();
				
				// Confirm current db version
				$this->db_version = get_option('picosearch_db_version', '0.0.0');				
				if( $this->db_version == '0.0.0' ){
					$this->create_tables();
					update_option('picosearch_db_version', $this->version);
					$this->db_version = get_option('picosearch_db_version', '1.0.0');
				}
				
				//initialize hooks
				$this->init_hooks();
				
				do_action('picosearch_loaded');
			}

            /**
             * Include necessary files
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            private function includes() {
												
				// Core functions
                require_once $this->plugin_path . 'includes/functions.php';
												
				//Stop words
				require_once $this->plugin_path . 'includes/stopwords.php';
				
				//Bg Indexer
				require_once $this->plugin_path . 'includes/indexer.php';
				
				// Core hooks
                require_once $this->plugin_path . 'includes/hooks.php';
				
                // Admin options
                require_once $this->plugin_path . 'wp-elements/admin.php';
            }

            /**
             * Run action and filter hooks
             *
             * @access      private
             * @since       1.0.0
             * @return      void
             */
            private function init_hooks() {

                // Load plugin text domain
                add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
				
				// Print the ajax footprint
				add_action( 'picosearch_front', array( $this, 'print_ajax' ) );
				
				// Optionally index
                add_action( 'init', array( $this, 'maybe_index' ) );
            }
			
			/**
             * Loads the textdomain
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            public function load_plugin_textdomain() {
				
                load_plugin_textdomain(
					'ajax-live-search',
					false,
					$this->plugin_path . 'languages/'
				);
				
            }
			
			/**
             * Prints html element for ajax manipulation
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            public function print_ajax() {				
                echo '<span id="picosearch-live-init-ajlive"></span>';				
            }
			
			/**
             * Optionally runs the bg indexer
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            public function maybe_index() {
				
				$indexer = new Picosearch_indexer();
				if ( ! get_option('picosearch_indexed', '0') && !$indexer->is_indexing() )
					picosearch_index_posts();	
				
            }
			
			/**
             * Provides a wrapper to wp_elements
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            public function elements( $id= 'picosearch') {				
                return WP_Elements_Picosearch::instance($id);				
            }
			
			/**
             * Creates the necessary db tables
             *
             * @access      public
             * @since       1.0.0
             * @return      void
             */
            public function create_tables() {
				global $wpdb;
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$charset_collate_bin_column = '';
				$charset_collate = '';

				if (!empty($wpdb->charset)) {
    				$charset_collate_bin_column = "CHARACTER SET $wpdb->charset";
					$charset_collate = "DEFAULT $charset_collate_bin_column";
				}
				
				if (strpos($wpdb->collate, "_") > 0) {
    				$charset_collate_bin_column .= " COLLATE " . substr($wpdb->collate, 0, strpos($wpdb->collate, '_')) . "_bin";
    				$charset_collate .= " COLLATE $wpdb->collate";
				} else {
					
    				if ($wpdb->collate == '' && $wpdb->charset == "utf8") {
	    				$charset_collate_bin_column .= " COLLATE utf8_bin";
					}
					
				}
				
				//Create the searches log table
				$sql = "CREATE TABLE IF NOT EXISTS {$this->log_table} (id bigint(9) NOT NULL AUTO_INCREMENT, 
					query TEXT NOT NULL,
					hits INT(9) NOT NULL DEFAULT '0',
					searches INT(9) NOT NULL DEFAULT '1',
					results INT(9) NOT NULL DEFAULT '1',
					time timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					UNIQUE KEY id (id)) $charset_collate;";
				
				dbDelta($sql);
				
				//The index table
				$sql = "CREATE TABLE IF NOT EXISTS {$this->index_table} (id bigint(9) NOT NULL AUTO_INCREMENT, 
					post_id INT(9) NOT NULL DEFAULT '0',
					word varchar(200) NOT NULL,
					title INT(9) NOT NULL DEFAULT '0',
					content INT(9) NOT NULL DEFAULT '0',
					excerpt INT(9) NOT NULL DEFAULT '0',
					UNIQUE KEY id (id)) $charset_collate;";

				dbDelta($sql);
				
				//The docs table
				$sql = "CREATE TABLE IF NOT EXISTS {$this->doc_table} (id bigint(9) NOT NULL,  
					comment_count INT(9) NOT NULL DEFAULT '0',
					content_length INT(9) NOT NULL DEFAULT '0',
					title_length INT(9) NOT NULL DEFAULT '0',
					last_modified timestamp NOT NULL,
					post_type INT(9) NOT NULL DEFAULT '0',
					excerpt_length INT(9) NOT NULL DEFAULT '0',
					author INT(9) NOT NULL DEFAULT '0',
					UNIQUE KEY id (id)) $charset_collate;";

				dbDelta($sql);
            }
        }
    }
	
function picosearch() {
	return picosearch::instance();
}

picosearch();