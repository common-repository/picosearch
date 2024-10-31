<?php

/**
 * Defines basic utility functions
 *
 *
 * @since             1.0.0
 * @package           Picosearch
 *
 */
 
 
 /**
  * Gets a user defined option
  *
  * @since       1.0.0
  * @return      mixed bool when no value is found; the value otherwise
  */
   function picosearch_get_option( $option  ) {	   
		return picosearch()->elements()->get_option( $option );				
   }

   
   
 /**
  * Checks if we are in a main search query
  *
  * @since       1.0.0
  * @return      bool 
  */
   function picosearch_is_search( $query ) {
		return ( !is_admin() && $query->is_main_query() && $query->is_search());				
   }
   
 /**
  * Checks whether or not we should use custom snippets
  *
  * @since       1.0.0
  * @return      bool 
  */
   function picosearch_use_custom_snippets() {
		return ( picosearch_get_option( 'picosearch-snippet-enable' ) // Has the user enabled custom snippets
				&& isset( $_GET['s'] ) // Are we on the search page
				&& is_main_query() // Is this the main query
				&& in_the_loop() // Are we in the loop
				);				
   }

 /**
  * Returns an array of tags to preseve in snippets
  *
  * @since       1.0.0
  * @return      array
  */
   function picosearch_snippet_tags() {
		$tags = picosearch_tokenize ( picosearch_get_option( 'picosearch-snippet-tags' ) );
		$modified_tags = array();
		
		foreach( $tags as $tag ) {
			$modified_tags[$tag] = array();
			if ( $tag == 'a')
				$modified_tags[$tag] = array('href');
		}
		
		return $modified_tags;
   }

 /**
  * Highlights a search snippet
  *
  * @since       1.0.0
  * @return      string
  */
   function picosearch_snippet_highlight( $text, $words ) {
		
		foreach( $words as $word ) {					
			$text=preg_replace("/($word)(?![^<]*>)/i", "<strong><span class='picosearchexcerpt'>\${1}</span></strong>", $text);					
		}
		return $text;
   }

 /**
  * Finds the locations of the words in a snippet
  *
  * @since       1.0.0
  * @return      array
  */
   function picosearch_word_locations( $text, $words ) {
		
		$locations = array();
        foreach ($words as $word) {
            $wordlen = strlen($word);
            $loc     = stripos( $text, $word);
            while ( $loc !== false ) {
                $locations[] = $loc;
                $loc         = stripos( $text, $word, $loc + $wordlen);
            }
        }
        $locations = array_unique($locations);
        sort( $locations );
		
        return $locations;
   }

 /**
  * Finds the locations of the words in a snippet
  *
  * @since       1.0.0
  * @return      array
  */
   function picosearch_snippet_location( $word_positions = array() ) {
		
		$radius = picosearch_snippet_radius();
		$words_in_radius = 0;
		$best_radius = array( 0, $radius * 2 );
		$start_pos = 0;
		$end_post = $radius * 2;
		
		if (! is_array( $word_positions ) )
			$word_positions = array();
		
		if (!isset($word_positions[0]))
            return $best_radius;
		
		//Loop through all words to extract the best word range		
		foreach ( $word_positions as $position ) {
			
			//Fetch possible ranges for this word position
			$ranges = picosearch_word_ranges( $position );
			
			//Get the best position
			$best_range = picosearch_best_range( $ranges, $word_positions );
			
			//Check if its better than what we currently have
			if ( $best_range[0] > $words_in_radius ) {
				$words_in_radius = $best_range[0];
				$best_radius = $best_range[1];
			}
		}
		
		return $best_radius;
   }

   
   
   
 /**
  * Gets best range from a group of ranges
  *
  * @since       1.0.0
  * @return      array
  */
   function picosearch_best_range( $ranges, $word_positions ) {
		$words_in_range = 0;
		$best_range = $ranges[0];
		
		foreach ( $ranges as $range ) {
			$start = $range[0];
			$end = $range[1];
			
			$range = range( $start, $end );
			$_words_in_range = count ( array_intersect( $word_positions, $range) );
			
			if ( $_words_in_range > $words_in_range ) {
				$words_in_range = $_words_in_range;
				$best_range = array($start, $end);
			}
		}
		
		return array( $words_in_range, $best_range );
   }
   
   
	/**
	 * Gets possible word ranges
	 *
	 * @since       1.0.0
	 * @return      array 
	 */
   function picosearch_word_ranges( $position ) {
		$radius = picosearch_snippet_radius();
		$half_radius = ceil($radius / 2);
		
		$ranges = array(
					array ( $position - $radius, $position + $radius),
					array ( $position - $half_radius, $position + $radius + $half_radius),
					array ( $position - $half_radius - $radius, $position + $half_radius),
				);
		
		//Make sure no start range is less than 0
		if ( $ranges[0][0] < 0) {
			$ranges[0][1] = $ranges[0][1] + $radius -$position ;
			$ranges[0][0] = 0;
		}
		
		if ( $ranges[1][0] < 0) {
			$ranges[1][1] = $ranges[1][1] + $half_radius -$position;
			$ranges[1][0] = 0;
		}
		
		if ( $ranges[2][0] < 0) {
			$ranges[2][1] = $ranges[2][1] + $half_radius + $radius -$position;
			$ranges[2][0] = 0;
		}
		
		return $ranges;
   }
   
 /**
  * Gets radius of a search snippet
  *
  * @since       1.0.0
  * @return      array
  */
   function picosearch_snippet_radius() {
		$radius = ceil( absint ( picosearch_get_option ( 'picosearch-snippet-length' ) + 1 )  / 2 );
		
		if( $radius < strlen( $_GET['s'] ) ) 				
			return strlen( $_GET['s'] ); //Radius should not be less than the search term					
		
		return $radius;
   }

 /**
  * Extracts a snippet from text
  *
  * @since       1.0.0
  * @return      array
  */
