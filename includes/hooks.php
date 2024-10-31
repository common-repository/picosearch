<?php

/**
 * Defines non-search hooks used
 *
 *
 * @since             1.0.0
 * @package           PicoSearch
 *
 */

	
	/**
	 * Enques frontend styles and scripts
	 *
	 * @since 1.0
	 *
	 * @return null
	 */
	function picosearch_front_scripts(  ) {
		wp_enqueue_style( 'picosearch_css',  picosearch()->plugin_url . 'assets/picosearch.css');
		wp_register_script( 'picosearch_front', picosearch()->plugin_url . 'assets/picosearch.js', array( 'jquery' ), '1.0.0', true );
		
		$fields = array(
			'nextNonce' => wp_create_nonce('myajax-next-nonce'),
			'limit' => picosearch_get_option('picosearch-autocomplete-count'),
			'action' => 'picosearchgetsuggestions',
			);
				
		$params = array(
			'nextNonce'				=>wp_create_nonce('myajax-next-nonce'),
			'load_suggestions'	=> picosearch_get_option('enable-autocomplete'),
			'search_string'	=> apply_filters( 'picosearch_search_string', 's'),
			'min_chars'	=> 1,
			'delay'	=> 150,
			'fields'	=> $fields,
			'request_url'	=> admin_url( 'admin-ajax.php' ),
		);
		
		$params = apply_filters( 'picosearch_javascript_params', $params  );
		
		wp_localize_script( 'picosearch_front', 'picosearch', $params );
		wp_enqueue_script( 'picosearch_front' );
				
	}
	
	add_action( 'wp_enqueue_scripts', 'picosearch_front_scripts' );
	
function picosearch_wp_head() {
	$border1 = picosearch_get_option( 'border-autocomplete' );
	$border2 = picosearch_get_option( 'border-single-autocomplete' );
	$background = picosearch_get_option( 'autocomplete-background' );
	$color1 = picosearch_get_option( 'autocomplete-suggestion-color' );
	$color2 = picosearch_get_option( 'autocomplete-suggestion-matched' );
	
	$style = ".autocomplete-suggestions {border-color: $border1; background-color: $background;}";
	$style .= ".autocomplete-suggestion {border-color: $border2; color:$color1 }";
	$style .= ".autocomplete-matched{ color: $color2;}";
	echo "<style>$style</style>";

}
add_action( 'wp_head', 'picosearch_wp_head' );
	
/**
 * Handles the ajax requests for search suggestions
 *
 * Returns the results in jsonp format
 * @since       1.0.0
 **/

function picosearch_get_suggestions() {
	$s=trim( picosearch_normalize ($_GET["s"]) );
	$callback=isset( $_GET["callback"] ) ? trim( $_GET["callback"] ) : false;
	
	//Check nonce
	$nonce = $_GET['nextNonce'];

	if ( ! wp_verify_nonce( $nonce, 'myajax-next-nonce' ) OR empty($s)) {
			die ();
		}
		
	global $wpdb;
	$table = picosearch_get_option('picosearch-autocomplete-table');
	$count = intval($_GET["limit"]);
	$s = $wpdb->prepare('%s', $s . '%');
	
	if($table == 'posts') {
		
		$sql ="SELECT post_title FROM {$wpdb->posts}
			WHERE LOWER(post_title) LIKE LOWER($s) AND post_status='publish' ORDER BY post_title ASC 
			LIMIT $count";
		
	} else {
		$table = $wpdb->prefix . 'picosearch_log';
		$sql = "SELECT query FROM {$table} 
			WHERE LOWER(query) LIKE LOWER($s) ORDER BY query ASC 
			LIMIT $count";
		
	}
		
	$results = array(sanitize_text_field($_GET["s"]));
	$results[] = $wpdb->get_col($sql);
	$results = json_encode($results);
	
	if($callback) {	
	
		echo "$callback($results)";		
		
	} else {
		
		echo $results;
		
	}
	

	exit; //This is important
}
add_action( 'wp_ajax_picosearchgetsuggestions', 'picosearch_get_suggestions' );
add_action( 'wp_ajax_nopriv_picosearchgetsuggestions', 'picosearch_get_suggestions' );

/**
 * Filters the search query
 *
 * This is because during the loop we unset it to prevent the default
 * search from running
 * 
 * @since       1.0.0
**/

function picosearch_get_search_query( $s ) {
	
	if(isset($_GET['s']))
		return esc_attr($_GET['s']);
	
	return $s;
	
}

