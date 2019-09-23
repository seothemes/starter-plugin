<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Provider
 *
 * @package SeoThemes\StarterPlugin\Providers
 */
abstract class Provider {

	/**
	 * @var string
	 */
	const SETUP = 'setup';

	/**
	 * @var Plugin $plugin
	 */
	protected $plugin;

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	private function conditional() {
		return true;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function setup( Plugin $plugin ) {
		if ( $this->conditional() ) {
			$this->plugin = $plugin;
			$this->hooks();
		}
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function hooks();
}
