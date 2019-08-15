<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class App
 *
 * @package SeoThemes\StarterPlugin
 */
class Plugin {

	/**
	 * @var array
	 */
	public $config;

	/**
	 * @var string
	 */
	public $file;

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
	public $path;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var string
	 */
	public $handle;

	/**
	 * @var string
	 */
	public $lang;

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * App constructor.
	 *
	 * @param Config $config App config.
	 */
	public function __construct( $config ) {
		$this->config  = $config;
		$this->file    = $config->file;
		$this->dir     = trailingslashit( dirname( $this->file ) );
		$this->url     = trailingslashit( plugin_dir_url( $this->file ) );
		$this->path    = trailingslashit( basename( $this->dir ) ) . basename( $this->file );
		$this->data    = get_file_data( $this->file, $this->config->headers, 'plugin' );
		$this->name    = $this->data['Name'];
		$this->version = $this->data['Version'];
		$this->handle  = $this->data['TextDomain'];
		$this->lang    = trailingslashit( $this->data['DomainPath'] );
		$this->prefix  = str_replace( '-', '_', $this->handle );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate() {
		$this->register_services();
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
		$this->register_services();
		\flush_rewrite_rules();
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function register( $hook ) {
		add_action( $hook, [ $this, 'register_services' ] );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_services() {
		foreach ( $this->config->services as $service => $args ) {
			$this->$service = new $service( $this, $args );

			if ( method_exists( $service, 'register' ) ) {
				$this->$service->register();
			}
		}
	}
}
