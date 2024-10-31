<?php

/**
 * Renders pills
 *
 *
 */
	$class = 'badge badge-pill ';
	
	if( isset( $args['pill_type'] )) {
		$class .= ' badge-' . $args['badge_type'] . ' ';
	} else {
		$class .= ' badge-default ';
	}
		
	if( isset( $args['class'] ) ) {
		$class .= ' ' . $args['class'] . ' ';
	}
	
	if(! isset( $args['text'] ) ) {
		$args['text'] = '';
	}
	
	echo "<span class='$class'>{$args['text']}</span>";
