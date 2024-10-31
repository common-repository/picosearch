<?php

/**
 * Outputs elements for multi_text
 *
 *
 */

if ( empty( $args['_value'] ) || !is_array( $args['_value'] ) ) {
	
		$args['_value'] = array('');
		
	}

if (! empty( $args['description'] ) )
	echo "<p class='descprition'>{$args['description']}</p>";
	
$first = true;
foreach( $args['_value'] as $value ) {
	
	if ( $first )	{
		$class = 'wp-elements-new-text-box dashicons dashicons-plus';
		$first = false;
	} else {
		$class = 'wp-elements-new-text-box dashicons dashicons-minus';
	}
	
	$id = $args['__id'];
	$value = esc_attr ( $value );
?>
	
<div class="input-group wp-elements-multi">
	<input value="<?php echo $value; ?>" name="<?php echo $id; ?>[]" type="text" class="form-control ">
	<span class="input-group-btn">
		<button class="btn btn-secondary" type="button">	
			<span class="<?php echo $class; ?>"></span>
		</button>
	</span>
</div>

<?php 
}