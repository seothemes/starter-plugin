<?php

namespace SeoThemes\StarterPlugin\Providers;

use SeoThemes\StarterPlugin\Provider;

/**
 * Class I18n
 *
 * @package \SeoThemes\StarterPlugin\Providers
 */
class I18n extends Provider {

	public function hooks() {
		\add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
	}

	public function load_textdomain() {
		load_plugin_textdomain( $this->plugin->handle, false, $this->plugin->dir . 'assets/lang' );
	}

}
