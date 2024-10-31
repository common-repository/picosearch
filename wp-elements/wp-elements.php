<?php

/**
 * WP Elements Class
 *
 * Rename the WP_Elements class & the WE() function to something unique
 *
 */

if ( ! defined( 'ABSPATH' ) ) {	
	exit; // Exit if accessed directly.
}

// Incase the class is already loaded

if ( class_exists( 'WP_Elements_Picosearch' ) ) {
	return;
}


/**
 * Main WP_Elements Class.
 *
 * @class WP_Elements
 * @version	0.1.0
 */
class WP_Elements_Picosearch {

	/**
	 * Current version.
	 *
	 * @var string
	 */
	public $version = '0.1.0';

	/**
	 * The single instance of the class.
	 *
	 * @var Object
	 * @since 0.1.0
	 */
	protected static $_instance = null;
	
	/**
	 * Unique registered instances (ids)
	 *
	 * This is simply an array of options 
	 * belonging to each unique id passed along when calling WE()
	 *
	 * @var array
	 */
	protected $instances = array();
	
	/**
	 * Unique hook suffixes
	 *
	 * This is simply an array of hooks suffixes
	 * belonging to each unique id passed along when calling WE()
	 *
	 * Used to conditionally load assets
	 * @see self::set_instance_args()
	 * @var array
	 */
	protected $hook_suffixes = array();
	
	/**
	 * An array of all registered elements
	 *
	 * @see self::register_element_type()
	 * @var array
	 */
	protected $elements = array();
	
	/**
	 * Current instance id
	 * Evaluates to the value passed onto WE() when accessing this object
	 *
	 * @see self::instance()
	 * @var string
	 */
	protected $instance_id = false;
	
	/**
	 * The plugin base url
	 * @see self::set_base_url()
	 * @var string
	 */
	public $base_url;
	
	/**
	 * Callbacks used to retrieve custom form data
	 * @see self::get_data()
	 * @var string
	 */
	public $data_callbacks = array();

	/**
	 * Main WP-Elements Instance.
	 *
	 * Ensures only one instance of WP-Elements is loaded or can be loaded.
	 * 
	 * @since 0.1.0
	 * @return WP-Elements - Main instance.
	 */
	public static function instance( $instance_id = false ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		// If an id is provided and does not exist in the list of instances; add it
		if ( $instance_id !== false && !isset( self::$_instance->instances[$instance_id] )) {
			self::$_instance->instances[$instance_id] = array();
		}
		
		// Change the value of the current instance id since most functions depend on it 

		self::$_instance->instance_id = $instance_id;
		return self::$_instance;
	}


	/**
	 * WP-Elements Constructor.
	 *
	 */
	public function __construct() {
		
		$this->register_core_elements();
		$this->register_core_data_callbacks();
		$this->base_url = plugins_url( '/', __FILE__ );
		
	}
	
