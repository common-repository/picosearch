<?php

/**
 * Outputs elements for a switch
 *
 *
 */
	$type = $args['type'];
	$id = $args['__id'];
	$attr = $args['_custom_attributes'];
	$class = $args['class'];
	$class .= ' wpe-set-'. $type;
	
	//Labels for our switches
	$enabled = __( 'Enabled', 'ajax-live-search' );
	$disabled = __( 'Disabled', 'ajax-live-search' );
	
	if ( isset( $args['enabled'] ) ) {
		$enabled = $args['enabled'];
	}
	
	if ( isset( $args['disabled'] ) ) {
		$disabled = $args['disabled'];
	}
	
	if ( $type == 'switch') {
		
		$options = array(
			'1' => $enabled,
			'0' => $disabled,
		);
				
	} else {

		$options = $args['options'];
	}
	
	$description = $args['description'];
	$current = $args['_current'];

	echo "<div class='btn-group $class' data-toggle='buttons'>";
	foreach( $options as $key => $val ) {
		$active = checked( $key, $current, false ) ? 'active': '';
		echo "<label class='btn btn-primary $active'>
				<input type='radio' value='$key' name='$id' autocomplete='off'  $attr";
				
			checked( $key, $current );
		
		echo ">$val </label>";
	}
	echo '</div>';

	// The description
	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}