function picosearch_extract_snippet( $content='', $words=array(), $radius = 60 ) {
	$snippet =  false;	
	$word_locations = picosearch_word_locations( $content, $words );
	$snippet_location = picosearch_snippet_location( $word_locations );
	
	$snippet =  substr($content,$snippet_location[0] , $snippet_location[1]);
	//If no word was found in the content...
	if( !$snippet )				
		$snippet = substr( $content, 0, $radius * 2 );
	
	return $snippet;
}


   
 /**
  * Searchable post types
  *
  * @since       1.0.0
  * @return      array an array of selected post types
  */
   function picosearch_post_types(  ) {
	   
		$all = picosearch_get_option( 'searchable-post-types' );
		
		if( !is_array( $all ) ) {			
			return array();			
		}
		return $all;	
   }

//Returns an array of stopwords
function picosearch_get_stopwords(){
	$stopwords = picocodes_the_stopwords(); //Implementend in stopwords.php
	if ( $stopwords )
		return picosearch_tokenize( $stopwords );
	
	return array();
}

//Removes stopwords from a list of words
function picosearch_remove_stopwords( $words ){
	return array_diff( $words, picosearch_get_stopwords() );
}

//Stems the array of words
function picosearch_get_stemmer(){
	$file = substr ( get_locale(), 0, 2 ) . '-stemmer.php';
	
	if ( file_exists($file) ) {
		require_once($file);
	} else {
		require_once('default-stemmer.php');
	}
}	

//Stems a single word
function picosearch_stem_word( $word ){
	if ( ! class_exists( 'Picosearch_Stemmer' ) )
		picosearch_get_stemmer();
	
	return Picosearch_Stemmer::Stem( $word );
}

//Prepares a string for indexing / searching
function picosearch_prepare( $text ){
	
	//Get a list of words
	$words =  picosearch_tokenize( $text );
	
	//Remove stopwords
	$words =  picosearch_remove_stopwords( $words );
	
	//Stem the words and return
	return array_map( 'picosearch_stem_word', $words );
}

//Tokenizes a string into words
function picosearch_tokenize( $text ){
	return explode( ' ', picosearch_normalize( $text ) );
}

//Nomarlizes a string
function picosearch_normalize( $text ){
	
	//Remove accents
	$text = remove_accents( $text );
	//Convert to lowecase
	$text = strtolower( $text );
	
	//Delete non alphanumeric characters except spaces
	//$text = preg_replace( '/^\da-z ]', '', $text ); Some languages dont use alphanumeric chars
	
	//Remove punctutions and return
	return picosearch_remove_punct( $text );

}

