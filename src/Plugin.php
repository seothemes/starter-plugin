<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Plugin
 *
 * @package SeoThemes\StarterPlugin
 */
final class Plugin {

	/**
	 * @var string
	 */
	const MIN_PHP_VERSION = '5.6';

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $base;

	/**
	 * @var string
	 */
	public $handle;

	/**
	 * @var string
	 */
	public $dir;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * Plugin constructor.
	 *
	 * @param string $file Path to main plugin file.
	 *
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file   = $file;
		$this->base   = \plugin_basename( $file );
		$this->handle = \basename( $file, '.php' );
		$this->dir    = \trailingslashit( \dirname( $file ) );
		$this->url    = \trailingslashit( \plugin_dir_url( $file ) );
		$this->name   = \ucwords( \str_replace( '-', ' ', $this->handle ) );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate() {
		\flush_rewrite_rules();
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function deactivate() {
		\flush_rewrite_rules();
	}
}
