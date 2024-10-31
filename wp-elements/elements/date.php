<?php

/**
 * Outputs elements for date
 *
 *
 */
 	
	$type = 'text';
	$description = $args['description'];
	$class = 'wpe-date-control form-control ' . $args['class'];
	$id = $args['__id'];
	$placeholder = $args['placeholder'];
	$value = $args['_current'];
	$input_grp_before = false;
	$input_grp_after = false;
	$addon_before = '';
	$addon_after = '';
	$attr = $args['_custom_attributes'];
	
	
	if ( $type == 'date' ) {
		$class .= ' wpe-date-control';
		$type = 'text';
		$addon_after .= '<span class="input-group-btn">
							<button class="btn btn-secondary" type="button"  disabled>
							<span class="dashicons dashicons-calendar"></button></span>';
		
	}
	
	echo '<span class="input-group-btn">
			<button class="btn btn-secondary" type="button"  disabled>
			<span class="dashicons dashicons-calendar"></button></span>';
	echo "<div class='input-group'><input value='$value' $attr  name='$id' id='$id' type='$type' class='$class' placeholder='$placeholder'/></div>";

	if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}
	