add_filter( 'get_search_query', 'picosearch_get_search_query' );

/**
 * Fires when a post is being saved
 *
 * @since       1.0.0
 * @param       int $id the id of the post to index
 * @param       object $post The post object of the current post
 * @return      void
 */
function picosearch_saving_post( $id, $post ) {
	
	//Only add to index if its not an autosave
	if ( !( defined( 'DOUNG_AUTOSAVE' ) && DOING_AUTOSAVE ) && $post->post_status == 'publish')
			picosearch_index_post( $id, $post );		
		
 }
add_action( 'save_post', 'picosearch_saving_post',10 ,2 );

/**
 * Fires when a post status transitions
 *
 * @since       1.0.0
 * @param       int $id the id of the post to index
 * @param       object $post The post object of the current post
 * @return      void
 */
function picosearch_maybe_delete_post( $new_status, $old_status, $post ) {
	if ( $old_status == 'publish' && $new_status != 'publish' )
		picosearch_delete_post( $post->ID );	
		
 }
add_action( 'transition_post_status', 'picosearch_maybe_delete_post',10 ,3 );

/**
 * Fires when a post is being deleted from the db
 *
 * @since       1.0.0
 * @param       int $id the id of the post to index
 * @param       object $post The post object of the current post
 * @return      void
 */
function picosearch_delete_post( $id ) {
	
	global $wpdb;
	$index_table = picosearch()->index_table;
	$doc_table = picosearch()->doc_table;
	
	picosearch_query( $wpdb->prepare("DELETE FROM $index_table WHERE post_id= %d", $id));
	picosearch_query( $wpdb->prepare("DELETE FROM $doc_table WHERE id= %d", $id));
	
 }
add_action( 'delete_post', 'picosearch_delete_post' );

/**
 * Fires when post_content is being rendered
 *
 * @since       1.0.0
 */
function picosearch_custom_snippet( $content ) {
	//Are we allowed to snippetify this content
	if( !picosearch_use_custom_snippets() )
		return $content;
	
	global $post;
	$content = do_shortcode( $post->post_content );
	$s = $_GET['s'];
	
	//Convert search term to individual words and sort them
	$s_words = picosearch_tokenize( $s );
	$s2_words = picosearch_prepare( $s );
	$words = array_unique ( array_merge( $s_words, $s2_words));
	
	//Get a list of html tags to preserve in the snippet && strip the rest
	$tags = picosearch_snippet_tags();
	$content = wp_kses ( stripslashes ( $content ), $tags );
	
	//Radius of our snippet
	$radius = picosearch_snippet_radius();
	
	//Extract the snippet the snippet
	$excerpt =  picosearch_extract_snippet( $content, $words, $radius );
					
	//If we are not highlighting; return  our excerpt
	if ( !picosearch_get_option( 'picosearch-snippet-highlight' ) )
		return $excerpt;
				
	return picosearch_snippet_highlight( $excerpt, $words );
}
add_filter( 'the_excerpt', 'picosearch_custom_snippet', 20 );
add_filter( 'the_content', 'picosearch_custom_snippet', 20 );

/**
 * Fires when WP_Query is called
 *
 * @since       1.0.0
 */
function picosearch_pre_get_posts( $query ) {
				
//Check if it is a search query
if( picosearch_is_search( $query ) ) {
	
	$q = $_GET['s'];
	$results = (array) apply_filters ( 'picosearch_search_results', picosearch_search( $q  ) );
	picosearch_log_query( $q, count ($results) );
	
	//post types to search
	$post_types = picosearch_post_types();
	$args = array();		   
			   
	//If we found results, we unset s; otherwise; we optionally fallback to in_built WordPress
	if ( is_array ( $results ) && count( $results ) > 0 ) {
					
		$query->set('s', '');
		$query->set('post__in', $results);
		$query->set('orderby', picosearch_get_option ( 'picosearch-order-by' ));
					
	}
				
	//If we are also indexing attachments
	if( in_array('attachment', picosearch_post_types()) )
		$query->set('post_status', array('publish', 'inherit'));
	
	}
	
	//Exclude cats
	$query->set('category__not_in', picosearch_get_option ( 'excluded-cats' ));
	
	//Exclude tags
	$query->set('tag__not_in', picosearch_get_option ( 'excluded-tags' ));
	
	//Exclude authors
	$query->set('tag__not_in', picosearch_get_option ( 'excluded-user-posts' ));
}

add_action( 'pre_get_posts', 'picosearch_pre_get_posts', 30 );	