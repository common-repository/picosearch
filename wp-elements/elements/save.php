<?php

/**
 * Outputs elements for save
 *
 *
 */
 	
	$description = $args['description'];
	$class = 'btn btn-primary ' . $args['class'];
	$id = $args['__id'];
	$attr = $args['_custom_attributes'];
		
	echo "<hr> <input id='$id' name='wpe-save' type='submit' class='$class' value='Save' $attr>";
	echo "<input id='{$id}_reset' name='wpe-reset' type='submit' class='$class btn-secondary ml-4' value='Reset'>";
			
	if (! empty( $description ) )
		echo "<p class='descprition'>$description</p>";