	/** Set special arguments for the instance
	 * $wp_now value to check against
	 * @since  0.1.0
	 */
	public function set_instance_args( $args ) {
		if ( $this->instance_id === false OR !is_array( $args ) )
			return;
		
		if ( isset( $args['hook_suffix'] ) &&  $args['hook_suffix'] ) {
			$hook_suffix = $args['hook_suffix'];
			
			if ( isset( $args['element_types'] ) && is_array( $args['element_types'] ) ) {
				
				$this->hook_suffixes[ $hook_suffix ] = $args['element_types'];
				
			} else {
			
				$this->hook_suffixes[ $hook_suffix ] = array();
			
			}
			 
			add_action( 'admin_enqueue_scripts', array( $this, 'enqeue_scripts'), 5);
		}
		
		$this->instance_args[ $this->instance_id ] = $args;
	}
	/**
	 * Adds stylesheets to the queue
	 * @since  0.1.0
	 */
	public function enqeue_scripts( $hook_suffix ) {
		
		if (! isset( $this->hook_suffixes[ $hook_suffix ] ) )
			return;
		
		wp_enqueue_script( 'bootstrap', $this->base_url . 'assets/js/bootstrap.min.js', array( 'jquery', 'tether' ), '4.0.0', true );
		wp_enqueue_script( 'tether', $this->base_url . 'assets/js/tether.min.js', array( ), '4.0.0', true );
	
		//We use a modified version of bootstrap so we cant register it as bootstrap 
		wp_enqueue_style( 'wpe_bootstrap', $this->base_url . 'assets/css/bootstrap.css');
		
		//Finally enque additional styles needed by the current hook_suffix
			
		foreach ( $this->hook_suffixes[ $hook_suffix ] as $element ) {
			if ( isset ( $this->elements[$element]['enque'] ) )
				call_user_func( $this->elements[$element]['enque'] );
		}
		
		//This should be enqued last
		wp_enqueue_script( 'wp_elements', $this->base_url . 'assets/js/wp-elements.js', array( 'bootstrap', 'underscore'), '0.1.0', true );
		
		$wpe_i18n = array( 
			'emptyData' => __( 'Please provide the import data.', 'picosearch' ),
			'emptyJson' => __( 'You provided an empty object so nothing was imported.', 'picosearch' ),
			'badFormat' => __( 'The data you provided does\'nt seem to be well formatted. Try reimporting it again.', 'picosearch' ),
			'importing' => __( 'Importing data...', 'picosearch' ),
			'finalising' => __( 'Almost done.', 'picosearch' ),
			'finished' => __( 'Done. Please wait until the page reloads.', 'picosearch' ),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'wpeAjaxNonce' => wp_create_nonce('wpeAjaxNonce'),
			'importAction' => 'wpe_import_data',
			'ajaxError' => __( 'Unable to connect to the server. Try again later.', 'picosearch' ),
		);
		
		wp_localize_script( 'wp_elements', 'wpElements', $wpe_i18n );
	}
	
	/**
	 * gets a user defined option
	 */
	public function get_option( $option = false, $default = false, $mode = 'normal' ) {
		
		if( $option === false OR $this->instance_id === false)
			return $default;
		
		$instance = $this->instances[$this->instance_id];
		
		if ( isset( $instance['option_data'][$mode] ) ) {
			
			$options = $instance['option_data'][$mode];
			
		}else{
			
			$options = $this->merge_options( $mode );
			$this->instances[$this->instance_id]['option_data'][$mode] = $options;
			
		}
		
		if (!isset ($options[$option]) OR is_null ( $options[$option] ) )			
			return $default;
		
		return wp_unslash ( $options[$option] );
	}
	
	/**
	 * Returns a list of all registered elements
	 *
	 */
	public function get_registered_elements(  ) {
		return array_keys( $this->elements );
	}
	
	/**
	 * Registers a new element
	 *
	 */
	public function register_element( $element_type = false, $args = array() ) {
		if( $element_type !== false )
			$this->elements[$element_type] = $args;		
	}
	
	/**
	 * Registers multiple elements at once
	 *
	 */
	public function register_multiple_elements( $args = array() ) {
		if( !is_array( $args) )
			return;		
		
		foreach ( $args as $element => $options ) {
				
				$this->register_element( $element, $options );
			
		}
	}
	
	/**
	 * Enques styles for select elements
	 *
	 */
	public function enque_select() {
		wp_enqueue_script( 'selectize', $this->base_url . '/assets/js/selectize.min.js', array( 'jquery' ), '4.0.3', false );	
		wp_enqueue_style( 'selectize.bootstrap3', $this->base_url . '/assets/css/selectize.bootstrap3.css' );
	}
	
	/**
	 * Enques styles for date elements
	 *
	 */
	public function enque_date() {
		wp_enqueue_script( 'wp_datepicker', $this->base_url . '/assets/js/datepicker.min.js', array( 'jquery' ), '0.4.0', true );
		wp_enqueue_style( 'wp_datepicker', $this->base_url . '/assets/css/datepicker.css' );
	}
	
	/**
	 * Enques styles for color elements
	 *
	 */
	public function enque_color() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
	
	/**
	 * A catch all callback that renders default elements
	 *
	 */
	public function default_cb( $element_id, $args ) {
		if ( isset ( $args['type'] ) )
			include "elements/{$args['type']}.php";
	}
	
