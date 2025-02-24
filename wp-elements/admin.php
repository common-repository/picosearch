<?php

//The core elements class
require_once ('wp-elements.php');

add_action( 'admin_menu', 'picosearch_add_page' );
function picosearch_add_page () {
	
$picosearch_upgrade = add_menu_page( 'Picosearch', 
								  'Picosearch', 
								  'read', 
								  'picosearch', 
								  'picosearch_welcome_render'
								);
								
$picosearch_page = add_submenu_page( 'picosearch', 
								'Settings', 
								'Search Settings', 
								'manage_options', 
								'picosearch-settings', 
								'picosearch_admin_render'
							);
							
$picosearch_reports = add_submenu_page( 'picosearch', 
								'Reports', 
								'Search Reports', 
								'manage_options', 
								'picosearch-reports', 
								'picosearch_reports_render'
							);
		
//Set our hook  suffix and active element types
picosearch()->elements()->set_instance_args( array(
	'hook_suffix' => $picosearch_page,
	'element_types' => array( 'select', 'color'),
));

picosearch('picosearch-stats')->elements('picosearch-stats')->set_instance_args( array(
	'hook_suffix' => $picosearch_reports,
	'element_types' => array( 'card', 'alert'),
));

picosearch()->elements()->update_element_type( 'title', 'render_default_markup', true );

}
	
//Register elements	 
add_action( 'init', 'picosearch_admin_init' );

function picosearch_admin_init() {
	include 'fields.php';
}

function picosearch_admin_render() {
	picosearch()->elements()->set_template( 'picosearch-template.php' );
	picosearch()->elements()->render();	
}

function picosearch_reports_render() {
	include 'reports.php';
	picosearch()->elements('picosearch-stats')->render();		
}

function picosearch_welcome_render() {
	include 'welcome_screen.php';	
}

function picosearch_render_wrapper_open( $element_id, $args ) {
	$element_id = esc_attr($element_id);
	$class = '';
	
	if( isset ( $args['section'] ) &&  $args['section'] )		
		$class .= ' wp-section-wrapper-' . sanitize_html_class( $args['section'] );
	
	echo "<tr valign='top' class='$class'>";
			
	if ( isset( $args['title'] ) ) {
		
		$title = '<strong>' . $args['title']. '</strong>';
		if ( isset( $args['subtitle'] ) ) {
				$title .= "<br /><span class='wpe-text-normal'>{$args['subtitle']}</span>";
			}
		
		$label_class = 'label';
		if ( in_array( $args['type'], explode ( ' ', 'title alert' ) ) ) {
				$label_class = 'd-none';
			}
			
		echo "<th scope='row' class='titledesc'>
					<label class='$label_class' for='$element_id'>$title</label>
			  </th>	";
			  
	}
	echo "<td class='forminp'>";
}

function picosearch_render_wrapper_end() {
	echo '</td></tr>';
}