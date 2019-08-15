<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Widget
 *
 * @package SeoThemes\StarterPlugin
 */
class Widget extends \WP_Widget {

	/**
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		$this->plugin = ( new Factory() )->get_instance();

		parent::__construct(
			$this->plugin->handle,
			$this->plugin->name,
			[
				'classname'   => $this->plugin->handle,
				'description' => __( 'Displays a title and description.', 'starter-plugin' ),
			]
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     The array of form elements.
	 * @param array $instance The current instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->plugin->id;
		}

		$html      = $args['before_widget'];
		$shortcode = '[starter-plugin ';
		$params    = [
			'title',
		];

		foreach ( $params as $param ) {
			$value     = isset( $instance[ $param ] ) ? $instance[ $param ] : '';
			$shortcode .= $value ? $param . '="' . htmlentities( $value ) . '" ' : '';
		}

		$shortcode .= ']';
		$html      .= do_shortcode( $shortcode );
		$html      .= $args['after_widget'];

		echo $html;
	}

	/**
	 * Process the widget's options to be saved.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The new instance of values to be generated via the
	 *                            update.
	 * @param array $old_instance The previous instance of values before the update.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Update widget's old values with new incoming values.
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The array of keys and values for the widget.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = apply_filters( $this->plugin->prefix . '_defaults', [
			'title' => '',
		] );

		// Define default values for your variables.
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Store the values of the widget in their own variable.
		$title = $instance['title'];

		require $this->plugin->dir . 'assets/views/widget.php';
	}
}
