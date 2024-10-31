<?php

/**
 * Outputs a card
 *
 *
 */	
if(! isset( $args['cards'] ) OR ! is_array ( $args['cards'] ) )
	return;

$card_type = '';
$attr = $args['_custom_attributes'];

if( isset( $args['card_type'] ) )
	$card_type = $args['card_type'];

if( $card_type == 'deck' ) {
	echo '<div class="card-deck-wrapper"> <div class="card-deck">';
}

if( $card_type == 'column' ) {
	echo '<div class="card-columns">';
}

if( $card_type == 'column2' ) {
	echo '<div class="card-columns2">';
}

if( $card_type == 'group' ) {
	echo '<div class="card-group">';
}

foreach( $args['cards'] as $card => $details ) {
	
	if(! isset( $details['card_header'] ) )
		$details['card_header'] = false;
	
	if(! isset( $details['card_title'] ) )
		$details['card_title'] = false;
	
	if(! isset( $details['card_body'] ) )
		$details['card_body'] = false;
	
	if(! isset( $details['card_render_element'] ) )
		$details['card_render_element'] = false;
	
	if(! isset( $details['card_footer'] ) )
		$details['card_footer'] = false;
	
	if(! isset( $details['card_image_top'] ) )
		$details['card_image_top'] = false;
	
	if(! isset( $details['card_image'] ) )
		$details['card_image'] = false;
	
	if(! isset( $details['card_image_bottom'] ) )
		$details['card_image_bottom'] = false;
	
	$class = 'wpe-card ';
	
	if( isset( $details['state'] ) OR ( isset( $details['inverse'] ) && $details['inverse'] == true ) )
		$class .= ' card-inverse';
	
	if( isset( $details['state'] ) )
		$class .= ' card-' . $details['state'];
	
	if( isset( $details['outline'] ) )
		$class .= ' card-outline-' . $details['outline'];
	
	$class .= ( isset( $details['class'] ) ) ? ' '. $details['class'] : '';
	echo "<div class='$class' $attr>";
	
	if( $details['card_header'] ) {
		
		echo "<div class='card-header'>{$details['card_header']}</div>";
		
	}
	
	if( $details['card_image_top'] ) {
		
		$url = '';
		$alt = '';
		
		if( isset( $details['card_image_top']['url'] ) )
			$url = $details['card_image_top']['url'];
		
		if( isset( $details['card_image_top']['alt'] ) )
			$alt = $details['card_image_top']['alt'];
		
		if (! is_array ( $details['card_image_top'] )) {
			$url = $details['card_image_top'];
		}
		echo "<img class='card-img-top img-fluid' src='$url' alt='$alt'>";
		
	}
	
	if( $details['card_image'] ) {
		
		$url = '';
		$alt = '';
		
		if( isset( $details['card_image']['url'] ) )
			$url = $details['card_image']['url'];
		
		if( isset( $details['card_image']['alt'] ) )
			$alt = $details['card_image']['alt'];
		
		if (! is_array ( $details['card_image'] )) {
			$url = $details['card_image'];
		}
		
		echo "<img class='card-img img-fluid' src='$url' alt='$alt'>";
		
	}
	
	if( $details['card_body'] ||  $details['card_render_element']  ) {
		
		echo '<div class="card-block">';
		if( $details['card_title'] )
			echo "<h3 class='card-title'>{$details['card_title']}</h3>";
		
		if( $details['card_render_element'] && is_array( $details['card_render_element'] ) ) {
			$custom_element = array();
			$custom_element['id'] = $args['id'] . $card;
			$custom_element['args'] = $details['card_render_element'];
			$this->render_element( $custom_element, false );
		}
		
		if( $details['card_body'] ) {
			echo '' . $details['card_body'];
		}
		echo '</div>';
		
	}
	
	if( $details['card_image_bottom'] ) {
		
		$url = '';
		$alt = '';
		
		if( isset( $details['card_image_bottom']['url'] ) )
			$url = $details['card_image_bottom']['url'];
		
		if( isset( $details['card_image_bottom']['alt'] ) )
			$alt = $details['card_image_bottom']['alt'];
		
		if (! is_array ( $details['card_image_bottom'] )) {
			$url = $details['card_image_bottom'];
		}
		echo "<img class='card-img-bottom img-fluid' src='$url' alt='$alt'>";
		
	}
	
	if( $details['card_footer'] ) {
		
		echo "<div class='card-footer'>{$details['card_footer']}</div>";
		
	}
	
	echo '</div>';
	
}

if( $card_type == 'deck' ) {
	echo '</div> </div>';
}

if( $card_type == 'column' OR $card_type == 'column2' OR $card_type == 'group' ) {
	echo '</div>';
}