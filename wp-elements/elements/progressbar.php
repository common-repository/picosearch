<?php

/**
 * Outputs an alert box
 *
 *
 */
$class = 'progress';

if( isset( $args['stripped'] ) && $args['stripped'] == true )
	$class .= ' progress-striped';

if( isset( $args['state'] ) )
	$class .= ' progress-' . $args['state'] ;

$value = 0;
$max = 100;

if( isset( $args['max'] ) )
	$max = $args['max'];

if( isset( $args['value'] ) )
	$value = $args['value'];

echo "<progress class='$class' value='$value' max='$max'></progress>";

$description = $args['description'];
if (! empty( $description ) ) {
		echo "<p class='descprition'>$description</p>";
	}