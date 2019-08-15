<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Shortcode
 *
 * @package SeoThemes\StarterPlugin
 */
class Shortcode extends Service {

	/**
	 * Runs class hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'starter_plugin', [ $this, 'add_shortcode' ] );
	}

	/**
	 * Add Shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function add_shortcode( $atts ) {
		$atts = shortcode_atts(
			apply_filters( 'starter_plugin_defaults', [
				'title' => $this->plugin->name,
			] ),
			$atts,
			'starter_plugin'
		);

		// Store variables.
		$title = $atts['title'];

		// Build HTML.
		$html = sprintf( '<h3 class="widget-title">%s</h3>', $title );

		return apply_filters( 'starter_plugin_html_output', $html );
	}
}