	/**
	 * Registers core elements
	 *
	 */
	public function register_core_elements() {
		$elements = 'title textarea text multi_text email color date search number password editor tinymce quicktags select multiselect ';
		
		$elements .= 'button save reset import badge pill checkbox button_set radio switch progressbar ';
		
		$elements .= 'list_group card alert raw';
		
		$elements = array_map( 'trim', explode(' ', $elements ) );
		$modified = array();
		
		foreach ($elements as $element) {
			
			// Enque custom css and js for select, date and color elements			
			$modified[$element] = array();
			if( $element == 'select' OR $element == 'multiselect' )
				$modified[$element]['enque'] = array( $this, 'enque_select');
			
			if( $element == 'date' )
				$modified[$element]['enque'] = array( $this, 'enque_date');

			if( $element == 'color')
				$modified[$element]['enque'] = array( $this, 'enque_color');
			
			//Do not render default markup for titles
			if( $element == 'title')				
				$modified[$element]['render_default_markup'] = false;
			
			//Register Callbacks to render core elements
			$modified[$element]['callback'] = array( $this, 'default_cb');

		}

		$this->register_multiple_elements( $modified );
	}
	
	/**
	 * Updates an existing element
	 *
	 */
	public function update_element_type( $element_type = false, $key = false, $value = false ) {
		if( $element_type !== false && isset( $this->elements[$element_type] ) )
			$this->elements[$element_type][$key] = $value;		
	}
	
	/**
	 * Adds an element to the current instance
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function queue_control( $element_id = false, $args = array() ) {
		if( $element_id !== false && $this->instance_id !== false) {
						
			$this->instances[$this->instance_id]['elements'][] = array(
				'id' => $element_id,
				'args' => $args
			);
			
			//Set it in a separate array to allow easy access; no need to loop the above  array
			if ( isset ( $args['default'] )) {
				$this->instances[$this->instance_id]['defaults'][$element_id] = $args['default'];
			}
						
		}
						
	}
	
	/**
	 * Adds an element to the current instance
	 *
	 * @since 0.1.0
	 * @access public
	 */
	private function get_defaults() {	
		if( $this->instance_id !== false 
			&& isset ( $this->instances[$this->instance_id]['defaults'] )
		) {
			return  $this->instances[$this->instance_id]['defaults'];
		}	

		return array();
	}
	
	/**
	 * Adds an element to the current instance
	 *
	 * @since 0.1.0
	 * @access public
	 */
	private function merge_options( $mode = 'normal' ) {
		
		if( $this->instance_id === false )
			return;
		
		$instance = $this->instances[$this->instance_id];
		
		if (!isset( $instance['elements'] ))
			return;
		
		if(! isset ( $instance['user_settings'] ) ) {
			
			$instance['user_settings'] = get_option( $this->instance_id, array() );
			$this->instances[$this->instance_id]['user_settings'] = $instance['user_settings'];
			
		}
		
		$saved_options = $instance['user_settings'];
		$defaults  = $this->get_defaults();
		$posted = array();
		$imported = array();
		
		if ( ! empty( $_POST ) )
			$posted = $_POST;
		
		if ( ! empty( $_POST['wpe-importer-data'] ) )
			$imported = (array) json_decode( wp_unslash(($_POST['wpe-importer-data'])) );
		
		$return = array();
		$merge_sources = array();
		//normal_mode; just mix saved options with defaults and return new array 
		if ( $mode == 'normal' ) {
			$merge_sources = array( $saved_options, $defaults);
		}
		
		//save_mode;
		if ( $mode == 'save' ) {
			$merge_sources = array( $posted );			
		}
		
		//reset_mode;
		if ( $mode == 'reset' ) {
			$merge_sources = array( $defaults );			
		}
		
		//import_mode;
		if ( $mode == 'import' ) {
			$merge_sources = array( $imported, $posted, $saved_options, $defaults );			
		}

		foreach( $instance['elements'] as $element ){
				$id = $element['id'];
				$data = $this->get_data_from_one_of_this_arrays( $id, $merge_sources);
				if (! is_null( $data ) )
					$return[$id] = $data;
		}

		return $return;
	}
	
