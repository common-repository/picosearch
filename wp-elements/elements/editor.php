<?php

/**
 * Outputs elements for editor
 *
 *
 */

	if (! isset ($args['textarea_rows'] ) ) {		
		$args['textarea_rows'] = 10;		
	}
	
	wp_editor( $args['_value'], $args['__id'], $args );
