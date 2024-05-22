<?php

namespace Jet_Engine\Bricks_Views;


class Query_Controller {
	public $initial_object = null;

	function __construct() {
		add_filter( 'bricks/query/run', array( $this, 'run_query' ), 10, 2 );
		add_filter( 'bricks/query/loop_object', array( $this, 'set_loop_object' ), 10, 3 );
		add_action( 'bricks/query/after_loop', array( $this, 'reset_current_object' ), 10 );
	}

	public function run_query( $results, $query ) {
		if ( $query->object_type !== 'jet_engine_query_builder' ) {
			return $results;
		}

		$jet_engine_query = $this->get_jet_engine_query( $query->settings );

		// Return empty results if query not found in JetEngine Query Builder
		if ( ! $jet_engine_query ) {
			return $results;
		}

		// Setup query args
		$jet_engine_query->setup_query();

		// Get current object for generating dynamic style in Listing grid
		if ( $query->element_id === 'jet-listing-elements' ) {
			$this->initial_object = jet_engine()->listings->data->get_current_object();
		}

		// Get the results
		return $jet_engine_query->get_items();
	}

	public function set_loop_object( $loop_object, $loop_key, $query ) {
		if ( $query->object_type !== 'jet_engine_query_builder' ) {
			return $loop_object;
		}

		global $post;

		// I only tested on JetEngine Posts Query, Terms Query, Comments Query and WC Products Query
		// I didn't set WP_Term condition because it's not related to the $post global variable
		if ( is_a( $loop_object, 'WP_Post' ) ) {
			$post = $loop_object;
		} elseif ( is_a( $loop_object, 'WC_Product' ) ) {
			// $post should be a WP_Post object
			$post = get_post( $loop_object->get_id() );
		} elseif ( is_a( $loop_object, 'WP_Comment' ) ) {
			// A comment should refer to a post, so I set the $post global variable to the comment's post
			// You might want to change this to $loop_object->comment_ID
			$post = get_post( $loop_object->comment_post_ID );
		}

		setup_postdata( $post );

		$jet_engine_query = $this->get_jet_engine_query( $query->settings );

		// Return empty results if query not found in JetEngine Query Builder
		if ( ! $jet_engine_query ) {
			return $loop_object;
		}

		// Set current object for JetEngine
		jet_engine()->listings->data->set_current_object( $loop_object );

		// We still return the $loop_object so \Bricks\Query::get_loop_object() can use it
		return $loop_object;
	}

	public function reset_current_object( $query ) {
		if ( $query->object_type !== 'jet_engine_query_builder' ) {
			return false;
		}

		$jet_engine_query = $this->get_jet_engine_query( $query->settings );

		if ( ! $jet_engine_query ) {
			return false;
		}

		// Set initial object for generating dynamic style in Listing grid
		if ( ! empty( $this->initial_object ) ) {
			jet_engine()->listings->data->set_current_object( $this->initial_object );
		} else {
			// Reset current object
			jet_engine()->listings->data->reset_current_object();
		}
	}

	public function get_jet_engine_query( $settings ) {
		$jet_engine_query_builder_id = ! empty( $settings['jet_engine_query_builder_id'] ) ? absint( $settings['jet_engine_query_builder_id'] ) : 0;

		// Return empty results if no query selected or Use Query is not checked
		if ( $jet_engine_query_builder_id === 0 || ! $settings['hasLoop'] ) {
			return false;
		}

		$query_builder = \Jet_Engine\Query_Builder\Manager::instance();

		// Get the query object from JetEngine based on the query id
		return $query_builder->get_query_by_id( $jet_engine_query_builder_id );
	}
}