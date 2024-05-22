<?php
/**
 * Bricks views manager
 */
namespace Jet_Engine\Modules\Calendar\Bricks_Views;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {
	/**
	 * Elementor Frontend instance
	 *
	 * @var null
	 */
	public $frontend = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {
		add_action( 'jet-engine/bricks-views/init', array( $this, 'init' ), 10 );
		add_action( 'jet-engine/bricks-views/listing/before-css-generation', array( $this, 'before_css_generation' ), 10 );
		add_action( 'jet-engine/bricks-views/listing/after-css-generation', array( $this, 'after_css_generation' ), 10 );
	}

	public function init() {
		add_action( 'jet-engine/bricks-views/register-elements', array( $this, 'register_elements' ), 11 );
	}

	public function register_elements() {
		\Bricks\Elements::register_element( $this->module_path( 'calendar.php' ) );
	}

	public function module_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/modules/calendar/bricks-views/' . $relative_path );
	}

	public function before_css_generation() {
		add_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ), 10, 3 );
	}

	public function after_css_generation() {
		remove_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ) );
	}

	public function add_date_args_to_custom_query( $query, $settings ) {
		if ( ! isset( $settings['start_from_year'] ) ) {
			return false;
		}

		$render_instance = jet_engine()->listings->get_render_instance( 'listing-calendar', $settings );

		$render_instance->query_instance = $query;
		$query->final_query = $render_instance->add_calendar_query( $query->final_query );

		// Reset query if it was stored before.
		$query->reset_query();
	}
}