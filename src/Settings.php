<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Settings
 *
 * @package SeoThemes\StarterPlugin
 */
class Settings extends Service {

	/**
	 * Runs class hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
		add_filter( "plugin_action_links_{$this->plugin->path}", [ $this, 'action_links' ] );
	}

	/**
	 * Add settings menu item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_options_page(
			$this->plugin->name,
			$this->plugin->name,
			'manage_options',
			$this->plugin->prefix,
			[ $this, 'options_page' ]
		);
	}

	/**
	 * Initialize settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings_init() {
		register_setting( $this->plugin->prefix . '_setting', $this->plugin->prefix . '_settings' );

		add_settings_section(
			$this->plugin->prefix . '_section',
			'',
			'',
			$this->plugin->prefix . '_setting'
		);

		add_settings_field(
			$this->plugin->prefix . '_font',
			__( 'Setting', 'starter-plugin' ),
			[ $this, 'render_fonts' ],
			$this->plugin->prefix . '_setting',
			$this->plugin->prefix . '_section'
		);
	}

	/**
	 * Display options page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( $this->plugin->name ); ?></h1>
			<form action='options.php' method='post'>
				<?php
				settings_fields( $this->plugin->prefix . '_setting' );
				do_settings_sections( $this->plugin->prefix . '_setting' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Adds plugin settings link.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $links Plugin links.
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		$settings[] = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=' . $this->plugin->prefix ),
			__( 'Settings', 'starter-plugin' )
		);

		return array_merge( $links, $settings );
	}
}
