<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Factory
 *
 * @package SeoThemes\StarterPlugin
 */
class Factory {

	/**
	 * Factory method to return single instance of plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param Config $config
	 *
	 * @return object
	 */
	public function create( $config = '' ) {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Plugin( $config );
		}

		return $instance;
	}

	/**
	 * Alias for `create` method.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_instance() {
		return $this->create();
	}
}
