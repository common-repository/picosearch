<?php

/**
 * Outputs elements for password
 *
 *
 */
 	
	$type = $args['type'];
	$description = $args['description'];
	$class = 'form-control ' . $args['class'];
	$id = $args['__id'];
	$placeholder = $args['placeholder'];
	$value = esc_attr( $args['_current'] );
	$attr = $args['_custom_attributes'];
			
	echo "<input value='$value' $attr name='$id' id='$id' type='password' class='$class' placeholder='$placeholder' />";

	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}
	