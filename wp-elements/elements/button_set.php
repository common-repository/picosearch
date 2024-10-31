<?php

/**
 * Outputs elements for a button set
 *
 *
 */
	$type = $args['type'];
	$id = $args['__id'];
	$attr = $args['_custom_attributes'];
	$options = $args['options'];	
	$description = $args['description'];
	$current = $args['_current'];
	$class = $args['class'];
	$class .= ' wpe-set-'. $type;
	
	echo "<div class='btn-group $class' data-toggle='buttons'>";
	foreach( $options as $name => $val ) {
		
		$name = esc_attr( $name );
		$active = checked( ( is_array( $current ) && in_array ( $name, $current ) ), true, false ) ? 'active': '';
		echo "<label class='btn btn-primary $active'>
				<input type='checkbox' value='$name' name='{$id}[$name]' autocomplete='off'  $attr";
				
			checked( ( is_array( $current ) && in_array ( $name, $current ) ), true );
		
		echo ">$val </label>";
		
	}
	echo '</div>';

	// The description
	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}