<?php

/**
 * Outputs elements for button
 *
 *
 */
 	
	$description = $args['description'];
	$class = 'btn btn-primary ' . $args['class'];
	$id = $args['__id'];
	$value = ucfirst($type);
	$attr = $args['_custom_attributes'];
		
	echo "<input id='$id' name='$id' type='submit' class='$class' $attr>$value</button>";
			
	if (! empty( $description ) )
		echo "<p class='descprition'>$description</p>";
