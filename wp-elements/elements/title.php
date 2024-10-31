<?php

/**
 * Renders a title
 *
 * Modified for our plugin. Not similar to the original element
 *
 *
 */

	if ( isset( $args['title'] ) ) {
		echo '<h2 class="' . $args['class'] .  '">' . $args['title'] . '</h2>';
	}
	
	if ( isset( $args['subtitle'] ) ) {
		echo $args['subtitle'];
	}
	
	if (! empty( $args['description'] ) ) {
		echo "<br><small class='form-text text-muted'>{$args['description']}</small>";
	}
	
	echo '<hr />';
