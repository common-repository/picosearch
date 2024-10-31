<?php

/**
 * Outputs elements for tinymce
 *
 * Please note that apart from the quicktags element; 
 * the other two elements cannot be included more than once in the 
 * same instance. It's a WordPress limitation
 *
 */
		
	$args['quicktags'] = false;
	
	if (! isset ($args['textarea_rows'] ) ) {		
		$args['textarea_rows'] = 10;		
	}
	
	wp_editor( $args['_value'], $args['__id'], $args );
