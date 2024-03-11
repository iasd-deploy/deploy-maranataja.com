<?php

namespace Jet_Engine\Modules\Custom_Content_Types\Bricks_Views;

use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Manager {

	public function __construct() {
		if ( ! $this->has_bricks() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_providers' ), 10 );
	}

	public function register_providers() {
		require_once Module::instance()->module_path( 'bricks-views/dynamic-data/providers.php' );
		require_once Module::instance()->module_path( 'bricks-views/dynamic-data/provider.php' );

		Dynamic_Data\Providers::register(['content-types']);
	}

	public function has_bricks() {
		return ( defined( 'BRICKS_VERSION' ) && \Jet_Engine\Modules\Performance\Module::instance()->is_tweak_active( 'enable_bricks_views' ) );
	}
}