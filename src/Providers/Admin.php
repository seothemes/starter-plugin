<?php

namespace SeoThemes\StarterPlugin\Providers;

use SeoThemes\StarterPlugin\Plugin;
use SeoThemes\StarterPlugin\Provider;

/**
 * Class ExampleService
 *
 * @package SeoThemes\StarterPlugin
 */
class Admin extends Provider {

	protected function conditional() {
		return is_admin();
	}

	public function hooks() {
		\add_action( 'admin_init', [ $this, 'run' ] );
	}

	public function run() {
		echo 'Running in the admin';
	}
}