	/**
	 * Adds an element to the current instance
	 *
	 * @since 0.1.0
	 * @access public
	 */
	private function get_data_from_one_of_this_arrays( $key = false, $arrays = false ) {
		if( $key == false OR $arrays == false ) 
			return null;
		
		if (! is_array( $arrays ) )
			return null;
		
		foreach ( $arrays as $array ) {
			
			if (! is_array( $array ) )
				continue;
			
			if ( isset ( $array[$key] ) )
				return $array[$key];
		}
		
		return null;
	}
	/**
	 * Returns a list of sections
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function element_pluck( $property ) {
		
		$return = array();
		if( $this->instance_id !== false && isset( $this->instances[$this->instance_id]['elements'] )) {			
			
			foreach( $this->instances[$this->instance_id]['elements'] as $element ) {
				
				if ( isset( $element['args'][$property] ) && !empty( $element['args'][$property] ) )
					$return[] = $element['args'][$property];				
			}
						
		}
		return $return;			
	}
	
	/**
	 * Adds a message to the notices array
	 */
	public function add_message( $text = false ) {	
		if( $text !== false && $this->instance_id !== false)
			$this->instances[$this->instance_id]['messages'][] = $text;		
	}
	
	/**
	 * Adds a message to the errors array
	 */
	public function add_error( $text = false ) {		
		if( $text !== false && $this->instance_id !== false)
			$this->instances[$this->instance_id]['errors'][] = $text;		
	}

	/**
	 * Prints either the errors or information messages
	 */
	public function show_messages(  ) {
		
		if( $this->instance_id === false)
			return;
			
		//If we have errors; print them. Else print messages if they are available
		if ( isset( $this->instances[$this->instance_id]['errors'] ) 
			&& count( $this->instances[$this->instance_id]['errors'] ) > 0 ) {
				
			foreach ( $this->instances[$this->instance_id]['errors'] as $error ) {
				
				echo '<div class="error"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
				
			}
			
		} elseif ( isset( $this->instances[$this->instance_id]['messages'] ) 
			&& count( $this->instances[$this->instance_id]['messages'] ) > 0 ) {
				
			foreach ( $this->instances[$this->instance_id]['messages'] as $message ) {
				
				echo '<div class="updated"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
				
			}
			
		}
		
	}	
	
	/**
	 * Sets the rendering template
	 */
	public function set_template( $template = false ) {
		if ( $this->instance_id !== false && $template !== false )
			$this->instances[$this->instance_id]['template'] = $template;		
	}
	
