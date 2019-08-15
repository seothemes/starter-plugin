<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Hooks
 *
 * @package SeoThemes\StarterPlugin
 */
class Hooks extends Service {

	/**
	 * Register miscellaneous hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
	}

	/**
	 * Registers icon widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_widget() {
		\register_widget( __NAMESPACE__ . '\Widget' );
	}
}
