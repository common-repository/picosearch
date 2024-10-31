<?php

/**
 * Outputs radio buttons
 *
 *
 */
	$id = $args['__id'];
	$class = 'form-check ' . $args['class'];
	$current = $args['_current'];
	$description = $args['description'];
	$options = $args['options'];
	$attr = $args['_custom_attributes'];
	
	if ( isset ( $args['inline'] ) && $args['inline'] == true ) {
		$class .= ' form-check-inline';
	}
	
		foreach( $options as $name => $value) {

		echo "<div class='$class' >
					<label class='form-check-label'>
						<input class='form-check-input' value='$name' type='radio' name='$id' $attr";
					
				checked( $name, $current );
	
			echo ">$value</label></div>";

		}


	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}
