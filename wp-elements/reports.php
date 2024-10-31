<?php
//This file defines administration fields

if ( ! defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly.

$total_searches = (int) picosearch_total_searches();
$searches_without_results = (int) picosearch_total_searches( 'hits = 0' );
$searches_with_results = (int) picosearch_total_searches('hits > 0');

	picosearch()->elements('picosearch-stats')->queue_control( 'picosearch-stats-cards', array(
		'type' => 'card',
		'card_type' => 'deck',
		'full_width' => true,
		'section'  => 'Statistics',
		'cards' => array(
			
			'1' => array(
				'class' => 'display-5 rounded-0 text-center',
				'state' => 'primary',
				'card_body' => "$total_searches<small class='smallx2 d-block'> Total Searches</small>",
			),
			
			'2' => array(
				'class' => 'display-5 rounded-0 text-center',
				'state' => 'primary',
				'card_body' => "$searches_without_results<small class='smallx2 d-block'> Searches Without Results</small>",
			),
			
			'3' => array(
				'class' => 'display-5 rounded-0 text-center',
				'state' => 'primary',
				'card_body' => "$searches_with_results<small class='smallx2 d-block'> Searches With Results</small>",
			),
			
		),
	) );

	picosearch()->elements('picosearch-stats')->queue_control( 'picosearch-stats-tables', array(
		'type' => 'card',
		'full_width' => true,
		'section'  => 'Statistics',
		'cards' => array(
			
			'1' => array(
				'card_header' => 'Popular Searches',
				'class' => 'rounded-right rounded-0 ',
				'card_body' => picosearch_show_searches('WHERE 1=1 ORDER BY searches DESC limit 10' ),
			
			),	

			'2' => array(
				'card_header' => 'Searches with most results',
				'class' => 'rounded-right rounded-0 ',
				'card_body' => picosearch_show_searches( 'WHERE hits > 0 ORDER BY hits DESC limit 10' ),
			
			),
			
			'3' => array(
				'card_header' => 'Searches with least results',
				'class' => 'rounded-right rounded-0 ',
				'card_body' => picosearch_show_searches( 'WHERE hits > 0 ORDER BY hits ASC limit 10' ),
			
			),
		),
	) );