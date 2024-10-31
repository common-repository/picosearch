<?php
//This file defines administration fields

if ( ! defined( 'ABSPATH' ) ) {	
	exit; // Exit if accessed directly.
}


picosearch()->elements()->queue_control( 'indexing-title', array (
		'type' => 'title',
		'title' => __( 'Indexing', 'picosearch' ),
		'subtitle' => __( 'These options determine what content is indexed', 'picosearch' ),
		'section'  => 'Indexing',
	) );

picosearch()->elements()->queue_control( 'ranking-title', array (
		'type' => 'title',
		'title' => __( 'Ranking', 'picosearch' ),
		'subtitle' => __( 'These options determine how search results are ranked', 'picosearch' ),
		'section'  => 'Ranking',
	) );

picosearch()->elements()->queue_control( 'searching-title', array (
		'type' => 'title',
		'title' => __( 'Search Results', 'picosearch' ),
		'subtitle' => __( 'These options determine how search results are displayed', 'picosearch' ),
		'section'  => 'Searching',
	) );
	
picosearch()->elements()->queue_control( 'autocomplete-title', array (
		'type' => 'title',
		'title' => __( 'Query Completion', 'picosearch' ),
		'subtitle' => __( 'Options for automatic query completion', 'picosearch' ),
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'enable-autocomplete', array (
		'type' => 'switch',
		'title' => __( 'Autocomplete', 'picosearch' ),
		'description' => __( 'Automatically completes the current query being typed in the search box', 'picosearch' ),
		'default'  => '1',
		'enabled'  => 'Enabled',
        'disabled' => 'Disabled',
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-autocomplete-count', array (
		'type' => 'number',
		'description' => __( 'Number of autocomplete suggestions', 'picosearch' ),
		'title' => __( 'Count', 'picosearch' ),
		'default'  => '5',
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-autocomplete-engine', array (
		'type' => 'select',
		'description' => __( 'Engine used to load autocomplete suggestions', 'picosearch' ),
		'title' => __( 'Engine', 'picosearch' ),
		'default'  => 'google',
		'options'  => apply_filters( 'picosearch-autocomplete-engines', array(
                    'local' => __( 'local database', 'picosearch' ),
                )),
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-autocomplete-table', array (
		'type' => 'select',
		'description' => __( 'Database table to be used in case engine is set to Local database.', 'picosearch' ),
		'title' => __( 'DB Table', 'picosearch' ),
		'default'  => 'posts',
		'options'  => array(
                    'posts' => __( 'Post titles', 'picosearch' ),
                    'previous' => __( 'Previous searches', 'picosearch' ),
                ),
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'border-autocomplete', array (
		'type' => 'color',
		'description' => __( 'This is the border surrounding the whole suggestions field.', 'picosearch' ),
		'title' => __( 'Main Border', 'picosearch' ),
		'default'  => '#fff',
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'border-single-autocomplete', array (
		'type' => 'color',
		'description' => __( 'This is the border appearing above each suggestion.', 'picosearch' ),
		'title' => __( 'Secondary Border', 'picosearch' ),
		'default'  => '#f2f2f2',
		'section'  => 'Query Completion',
	) );
	
	
picosearch()->elements()->queue_control( 'autocomplete-background', array (
		'type' => 'color',
		'title' => __( 'Background', 'picosearch' ),
		'default'  => '#fff',
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'autocomplete-suggestion-color', array (
		'type' => 'color',
		'description' => __( 'Styles the shown suggestions', 'picosearch' ),
		'title' => __( 'Color', 'picosearch' ),
		'default'  => '#000',
		'section'  => 'Query Completion',
	) );
	
	
picosearch()->elements()->queue_control( 'autocomplete-suggestion-matched', array (
		'type' => 'color',
		'description' => __( 'Styles the matched part of the autocomplete results.', 'picosearch' ),
		'title' => __( 'Secondary Color', 'picosearch' ),
		'default'  => '#000',
		'section'  => 'Query Completion',
	) );
	
picosearch()->elements()->queue_control( 'index-opt-info', array (
		'type' => 'alert',
		'title' => '',
		'text' => __( 'Indexing occurs in the background and search results will only include posts that are already indexed.', 'picosearch' ),
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'fields-to-index', array (
		'type' => 'multiselect',
		'description' => __( 'Select the fields that should be added to the index.', 'picosearch' ),
		'title' => __( 'Fields', 'picosearch' ),
		'default'  => array( 'title', 'content', 'excerpt'  ),
		'options'  => array(
                    'title' => 'Title',
                    'content' => 'Content',
					'excerpt' => 'Excerpts',
                    'url' => 'Permalinks',
                    
                ),
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'searchable-post-types', array (
		'type' => 'multiselect',
		'description' => __( 'Limit results to this post types.', 'picosearch' ),
		'title' => __( 'Post Types', 'picosearch' ),
		'data'     => 'post_type',
		'default'  => array( 'attachment', 'page', 'post', 'product'  ),
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'excluded-cats', array (
		'type' => 'multiselect',
		'description' => __( 'Exclude posts in these categories', 'picosearch' ),
		'title' => __( 'Categories', 'picosearch' ),
		'data'     => 'categories',
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'excluded-tags', array (
		'type' => 'multiselect',
		'description' => __( 'Exclude posts with these tags', 'picosearch' ),
		'title' => __( 'Tags', 'picosearch' ),
		'data'     => 'tags',
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'excluded-user-posts', array (
		'type' => 'multiselect',
		'description' => __( 'Exclude posts by these authors', 'picosearch' ),
		'title' => __( 'Authors', 'picosearch' ),
		'data'     => 'users',
		'section'  => 'Indexing',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-snippet-enable', array (
		'type' => 'switch',
		'title' => __( 'Snippets', 'picosearch' ),
		'subtitle' => __( 'Enable custom snippets', 'picosearch' ),
		'default'  => '1',
		'section'  => 'Searching',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-snippet-highlight', array (
		'type' => 'switch',
		'title' => __( 'Highlight', 'picosearch' ),
		'subtitle' => __( 'Highlight custom snippets', 'picosearch' ),
		'default'  => '1',
		'section'  => 'Searching',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-snippet-color', array (
		'type' => 'color',
		'subtitle' => __( 'Highlight color', 'picosearch' ),
		'title' => __( 'Color', 'picosearch' ),
		'default'  => '#000',
		'section'  => 'Searching',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-snippet-length', array (
		'type' => 'number',
		'subtitle' => __( 'Length of custom snippets', 'picosearch' ),
		'title' => __( 'Length', 'picosearch' ),
		'default'  => '300',
		'section'  => 'Searching',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-snippet-tags', array (
		'type' => 'textarea',
		'textarea_rows' => 3,
		'description' => __( 'HTML tags that should be preserved in the snippet.', 'picosearch' ),
		'title' => __( 'Tags', 'picosearch' ),
		'subtitle' => __( 'Allowed HTML tags', 'picosearch' ),
		'default'  => 'strong, a',
		'section'  => 'Searching',
	) );
			
picosearch()->elements()->queue_control( 'picosearch-order-by', array (
		'type' => 'select',
		'title' => __( 'Order By', 'picosearch' ),
		'default'  => 'post__in',
		'options'  => array(
                    'post__in' => __( 'Relevance (recommended)', 'picosearch' ),
                    'title' => __( 'Title', 'picosearch' ),
                    'date' => __( 'Date Published', 'picosearch' ),
					'rand' => __( 'Random', 'picosearch' ),
                    'comment_count' => __( 'Popularity', 'picosearch' ),
					'type' => __( 'Post Type', 'picosearch' ),
					'author' => __( 'Author', 'picosearch' ),
                    
                ),
		'section'  => 'Ranking',
	) );

picosearch()->elements()->queue_control( 'picosearch-post-type-weight-title', array (
		'type' => 'title',
		'title' => __( 'Weights', 'picosearch' ),
		'subtitle' => __( 'Select how individual content should be weighted', 'picosearch' ),
		'section'  => 'Ranking',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-title-weight', array (
		'type' => 'select',
		'title' => __( 'Title', 'picosearch' ),
		'default'  => '10',
		'options' => array_combine( range(1, 20), range(1, 20)),
		'section'  => 'Ranking',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-content-weight', array (
		'type' => 'select',
		'title' => __( 'Content', 'picosearch' ),
		'default'  => '2',
		'options' => array_combine( range(1, 20), range(1, 20)),
		'section'  => 'Ranking',
	) );
		
picosearch()->elements()->queue_control( 'picosearch-excerpt-weight', array (
		'type' => 'select',
		'title' => __( 'Excerpt', 'picosearch' ),
		'default'  => '3',
		'options' => array_combine( range(1, 20), range(1, 20)),
		'section'  => 'Ranking',
	) );
			
picosearch()->elements()->queue_control( 'picosearch-url-weight', array (
		'type' => 'select',
		'title' => __( 'Permalink', 'picosearch' ),
		'default'  => '15',
		'options' => array_combine( range(1, 20), range(1, 20)),
		'section'  => 'Ranking',
	) );
	
picosearch()->elements()->queue_control( 'picosearch-post-type-weight-title', array (
		'type' => 'title',
		'title' => __( 'Post Types', 'picosearch' ),
		'subtitle' => __( 'Select how individual post types should be weighted', 'picosearch' ),
		'section'  => 'Ranking',
	) );
	
$post_types = picosearch()->elements()->get_data( 'post_type' );
foreach( $post_types as $slug => $label ){
	
	picosearch()->elements()->queue_control( "picosearch-$slug-weight", array (
		'type' => 'select',
		'title' => $label,
		'default'  => '1',
		'options' => array_combine( range(1, 20), range(1, 20)),
		'section'  => 'Ranking',
	) );
	
}
	do_action( 'picosearch_after_setting_sections' );
	
	picosearch()->elements()->queue_control( 'picosearch-import', array (
		'type' => 'import',
		'title' => 'Import/Export settings',
		'section' => 'import / Export'
	) );
	
	
	picosearch()->elements()->queue_control( 'picosearch-save2', array (
		'type' => 'save',
		'value' => 'Save',
		'full_width' => true,
	) );