//Removes punctuations. From Relevanssi
function picosearch_remove_punct($a) {
    if (!is_string($a)) return "";  // In case something sends a non-string here.

	$a = preg_replace ('/<[^>]*>/', ' ', $a);

	$a = str_replace("\r", '', $a);    // --- replace with empty space
	$a = str_replace("\n", ' ', $a);   // --- replace with space
	$a = str_replace("\t", ' ', $a);   // --- replace with space

	$a = stripslashes($a);

	$a = str_replace('ß', 'ss', $a);

	$a = str_replace("·", ' ', $a);
	$a = str_replace("…", ' ', $a);
	$a = str_replace("€", '', $a);
	$a = str_replace("&shy;", '', $a);

	$a = str_replace(chr(194) . chr(160), ' ', $a);
	$a = str_replace("&nbsp;", ' ', $a);
	$a = str_replace('&#8217;', ' ', $a);
	$a = str_replace("'", '', $a);
	$a = str_replace("’", ' ', $a);
	$a = str_replace("‘", ' ', $a);
	$a = str_replace("”", ' ', $a);
	$a = str_replace("“", ' ', $a);
	$a = str_replace("„", ' ', $a);
	$a = str_replace("´", ' ', $a);
	$a = str_replace("—", ' ', $a);
	$a = str_replace("–", ' ', $a);
	$a = str_replace("×", ' ', $a);
    $a = preg_replace('/[[:punct:]]+/u', ' ', $a);

    $a = preg_replace('/[[:space:]]+/', ' ', $a);
	$a = trim($a);

    return $a;
}

/**
 * Reads the contents of the given pdf file
 *
 * @since       1.0.0
 * @return      string a string containing the pdfs content
 */
function picosearch_read_pdf( $path ){
	//TODO:
}

/**
  * Wrapper for $wpdb::prepare strings
  *
  * @since       1.0.0
  */
function picosearch_wpdb_prepare( $word ){
	global $wpdb;
	return $wpdb->prepare( '%s', $word );
}

/**
 * Proxy to $wpdb->get_results
 *
 * @since       1.0.0
 */
function picosearch_get_results( $sql ){
	global $wpdb;
	return $wpdb->get_results($sql);
}

/**
 * Proxy to $wpdb->get_col
 *
 * @since       1.0.0
 */
function picosearch_get_col( $sql ){
	global $wpdb;
	return $wpdb->get_col($sql);
}

/**
 * Proxy to $wpdb->get_var
 *
 * @since       1.0.0
 */
function picosearch_get_var( $sql ){
	global $wpdb;
	return $wpdb->get_var($sql);
}

/**
 * Proxy to $wpdb->query
 *
 * @since       1.0.0
 */
function picosearch_query( $sql ){
	global $wpdb;
	return $wpdb->query($sql);
}

/**
 * Deletes a table from the db
 *
 * @since       1.0.0
 */
function picosearch_delete_table( $table ){
	global $wpdb;
	return $wpdb->query( "DROP TABLE IF EXISTS $table" );
}

/**
 * Proxy to $wpdb->prepare
 *
 * @since       1.0.0
 */
function picosearch_prepare_string( $string ){
	global $wpdb;
	return $wpdb->prepare('%s', $string );;
}

/**
 * Returs total number of indexed posts
 *
 * @since       1.0.0
 */
function picosearch_total_indexed(){
	$index_table = picosearch()->index_table;
	return picosearch_get_var ( "SELECT COUNT(id) as total FROM $index_table" );
}

/**
 * Returns the id of the last indexed post
 *
 * @since       1.0.0
 */
function picosearch_last_indexed(){
	$index_table = picosearch()->index_table;
	return picosearch_get_var ( "SELECT MAX(id) as last FROM $index_table" );
}

/**
 * Returns Total number of searches matching the given condition
 *
 * @since       1.0.0
 */
function picosearch_total_searches( $conditions ='1 = 1' ){
	$searches_log_table = picosearch()->log_table;
	return picosearch_get_var ( "SELECT SUM(searches) as total FROM $searches_log_table WHERE {$conditions}" );
}

/**
 * Fetches previous searches
 *
 * @since       1.0.0
 */
function picosearch_previous_searches( $conditions ='1 = 1' ){
	$searches_log_table = picosearch()->log_table;
	return picosearch_get_results ( "SELECT * FROM $searches_log_table $conditions" );
}

/**
 * Generates a table of previous searches
 *
 * @since       1.0.0
 */
