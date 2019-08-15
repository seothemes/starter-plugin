<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Enqueue
 *
 * @package SeoThemes\StarterPlugin
 */
class Enqueue extends Service {

	/**
	 * Enqueue constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_print_styles', [ $this, 'load_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ] );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_admin_styles() {
		if ( ! is_customize_preview() && get_current_screen()->id !== 'widgets' ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin->prefix . '-bootstrap',
			$this->plugin->url . 'assets/css/admin.css'
		);
	}

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_admin_scripts() {
		if ( ! is_customize_preview() && get_current_screen()->id !== 'widgets' ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin->prefix . '-admin',
			$this->plugin->url . 'assets/js/admin.js',
			[ 'jquery' ]
		);
	}
}
