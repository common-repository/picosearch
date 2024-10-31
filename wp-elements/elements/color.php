<?php

/**
 * Outputs elements for color
 *
 *
 */
 	
	$type = 'text';
	$description = $args['description'];
	$class = 'wpe-colorpick form-control ' . $args['class'];
	$id = $args['__id'];
	$placeholder = $args['placeholder'];
	$value = $args['_current'];
	$attr = $args['_custom_attributes'];
	
	echo "<div class='wpe-colorpickpreview' style='background: $value;'></div>";
	
	echo "<input value='$value' $attr  name='$id' id='$id' type='$type' class='$class' placeholder='$placeholder'/>";

	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}
	