function picosearch_show_searches( $conditions ='1 = 1' ){
	$searches = picosearch_previous_searches( $conditions );
	
	if( !is_array( $searches ) )
		return __( "Nothing to display.", 'picosearch' );
	
	$return = '<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Query</th>
							<th>Searches</th>
							<th>Results</th>
						</tr>
					</thead>
					<tbody>';
	
	foreach($searches as $single){
		
		$return .= "<tr><td class=''>$single->query</td>";
		$return .= "<td class=''>$single->searches</td>";
		$return .= "<td class=''>$single->hits</td>";
		
	}
	
	$return .= '</tbody>
					</table>';
	return $return;
}

/**
 * Fetches indexed post ids
 *
 * @since       1.0.0
 */
function picosearch_indexed_ids(){
	$index_table = picosearch()->index_table;
	return picosearch_get_col ( "SELECT id FROM $index_table" );
}

/**
 * Deletes the index
 *
 * @since       1.0.0
 */
function picosearch_reindex(){
	global $wpdb;
	
	//Incase we are already indexing
	$indexer = new Picosearch_indexer();
	if ( $indexer->is_indexing() )
		$indexer->cancel_process();
	
	$index_table = picosearch()->index_table;
	$doc_table = picosearch()->doc_table;
	
	picosearch_query( "DELETE FROM $doc_table");
	picosearch_query( "DELETE FROM $doc_table");
	
	update_option('picosearch_indexed', '0');
}

/**
 * Fetches non-indexed posts
 *
 * @since       1.0.0
 */
function picosearch_non_indexed_posts( $count = -1 ){
	$args = array(
		'post_status' => array('publish'),
		'posts_per_page' => $count,
		'ignore_sticky_posts' => true,
		'order' => 'ASC',
		'orderby' => 'id',
		'post_type'=> picosearch_post_types(),
	);
				
	//If we are picosearcho indexing attachments
	if( in_array('attachment', picosearch_post_types()) ) {			
		$args['post_status'] = array('publish', 'inherit');					
	}
					
	$posts = new WP_Query($args);
	return $posts->posts;
}

/**
 * Indexes posts in the background
 *
 * @since       1.0.0
 */
function picosearch_index_posts(){
	$indexer = new Picosearch_indexer();
	
		//fetch posts
		global $wpdb;
		$restriction = " (post.post_status='publish' OR
			(post.post_status='inherit'
				AND(
					parent.ID is not null AND (parent.post_status='publish')
				)
			)) AND post.post_type NOT IN('nav_menu_item', 'revision')";
			
		$q = "SELECT post.ID as ID FROM {$wpdb->posts} post LEFT JOIN {$wpdb->posts} parent ON (post.post_parent=parent.ID) WHERE $restriction";
		$posts = array_unique( picosearch_get_col( $q ) );
		
		if ( is_array( $posts ) )
			$indexer->data( $posts );
		
		$indexer->save()->dispatch();	
}

/**
 * Adds a single post to the index
 *
 * @since       1.0.0
 */
function picosearch_index_post( $id ){
	//delete the post from the index incase it was already indexed
	//This function is found in the hooks file
	picosearch_delete_post( $id );
	
	if ( !$post = get_post ( $id ) )
		return;
	
	global $wpdb;
	$doc_table = picosearch()->doc_table;
	$index_table = picosearch()->index_table;
	
	//Content
	$content = picosearch_prepare( do_shortcode( $post->post_content ) );
	$content_words = count ( $content );
	  
	//Title
	$title = picosearch_prepare( $post->post_title );
	$title_words = count ( $title );
	
	//Excerpt
	$excerpt = picosearch_prepare( $post->post_excerpt );
	$excerpt_words = count ( $excerpt );
	
	//Extra 
	$post_type = $wpdb->prepare('%s', $post->post_type);
	$last_modified = $wpdb->prepare('%s', $post->post_modified);
	$author = $post->post_author;
	$comment_count = $post->comment_count;
	
		
	//Dont index empty posts
	$all_words = array_unique( array_merge( $content, $title, $excerpt ) );
	

	if ( empty ( $all_words ) )
		return;
	
	//Add the document to the documents table
	$sql = "INSERT INTO $doc_table (id, comment_count, content_length, title_length, excerpt_length, last_modified, author, post_type)
			VALUES ( $id, $comment_count, $content_words, $title_words, $excerpt_words, $last_modified, $author, $post_type ) ";
	 
	picosearch_query( $sql );
	//Count word occurences in our arrays
	$content = array_count_values( $content );
	$title = array_count_values( $title );
	$excerpt = array_count_values( $excerpt );
	
	$values_to_insert = array();
	foreach( $all_words as $word ) {
		$word = trim($word);
		
		if ( empty( $word ) )
			continue;
		
		$content_count = 0;
		$title_count = 0;
		$excerpt_count = 0;
		
		if ( array_key_exists( $word, $content ) )
			$content_count = $content[ $word ];
		
		if ( array_key_exists( $word, $title ) )
			$title_count = $title[ $word ];
		
		if ( array_key_exists( $word, $excerpt ) )
			$excerpt_count = $excerpt[ $word ];
		
		$value = $wpdb->prepare("(%d, %s, %d, %d, %d)",
			$post->ID, $word, $title_count, $content_count, $excerpt_count );
		
		array_push($values_to_insert, $value);
	}
	 
	if ( !empty( $values_to_insert ) ) {
		
		$values_to_insert = implode(', ', $values_to_insert);
		$sql = "INSERT IGNORE INTO $index_table (post_id, word, title, content, excerpt) VALUES $values_to_insert";
		picosearch_query( $sql );
		
	}
	
}

