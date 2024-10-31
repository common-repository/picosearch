<?php

    /**
     * Handles background indexing of posts
     *
	 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
	 * indexing in the background.
     * @since       1.0.0
     */

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
	
if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once( 'wp-async-request.php' );
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once( 'wp-background-process.php' );
}


class Picosearch_indexer extends WP_Background_Process{

	/**
	 * @var string
	 */
	protected $action = 'indexer';
	
	/**
	 * Prefix
	 *
	 * (default value: 'wp')
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'picosearch';

	/**
	 * Is the indexer running?
	 * @return boolean
	 */
	public function is_indexing() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function
	 * @return mixed
	 */
	protected function task( $post_id ) {
		picosearch_index_post( $post_id);
		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		update_option('picosearch_indexed', '1');
		parent::complete();
	}
	
	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 50%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.5; // 50% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_memory_exceeded', $return );
	}
	
	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + apply_filters( $this->identifier . '_default_time_limit', 10 ); // 10 seconds
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_time_exceeded', $return );
	}
}