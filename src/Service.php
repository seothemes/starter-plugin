<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Service
 *
 * @package SeoThemes\StarterPlugin
 */
abstract class Service {

	/**
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * Service constructor.
	 *
	 * @param Plugin $plugin Plugin object.
	 */
	public function __construct( $plugin, $args ) {
		$this->plugin = $plugin;
		$this->args   = $args;
	}

	/**
	 * Run hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function register();
}
