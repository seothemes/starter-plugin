<?php

namespace SeoThemes\StarterPlugin\Providers;

use SeoThemes\StarterPlugin\Provider;

class Frontend extends Provider {

	protected function conditional() {
		return ! is_admin();
	}

	public function hooks() {
		\add_action( 'wp', [ $this, 'run' ] );
	}

	public function run() {
		echo 'Running on the frontend <br>';
		echo $this->plugin->handle;

		return 'running method';
	}
}