	/**
	 * Outputs the settings page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render(  ) {
		
		if( $this->instance_id === false)
			return;
		
		// Save settings if data has been posted
		if ( ! empty( $_POST ) )			
			$this->save();
					
		$template = 'template.php';
		if ( isset ( $this->instances[ $this->instance_id ]['template'] ) )
			$template = $this->instances[ $this->instance_id ]['template'];
		
		$elements = array();
		if ( isset ( $this->instances[ $this->instance_id ]['elements'] ) )
			$elements = $this->instances[ $this->instance_id ]['elements'];
		
		require_once ( $template );

	}

	/**
	 * Outputs the settings page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_element( $element, $normal_render = true, $cbs=array() ) {
	
		//If no control is provided return early
		if ( !isset ( $element['args'] ) || !isset ( $element['id'] ) )
			return;
		
		$args = $element['args'];
		$element_id = $element['id'];
		$args[ 'id' ] = $element_id;
		
		if ( !isset ( $args['type'] ) || !isset ( $this->elements[ $args['type'] ] ) )
			return;
		
		$element_type = $this->elements[ $args['type'] ];
		$args = $this->clean_args( $element_id, $args );
		
		//Optionally render a default markup
		$default_markup = ( !isset ( $element_type['render_default_markup'] ) || $element_type['render_default_markup'] == true );
		if( $default_markup && $normal_render ) {
			if ( isset( $cbs['wrapper_open_cb']) ) {
				call_user_func( $cbs['wrapper_open_cb'], $element_id, $args );
			} else {
				$this->render_wrapper_open( $element_id, $args );
			}
		}		
		
		//Call the element's render function
		if( isset ( $element_type['callback'] ) )
			call_user_func( $element_type['callback'], $element_id, $args );
		
		if( $default_markup && $normal_render ) {
			
			if ( isset( $cbs['wrapper_end_cb']) ) {
				call_user_func( $cbs['wrapper_end_cb'], $element_id, $args );
			} else {
				$this->render_wrapper_end( $element_id, $args );
			}			
		}
			
	}
	
	/**
	 * Outputs the settings page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_wrapper_open( $element_id, $args ) {
	
		$element_id = esc_attr($element_id);
		$class = 'form-group row';
		
		if( isset ( $args['section'] ) &&  $args['section'] ) {
			
			$class .= ' wp-section-wrapper-' . sanitize_html_class( $args['section'] );
			
		}
		
		echo "<div class='$class'>";
		
		$is_full_field = ( isset( $args['full_width'] ) && $args['full_width'] == true );
		
		$content_class = 'col-md-10 float-right';
		$title_class = 'col-md-3 col-form-label';
		
		if ( isset( $args['title'] ) ) :
		
			$content_class = 'col-md-8 offset-md-1';
			$title = '<strong>' . $args['title']. '</strong>';
			
			if ( isset( $args['subtitle'] ) ) {
				$title .= "<br />{$args['subtitle']}";
			}
			
			if ( $is_full_field )
				$title_class = 'col-12 col-form-label';
			
			echo "<label for='$element_id' class='$title_class'>$title</label>";
			
		endif;
		
		$extra = '';
	
		if ( isset ( $args['hint'] ) ) {
	
			$extra = "data-toggle='tooltip' data-placement='top' title='{$args['hint']}'";
		
		}
	
		
		if ( $is_full_field )
			$content_class = 'col-12';
				
			echo "<div class='$content_class' $extra>";		
		
	}
	
	/**
	 * Outputs the settings page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_wrapper_end( $element_id, $args ) {
		
		echo '</div></div>';

	}
		
	/**
	 * Outputs the settings page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function clean_args( $id, $args ) {
					
		//Data attibutes
		if(! isset( $args['custom_attributes'] )) {
			$args['custom_attributes'] = array();
		}	
		
		$args['_custom_attributes'] = '';
		
		foreach ( $args['custom_attributes'] as $attr => $value ) {
			$attr = esc_attr( $attr );
			$value = esc_attr( $value );
			$args['_custom_attributes'] .= " $attr='$value'";
		}
				
		//Default
		if(! isset( $args['default'] )) {
			$args['default'] = '';
		}
		
		//Description
		if(! isset( $args['description'] )) {
			$args['description'] = '';
		}
		
		//Placeholder
		if(! isset( $args['placeholder'] )) {
			$args['placeholder'] = '';
		}
		
		//Option details for select etc
		if(! isset( $args['options'] )) {
			$args['options'] = array();
		}
		
		//Data args
		if(! isset( $args['data_args'] )) {
			$args['data_args'] = array();
		}
		
		//Data
		if( isset( $args['data'] )) {
			$args['options'] = $this->get_data( $args['data'], $args['data_args'] );
		}
		
		//Class
		if(! isset( $args['class'] )) {
			$args['class'] = '';
		}
		
		$args['class'] = sanitize_html_class( $args['class'] );
		
		//Value == current value
		$args['_value'] = $args['_current'] = $this->get_option( $id );
		
		//Id attribute
		$args['__id'] = esc_attr( $id );
		
		return $args;
	}
	
	/**
	 * Saves submitted data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function save() {

		if( $this->instance_id === false )
			return;
		
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wp-elements' ) )			
			die( __( 'Action failed. Please refresh the page and retry.', 'picosearch' ) );
		
		$mode = 'save';
		
		//Check if this is an export
		if ( isset( $_POST['wpe-importer-data'] ) ){
			$mode = 'import';
		}
		
		//Check if this is an export
		if ( isset( $_POST['wpe-reset'] ) ){
			$mode = 'reset';
		}
		
		$options = $this->map_deep ( $this->merge_options( $mode ), 'wp_unslash');

		if ( is_array ( $options )) {
			update_option( $this->instance_id, $options );
		
			//Update cached data with our new values
			$this->instances[$this->instance_id]['user_settings'] = $options ;
		}
	}
	
	/**
	 * Same as map_deep found in core 
	 * We copied it here to support WP versions less than 4.4
	 */
	public function map_deep( $value, $callback ) {
		
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $item ) {
				$value[ $index ] = $this->map_deep( $item, $callback );
			}
		} elseif ( is_object( $value ) ) {
			$object_vars = get_object_vars( $value );
			foreach ( $object_vars as $property_name => $property_value ) {
				$value->$property_name = $this->map_deep( $property_value, $callback );
			}
		} else {
			$value = call_user_func( $callback, $value );
		}

		return $value;
	}
	
	/**
	 * Get the plugin url.
	 * You will probably want to edit this method if you have 
	 * a theme instead of a plugin. 
	 */
	public function set_base_url( $url ) {
		$this->base_url = $url;
	}
	
	/**
	 * Get the plugin url.
	 * You will probably want to edit this method if you have 
	 * a theme instead of a plugin. 
	 */
	public function get_base_url() {
		return $this->base_url;
	}
	
	/**
	 * Registers data callbacks  
	 */
	public function register_data_callback( $data, $args = array()) {
		
		if (! $data ) 
			return;
		
		if( is_array( $data ) ) {
			
			foreach ( $data as $single ) {
				$this->register_data_callback( $single, $args );
			}
			
		} else {

			$this->data_callbacks[$data] = $args;
			
		}
			
	}
	
	/**
	 * Registers data callbacks  
	 */
	public function update_data_callback( $data, $key ,$args = array()) {	
		
		if( isset( $this->data_callbacks[$data] ) && $key )
			$this->data_callbacks[$data][$key] = $args();
		
	}
	
	/**
	 * Registers multiple data callbacks  
	 */
	public function register_multiple_data_callbacks( $callbacks = array() ) {		
		foreach ( $callbacks as $data => $args ) {
			
			$this->register_data_callback( $data, $args );
			
		}
	}
	
	/**
	 * Registers multiple data callbacks  
	 */
	public function get_registered_data_callbacks( ) {		
		return $this->data_callbacks;
	}
	
	/**
	 * Fetch post taxonomies
	 */
	public function get_taxonomies( $args ) {		
		return get_taxonomies( $args, false );	
	}
	
	/**
	 * Fetch post types
	 */
	public function get_post_types( $args ) {		
		$defaults   = array(
                                'public'              => true,
                                'exclude_from_search' => false,
                            );
		$args = wp_parse_args( $args, $defaults );
		return get_post_types( $args, false );	
	}
	
	/**
	 * Fetch countries
	 */
	public function get_countries( $args ) {		
		return require( 'data/countries.php' );
	}
	
	/**
	 * Fetch post statuses
	 */
	public function get_post_statuses( $args ) {		
		global $wp_post_statuses;
		$return = array();
							
		foreach($wp_post_statuses as $status => $details ) {
			$return[ $status ] = $details->label;
		}
							
		return $return;
	}
	
	/**
	 * Fetch Roles
	 */
	public function get_roles( $args ) {		
		global $wp_roles;						
		return $wp_roles->role_names;
	}
	
	/**
	 * Fetch capabilities
	 */
	public function get_capabilities( $args ) {		
		global $wp_roles;
						
		$capabilities = array();
		if( !isset( $args['user_type'] ) ) {
			$roles = $wp_roles->roles;
			foreach ( $roles as $role) {
				foreach ( $role['capabilities'] as $cap => $bool ) {
									
					if( $bool == true )
						$capabilities[$cap] = ucfirst( str_replace( '_', ' ', $cap) );
										
				}
			}
		} else {
							
			if ( isset ($wp_roles->roles[$args['user_type']]) ){
								
				foreach ( $wp_roles->roles[$args['user_type']]['capabilities'] as $cap => $bool ) {
									
					if( $bool == true )
						$capabilities[$cap] = ucfirst( str_replace( '_', ' ', $cap) );
										
				}
								
			}
							
		}
						
		return $capabilities;
	}
	
	
	/**
	 * Registers multiple data callbacks  
	 */
	public function register_core_data_callbacks( ) {

		//How all callbacks should be formated
		$args = array (
				'cb' => 'get_categories',
				'key' => 'term_id',
				'value' => 'name',
				'modified' => false,
			);
			
		//Categories
		$callbacks = array( 'category', 'categories' );
		$this->register_data_callback( $callbacks, $args );
		
		//Tags
		$callbacks = array( 'tag', 'tags', 'post_tag' );
		$args['cb'] = 'get_tags';
		$this->register_data_callback( $callbacks, $args );
		
		//Terms
		$callbacks = array( 'terms', 'term' );
		$args['cb'] = 'get_terms';
		$this->register_data_callback( $callbacks, $args );
		
		//Menu
		$callbacks = array( 'menus', 'menu' );
		$args['cb'] = 'wp_get_nav_menus';
		$this->register_data_callback( $callbacks, $args );
		
		//Pages
		$callbacks = array( 'page', 'pages' );
		$args['cb'] = 'get_pages';
		$args['key'] = 'ID';
		$args['value'] = 'post_title';
		$this->register_data_callback( $callbacks, $args );
		
		//Posts
		$callbacks = array( 'posts', 'post' );
		$args['cb'] = 'get_posts';
		$this->register_data_callback( $callbacks, $args );
		
		//Taxonomies
		$callbacks = array( 'taxonomy', 'taxonomies' );		
		$args['cb'] = array( $this, 'get_taxonomies' );					 
		$args['key'] = 'name';
		$args['value'] = 'label';			 
		$this->register_data_callback( $callbacks, $args );
		
		//Users
		$callbacks = array( 'user', 'users', 'people' );
		$args['cb'] = 'get_users';
		$args['key'] = 'ID';
		$args['value'] = 'display_name';
		$this->register_data_callback( $callbacks, $args );
				
		//Post types
		$callbacks = array( 'post_types', 'post_type' );
		$args['cb'] = array( $this, 'get_post_types' );						
		$args['key'] = 'name';				
		$args['value'] = 'label';		
		$this->register_data_callback( $callbacks, $args );
		
		//Countries
		$callbacks = array( 'country', 'countries' );
		$args['cb'] = array( $this, 'get_countries' );						
		$args['modified']	= true;			
		$this->register_data_callback( $callbacks, $args );
		
		//Post statuses
		$callbacks = array( 'post_statuses', 'post_status' );
		$args['cb'] = array( $this, 'get_post_statuses' );
		$this->register_data_callback( $callbacks, $args );
		
		//User roles
		$callbacks = array( 'roles', 'role', 'user_roles', 'user_role' );		
		$args['cb'] = array( $this, 'get_roles' );						
		$this->register_data_callback( $callbacks, $args );
		
		//Capabilities
		$callbacks = array( 'capabilities', 'capability', 'user_capabilities', 'user_capability' );		
		$args['cb'] = array( $this, 'get_capabilities' );						
		$this->register_data_callback( $callbacks, $args );
	}
	
	public function get_data( $type = '', $args = array()) {
		
		if( empty ( $type ) || !is_string( $type ) )
			return array();
		
		if ( !is_array( $args ) )
			$args = array();
		
		$type = strtolower( $type );
		$callbacks = $this->data_callbacks;

		if( !isset ( $callbacks[ $type ] ) )
			return array();
				
		if( !isset ( $callbacks[ $type ]['cb'] ) )
			return array();
		
		if ( isset ( $callbacks[ $type ]['modified'] )
			 && true == $callbacks[ $type ]['modified'] ) {
				 
				return call_user_func( $callbacks[$type]['cb'], $args );
				 
			 }
		
		$data = call_user_func( $callbacks[$type]['cb'], $args );
		
		if (! is_array ( $data ) )
			return array();
		
		$return = array();
		$key = ( isset ( $callbacks[$type]['key'] )) ? $callbacks[$type]['key'] : 'ID';
		$value = ( isset ( $callbacks[$type]['value'] )) ? $callbacks[$type]['value'] : 'title';
		
		foreach ( $data as $single ) {
			
			if ( is_array( $single ) && isset ( $single[ $key ] ) ) {
				
				$label =  ( isset ( $single[ $value ] )) ? $single[ $value ] :  $single[ $key ] ;
				$return[ $single[ $key ] ] = $label;
				
			}
			
			if ( is_object( $single ) && isset ( $single->$key ) ) {
				
				$label =  ( isset ( $single->$value )) ?  $single->$value  :  $single->$key ;
				$return[ $single->$key ] =  $label;
				
			}
			
		}
	
		return $return;
	}
}
