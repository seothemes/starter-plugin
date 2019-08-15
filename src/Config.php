<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Config
 *
 * @package SeoThemes\StarterPlugin
 */
class Config {

	/**
	 * @var
	 */
	public $file;

	/**
	 * @var array
	 */
	private $files;

	/**
	 * @var array
	 */
	private $all;

	/**
	 * Config constructor.
	 *
	 * @param $file
	 */
	public function __construct( $file ) {
		$this->file  = $file;
		$this->files = glob( dirname( $this->file ) . '/config/*.php' );
		$this->all   = $this->get_config();
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_config() {
		$config = [];

		foreach ( $this->files as $file ) {
			if ( is_readable( $file ) ) {
				$config[ basename( $file, '.php' ) ] = require $file;
			}
		}

		return $config;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function get_sub_config( $name ) {
		return array_key_exists( $name, $this->all ) ? $this->all[ $name ] : [];
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function __get( $name ) {
		return $this->get_sub_config( $name );
	}
}