/**
 * Logs a given search query
 * @since       1.0.0
 * @return      void
 */
function picosearch_log_query( $q, $results_count ) {
	$q = picosearch_prepare_string( $q );
	$results_count = absint( $results_count );
	
	$searches_log_table = picosearch()->log_table;
	$sql = "INSERT IGNORE INTO $searches_log_table (query, hits) VALUES ($q, $results_count)";
					
	$exists = picosearch_get_results( "SELECT searches as searches FROM {$searches_log_table} WHERE LOWER(query)=LOWER({$q})" );
				
	if( !empty( $exists ) ) {
		
		$searches = intval($exists[0]->searches) + 1;
		$sql = "UPDATE $searches_log_table SET searches=$searches, hits=$results_count WHERE LOWER(query)=LOWER($q)";			

	}
	return picosearch_query( $sql );
}

/**
 * Returns Total number of searches
 *
 * @since       1.0.0
 * @return      int number of searches
 */
function total_searches ( $conditions ='1 = 1' )  {
				
	$searches_log_table = picosearch()->log_table;
	$sql = "SELECT SUM(searches) as total FROM $searches_log_table WHERE $conditions";
	return picosearch_get_var($sql);
				
}

/**
 * Searches for posts matching a term
 *
 * @since       1.0.0
 */
function picosearch_search( $q ){
	
	//Get a list of stemmed terms in the query minus stopwords
	$q = picosearch_prepare( $q );
	
	//Maybe abort
	if( empty( $q ) )
		return array();
	
	$results = apply_filters ( 'picosearch_custom_search_results', array(), $q );
	if (!empty( $results ) )
		return $results;
	
	$q = array_map( 'picosearch_wpdb_prepare', $q);

	//Tables fix hardcoding the wp_ as reported by @Cherryaustin
	$doc_table = picosearch()->doc_table;
	$index_table = picosearch()->index_table;

	$score = picosearch_doc_weight_bm25f( $q );
	$words = implode( ', ' , $q);
	$restrictions = " ind.word IN ($words)";
	$sql = "SELECT docs.id, SUM($score) as score FROM $index_table as ind		
			INNER JOIN $doc_table AS docs ON (ind.post_id=docs.id)	
			WHERE $restrictions 	
			GROUP BY ind.post_id
			ORDER BY score DESC LIMIT 100";
	$results = picosearch_get_col($sql);

	//Fires after a search is completed
	do_action('picosearch_complete_search', $q, $results);
	
	return $results;
}

 /**
  * Calculates Weight for a given document using a bm25f model
  *
  * @since       1.0.0
  */
function picosearch_doc_weight_bm25f( $words ){
	
	$return = '';
	$k1 = 1.2;
	
	foreach( $words as $word ) {
		$idf =picosearch_word_idf( $word );
		$wtd = picosearch_wtd();
		$post_type_weight = picosearch_post_type_weight();
		$boost = apply_filters( 'picosearch_search_boost', '1'  );
		
		$return .= "( $boost * $post_type_weight * ( $wtd / ($k1 + $wtd )) * $idf )+";
	}
	return rtrim($return, '+');
}

 /**
  * Calculates Weight of a given word relative to the current document
  *
  * @since       1.0.0
  */
