<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Elements_Templates_Manager' ) ) {

	/**
	 * Define Jet_Elements_Templates_Manager class
	 */
	class Jet_Elements_Templates_Manager {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Categories option name
		 * @var string
		 */
		protected $option = 'jet_elements_categories';

		/**
		 * Templates option name
		 * @var string
		 */
		protected $templates_option = 'jet_elements_templates';

		/**
		 * JetImpex templates API server
		 *
		 * @var string
		 */
		// Test: http://192.168.9.40/_2018/03_March/templates-server/
		// Live: http://jetelements.zemez.io/
		public static $api_server = 'http://jetelements.zemez.io/';

		/**
		 * JetImpex templates API route
		 *
		 * @var string
		 */
		public static $api_route = 'wp-json/jet/v1';

		/**
		 * Constructor for the class
		 */
		public function init() {

			$use_templates = jet_elements_settings()->get( 'jet_templates', 'enabled' );

			if ( 'enabled' !== $use_templates ) {
				return;
			}

			// Register jet-templates source
			add_action( 'elementor/init', array( $this, 'register_templates_source' ) );

			if ( defined( 'Elementor\Api::LIBRARY_OPTION_KEY' ) ) {
				// Add JetImpex templates to Elementor templates list
				add_filter( 'option_' . Elementor\Api::LIBRARY_OPTION_KEY, array( $this, 'prepend_categories' ) );
			}

			// Process JetImpext template request
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.2.8', '>' ) ) {
				add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ), 20 );
			} else {
				add_action( 'wp_ajax_elementor_get_template_data', array( $this, 'force_jet_template_source' ), 0 );
			}

			if ( defined( 'ELEMENTOR_VERSION' )
				 && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' )
				 && version_compare( ELEMENTOR_VERSION, '3.10.0', '<' )
			) {
				add_filter( 'option_' . Elementor\Api::LIBRARY_OPTION_KEY, array( $this, 'prepend_templates' ) );
			}

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.10.0', '>=' ) ) {
				$transient_key = Elementor\TemplateLibrary\Source_Remote::TEMPLATES_DATA_TRANSIENT_KEY_PREFIX . ELEMENTOR_VERSION;
				add_filter( 'transient_' . $transient_key, array( $this, 'prepend_remote_templates' ) );
			}
		}

		/**
		 * Register
		 *
		 * @return [type] [description]
		 */
		public function register_templates_source() {

			require jet_elements()->plugin_path( 'includes/template-library/class-jet-elements-templates-source.php' );

			$elementor = Elementor\Plugin::instance();
			$elementor->templates_manager->register_source( 'Jet_Elements_Templates_Source' );

		}

		/**
		 * Return transient key
		 *
		 * @return [type] [description]
		 */
		public function transient_key() {
			return $this->option . '_' . jet_elements()->get_version();
		}

		public function templates_transient_key() {
			return $this->templates_option . '_' . jet_elements()->get_version();
		}

		/**
		 * Retrieves categories list
		 *
		 * @return [type] [description]
		 */
		public function get_categories() {

			$categories = get_transient( $this->transient_key() );

			if ( ! $categories ) {
				$categories = $this->remote_get_categories();
				set_transient( $this->transient_key(), $categories, WEEK_IN_SECONDS );
			}

			return $categories;
		}

		/**
		 * Get categories from JetElements API
		 *
		 * @return array
		 */
		public function remote_get_categories() {

			$url      = self::$api_server . self::$api_route . '/categories/';
			$response = wp_remote_get( $url, array( 'timeout' => 60 ) );
			$body     = wp_remote_retrieve_body( $response );
			$body     = json_decode( $body, true );

			return ! empty( $body['data'] ) ? $body['data'] : array();

		}

		/**
		 * Add JetImpex templates to Elementor templates list
		 *
		 * @param  [type] $templates [description]
		 * @return [type]            [description]
		 */
		public function prepend_categories( $library_data ) {

			$categories = $this->get_categories();

			if ( ! empty( $categories ) ) {

				if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.3.9', '>' ) ) {
					$library_data['types_data']['block']['categories'] = array_merge( $categories, $library_data['types_data']['block']['categories'] );
				} else {
					$library_data['categories'] = array_merge( $categories, $library_data['categories'] );
				}

				return $library_data;

			} else {
				return $library_data;
			}

		}

		public function prepend_templates( $library_data ) {

			$templates = $this->get_templates();

			if ( ! empty( $templates ) ) {
				$library_data['templates'] = array_merge( $library_data['templates'], $templates );
			}

			return $library_data;
		}

		public function prepend_remote_templates( $remote_templates ) {

			$templates = $this->get_templates();

			if ( ! empty( $templates ) && is_array( $remote_templates ) ) {
				$remote_templates = array_merge( $remote_templates, $templates );
			}

			return $remote_templates;
		}

		public function get_templates() {

			$templates = get_transient( $this->templates_transient_key() );

			if ( ! $templates ) {
				$source = Elementor\Plugin::instance()->templates_manager->get_source( 'jet-templates' );
				
				if ( $source ) {
					$templates = $source->get_items();

					if ( ! empty( $templates ) ) {

						$templates = array_map( function ( $template ) {

							$template['id'] = $template['template_id'];
							$template['tmpl_created'] = $template['date'];
							$template['tags'] = json_encode( $template['tags'] );
							$template['is_pro'] = $template['isPro'];
							$template['access_level'] = $template['accessLevel'];
							$template['popularity_index'] = $template['popularityIndex'];
							$template['trend_index'] = $template['trendIndex'];
							$template['has_page_settings'] = $template['hasPageSettings'];

							unset( $template['template_id'] );
							unset( $template['date'] );
							unset( $template['isPro'] );
							unset( $template['accessLevel'] );
							unset( $template['popularityIndex'] );
							unset( $template['trendIndex'] );
							unset( $template['hasPageSettings'] );

							return $template;
						}, $templates );

						set_transient( $this->templates_transient_key(), $templates, WEEK_IN_SECONDS );

					} else {
						$templates = array();
					}
				}
			}

			return $templates;
		}

		/**
		 * Register AJAX actions
		 *
		 * @param $ajax
		 */
		public function register_ajax_actions( $ajax ) {
			if ( ! isset( $_REQUEST['actions'] ) ) {
				return;
			}

			$actions = json_decode( stripslashes( $_REQUEST['actions'] ), true );
			$data    = false;

			foreach ( $actions as $id => $action_data ) {
				if ( ! isset( $action_data['get_template_data'] ) ) {
					$data = $action_data;
				}
			}

			if ( ! $data ) {
				return;
			}

			if ( ! isset( $data['data'] ) ) {
				return;
			}

			$data = $data['data'];

			if ( empty( $data['template_id'] ) ) {
				return;
			}

			if ( false === strpos( $data['template_id'], 'jet_' ) ) {
				return;
			}

			$ajax->register_ajax_action( 'get_template_data', array( $this, 'get_jet_template_data' ) );
		}

		/**
		 * Get jet template data.
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public function get_jet_template_data( $args ) {

			$source = Elementor\Plugin::instance()->templates_manager->get_source( 'jet-templates' );

			$data = $source->get_data( $args );

			return $data;
		}

		/**
		 * Return jet template data insted of elementor template.
		 *
		 * @return [type] [description]
		 */
		public function force_jet_template_source() {

			if ( empty( $_REQUEST['template_id'] ) ) {
				return;
			}

			if ( false === strpos( $_REQUEST['template_id'], 'jet_' ) ) {
				return;
			}

			$_REQUEST['source'] = 'jet-templates';

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Elements_Templates_Manager
 *
 * @return object
 */
function jet_elements_templates_manager() {
	return Jet_Elements_Templates_Manager::get_instance();
}
