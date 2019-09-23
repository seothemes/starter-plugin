<?php

namespace SeoThemes\StarterPlugin;

/**
 * Class Container
 *
 * @package SeoThemes\StarterPlugin
 */
final class Container {

	const CONSTANT         = 'constant';
	const GLOBALS          = 'globals';
	const INSTANCE         = 'instance';
	const CHAIN_CALL       = 'chainCall';
	const SHARED           = 'shared';
	const INHERIT          = 'inherit';
	const CONSTRUCT_PARAMS = 'constructParams';
	const SUBSTITUTIONS    = 'substitutions';
	const CALL             = 'call';
	const INSTANCE_OF      = 'instanceOf';
	const SHARE_INSTANCES  = 'shareInstances';
	const PARAMS           = 'params';
	const ALL              = '*';

	/**
	 * @var array
	 */
	private $rules = [];

	/**
	 * @var array
	 */
	private $cache = [];

	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 * @param array  $rule
	 *
	 * @return Container
	 */
	public function add_rule( $name, array $rule ) {
		$container = clone $this;
		$this->add_rule_to( $container, $name, $rule );

		return $container;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param $rules
	 *
	 * @return Container
	 */
	public function add_rules( $rules ) {
		if ( is_string( $rules ) ) {
			$rules = json_decode( file_get_contents( $rules ), true );
		}
		$container = clone $this;
		foreach ( $rules as $name => $rule ) {
			$this->add_rule_to( $container, $name, $rule );
		}

		return $container;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param Container $container
	 * @param string    $name
	 * @param array     $rule
	 *
	 * @return void
	 */
	private function add_rule_to( Container $container, $name, array $rule ) {
		if ( isset( $rule[ self::INSTANCE_OF ] ) && ( ! array_key_exists( self::INHERIT, $rule ) || $rule[ self::INHERIT ] === true ) ) {
			$rule = array_replace_recursive( $container->get_rule( $rule[ self::INSTANCE_OF ] ), $rule );
		}
		if ( isset( $rule[ self::SUBSTITUTIONS ] ) ) {
			foreach ( $rule[ self::SUBSTITUTIONS ] as $key => $value ) {
				$rule[ self::SUBSTITUTIONS ][ ltrim( $key, '\\' ) ] = $value;
			}
		}
		unset( $container->instances[ $name ], $container->cache[ $name ] );
		$container->rules[ ltrim( strtolower( $name ), '\\' ) ] = array_replace_recursive( $container->get_rule( $name ), $rule );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function get_rule( $name ) {
		$lcName = strtolower( ltrim( $name, '\\' ) );
		if ( isset( $this->rules[ $lcName ] ) ) {
			return $this->rules[ $lcName ];
		}

		foreach ( $this->rules as $key => $rule ) {
			if ( empty( $rule[ self::INSTANCE_OF ] )
			     && $key !== self::ALL
			     && is_subclass_of( $name, $key )
			     && ( ! array_key_exists( self::INHERIT, $rule ) || $rule[ self::INHERIT ] === true ) ) {
				return $rule;
			}
		}

		return isset( $this->rules[ self::ALL ] ) ? $this->rules[ self::ALL ] : [];
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 * @param array  $args
	 * @param array  $share
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function create( $name, array $args = [], array $share = [] ) {
		if ( ! empty( $this->instances[ $name ] ) ) {
			return $this->instances[ $name ];
		}

		if ( empty( $this->cache[ $name ] ) ) {
			$this->cache[ $name ] = $this->get_closure( $name, $this->get_rule( $name ) );
		}

		return $this->cache[$name]( $args, $share );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 * @param array  $rule
	 *
	 * @return \Closure
	 * @throws \ReflectionException
	 */
	private function get_closure( $name, array $rule ) {
		$class       = new \ReflectionClass( isset( $rule[ self::INSTANCE_OF ] ) ? $rule[ self::INSTANCE_OF ] : $name );
		$constructor = $class->getConstructor();

		$params = $constructor ? $this->get_params( $constructor, $rule ) : null;

		if ( $class->isInterface() ) {
			$closure = function () {
				throw new \InvalidArgumentException( __( 'Cannot instantiate interface', 'starter-plugin' ) );
			};

		} else if ( $params ) {
			$closure = function ( array $args, array $share ) use ( $class, $params ) {

				return new $class->name( ...$params( $args, $share ) );
			};

		} else {
			$closure = function () use ( $class ) {
				return new $class->name;
			};
		}

		if ( ! empty( $rule[ self::SHARED ] ) ) {
			$closure = function ( array $args, array $share ) use ( $class, $name, $constructor, $params, $closure ) {

				if ( $class->isInternal() ) {
					$this->instances[ $name ] = $this->instances[ ltrim( $name, '\\' ) ] = $closure( $args, $share );

				} else {
					$this->instances[ $name ] = $this->instances[ ltrim( $name, '\\' ) ] = $class->newInstanceWithoutConstructor();

					if ( $constructor ) {
						$constructor->invokeArgs( $this->instances[ $name ], $params( $args, $share ) );
					}
				}

				return $this->instances[ $name ];
			};
		}

		if ( isset( $rule[ self::SHARE_INSTANCES ] ) ) {
			$closure = function ( array $args, array $share ) use ( $closure, $rule ) {
				foreach ( $rule[ self::SHARE_INSTANCES ] as $instance ) {
					$share[] = $this->create( $instance, [], $share );
				}

				return $closure( $args, $share );
			};
		}

		return isset( $rule[ self::CALL ] ) ? function ( array $args, array $share ) use ( $closure, $class, $rule, $name ) {
			$object = $closure( $args, $share );

			foreach ( $rule[ self::CALL ] as $call ) {
				$params = $this->get_params( $class->getMethod( $call[0] ), [
					self::SHARE_INSTANCES => isset( $rule[ self::SHARE_INSTANCES ] ) ? $rule[ self::SHARE_INSTANCES ] : [],
				] )( ( $this->expand( isset( $call[1] ) ? $call[1] : [] ) ), $share );
				$return = $object->{$call[0]}( ...$params );
				if ( isset( $call[2] ) ) {
					if ( $call[2] === self::CHAIN_CALL ) {
						if ( ! empty( $rule[ self::SHARED ] ) ) {
							$this->instances[ $name ] = $return;
						}

						if ( is_object( $return ) ) {
							$class = new \ReflectionClass( get_class( $return ) );
						}

						$object = $return;

					} else if ( is_callable( $call[2] ) ) {
						call_user_func( $call[2], $return );
					}
				}
			}

			return $object;
		} : $closure;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param       $param
	 * @param array $share
	 * @param bool  $createFromString
	 *
	 * @return array|mixed|string
	 * @throws \ReflectionException
	 */
	private function expand( $param, array $share = [], $createFromString = false ) {
		if ( is_array( $param ) ) {
			if ( isset( $param[ self::INSTANCE ] ) ) {
				$args = isset( $param[ self::PARAMS ] ) ? $this->expand( $param[ self::PARAMS ] ) : [];

				if ( is_array( $param[ self::INSTANCE ] ) ) {
					$param[ self::INSTANCE ][0] = $this->expand( $param[ self::INSTANCE ][0], $share, true );
				}

				if ( is_callable( $param[ self::INSTANCE ] ) ) {
					return call_user_func( $param[ self::INSTANCE ], ...$args );

				} else {
					return $this->create( $param[ self::INSTANCE ], array_merge( $args, $share ) );
				}

			} else if ( isset( $param[ self::GLOBALS ] ) ) {
				return $GLOBALS[ $param[ self::GLOBALS ] ];

			} else if ( isset( $param[ self::CONSTANT ] ) ) {
				return constant( $param[ self::CONSTANT ] );

			} else {
				foreach ( $param as $name => $value ) {
					$param[ $name ] = $this->expand( $value, $share );
				}
			}
		}

		return is_string( $param ) && $createFromString ? $this->create( $param ) : $param;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param \ReflectionParameter $param
	 * @param                      $class
	 * @param array                $search
	 *
	 * @return bool
	 */
	private function match_param( \ReflectionParameter $param, $class, array &$search ) {
		foreach ( $search as $i => $arg ) {
			if ( $class && ( $arg instanceof $class || ( $arg === null && $param->allowsNull() ) ) ) {

				return array_splice( $search, $i, 1 )[0];
			}
		}

		return false;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param \ReflectionMethod $method
	 * @param array             $rule
	 *
	 * @return \Closure
	 */
	private function get_params( \ReflectionMethod $method, array $rule ) {
		$paramInfo = [];

		foreach ( $method->getParameters() as $param ) {
			$class       = $param->getClass() ? $param->getClass()->name : null;
			$paramInfo[] = [
				$class,
				$param,
				isset( $rule[ self::SUBSTITUTIONS ] ) && array_key_exists( $class, $rule[ self::SUBSTITUTIONS ] ),
			];
		}

		return function ( array $args, array $share = [] ) use ( $paramInfo, $rule ) {
			if ( isset( $rule[ self::CONSTRUCT_PARAMS ] ) ) {
				$args = array_merge( $args, $this->expand( $rule[ self::CONSTRUCT_PARAMS ], $share ) );
			}

			$parameters = [];

			/**
			 * @var \ReflectionParameter $param
			 */
			foreach ( $paramInfo as list( $class, $param, $sub ) ) {
				if ( $args && ( $match = $this->match_param( $param, $class, $args ) ) !== false ) {
					$parameters[] = $match;

				} else if ( $share && ( $match = $this->match_param( $param, $class, $share ) ) !== false ) {
					$parameters[] = $match;

				} else if ( $class ) {
					try {
						$parameters[] = $sub ? $this->expand( $rule[ self::SUBSTITUTIONS ][ $class ], $share, true ) : $this->create( $class, [], $share );

					} catch ( \InvalidArgumentException $e ) {
					}

				} else if ( $args && ( ! $param->getType() || call_user_func( 'is_' . $param->getType()->__toString(), $args[0] ) ) ) {
					$parameters[] = $this->expand( array_shift( $args ) );

				} else if ( $param->isVariadic() ) {
					$parameters = array_merge( $parameters, $args );

				} else {
					$parameters[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
				}
			}

			return $parameters;
		};
	}
}
