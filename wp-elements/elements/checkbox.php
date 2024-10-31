<?php

/**
 * Outputs checkboxes
 *
 *
 */

    $id = $args['__id'];
	$class = 'form-check ' . $args['class'];
	$current = $args['_value'];
	$description = $args['description'];
	$options = $args['options'];
	$attr = $args['_custom_attributes'];
	
	if ( isset ( $args['inline'] ) && $args['inline'] == true ) {
		$class .= ' form-check-inline';
	}
		foreach( $options as $name => $label ) {

			$name = esc_attr( $name );
			
			echo "<div class='$class'>
					<label class='form-check-label'>
						<input class='form-check-input' name='{$id}[$name]' type='checkbox' value='$name' $attr";
					
				checked( ( is_array( $current ) && in_array ( $name, $current ) ), true );
	
			echo ">$label</label></div>";
			
		}


	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}