function picosearch_wtd(){
	$return = '';
	$fields = array( 'content', 'title', 'excerpt' );
	
	foreach ( $fields as $field ) {
		$field_boost = picosearch_get_option( "picosearch-$field-weight" );
		$b = 0.75;
		$avg_field_length = 100;
		$return .= "((ind.$field * $field_boost) / ( (1-$b) + $b * docs.{$field}_length / $avg_field_length ))+";
	}
	return rtrim($return, '+');
	
}

 /**
  * Calculates a post type weight
  *
  * @since       1.0.0
  */
function picosearch_post_type_weight(){
	$post_types = picosearch_post_types();
	$score = '(0';
	
	foreach( $post_types as $type ){
		$weight = floatval(picosearch_get_option ( "picosearch-{$type}-weight" ));
		
		if( $type == 'pc_sponsored_result' )
			$weight = 1000;
		
		$score .= "+((post_type IN('{$type}'))*{$weight})";
	}
	
	return "$score)";
}

 /**
  * Calculates a words idf
  *
  * @since       1.0.0
  */
function picosearch_word_idf( $term = '' ){
	//Tables fix hardcoding the wp_ as reported by @Cherryaustin
	$doc_table = picosearch()->doc_table;
	$index_table = picosearch()->index_table;
	return "( LOG( (( SELECT COUNT(*) FROM $doc_table ) + 0.5 )/ ((SELECT COUNT(*) FROM $index_table WHERE word = $term ) + 0.5)) + 1)";
}

 /**
  * Calculates Weight for a given document using a tfidf model
  *
  * @since       1.0.0
  *
  * @return      int the calculated score
  */
function picosearch_doc_weight_tfidf( $words ){
	$return = '';
	$fields = array( 'content', 'title', 'excerpt' );
	
	foreach( $words as $word ) {
		$idf =picosearch_word_idf( $word );
		foreach ( $fields as $field ) {
			//TODO: Optiomize this function for structured documents
			$return .= "( ( 1 / SQRT( docs.{$field}_length + 1 ) ) * ($idf) * SQRT( ind.$field + 1 ))+"; 
		}		
	}
	return rtrim($return, '+');
	
}

 /**
  * Implements the DEFREE scoring model
  *
  * @since       1.0.0
  * @var $tf int term frequency in the current document
  * @var $cf int term frequency in the whole collection
  * @var $qf int term frequency in the query
  * @var $dl int field length of current document
  * @var $fl int total field length across all documents in collection
  *
  * @link http://terrier.org/
  * @return      int the calculated score
  */
function picosearch_doc_weight_defree( $tf=1, $cf=1, $qf=1, $dl=1, $fl=1 ){
	$prior = $tf / $dl;
    $post = ($tf + 1.0) / ($dl + 1.0);
    $invpriorcol = $fl / $cf ;
    $norm = $tf * log($post / $prior);

    return $qf * $norm * ($tf * (log($prior * $invpriorcol))
                        + ($tf + 1.0) * (log($post * $invpriorcol))
                        + 0.5 * log($post / $prior));
}

 /**
  * Implements the PL2 scoring model
  *
  * @since       1.0.0
  * @var $tf int term frequency in the current document
  * @var $cf int term frequency in the whole collection
  * @var $qf int term frequency in the query
  * @var $dc int Total documents
  * @var $fl int field length in the current document
  * @var $avgfl int average field length across all documents
  * @var $c int Weighting parameter
  *
  * @link http://terrier.org/
  * @return      int the calculated score
  */
function picosearch_pl2( $tf, $cf, $qf, $dc, $fl, $avgfl, $c ){
	$TF = $tf * log( 1.0 + ( $c * $avgfl ) / $fl );
    $norm = 1.0 / ( $TF + 1.0 );
    $f = $cf / $dc;
	$rec_log2_of_e = 1.0 / log(2);
	
    return $norm * $qf * ($TF * log(1.0 / $f)
                        + $f * $rec_log2_of_e
                        + 0.5 * log(2 * pi * TF)
                        + TF * (log($TF) - $rec_log2_of_e));
}

/**
 * Clean variables using sanitize_text_field.
 * @param string|array $var text to be cleaned
 * @return string|array
 * @ since 1.0.0
 */

function picosearch_clean( $var ) {
	return is_array( $var ) ? array_map( 'picosearch_clean', $var ) : sanitize_text_field( $var );
}

/**
 * Generates the buy button
 * @ since 1.0.3
 */

function picosearch_print_buy_button() {
	echo'
		<a href = "https://sellfy.com/p/88pS/" id="88pS" class="sellfy-buy-button" data-text=""></a>
	';
}