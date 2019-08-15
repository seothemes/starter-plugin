<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Textdomain
 *
 * @package SeoThemes\StarterPlugin
 */
class Textdomain extends Service {

	/**
	 * Register action hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Loads plugin text domain.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->plugin->handle, false, $this->plugin->lang );
	}
}
