<?php
/**
 * Starter Plugin.
 *
 * This is the plugin's bootstrap file. It is responsible for providing the plugin
 * meta information that WordPress needs, preparing the environment so that it's
 * ready to execute our code and kick off our composition root (Plugin class).
 *
 * Plugin Name: Starter Plugin
 * Plugin URI:  https://wordpress.org/plugins/starter-plugin/
 * Description: Simple starter plugin for WordPress.
 * Version:     1.0.0
 * Author:      SEO Themes
 * Author URI:  https://seothemes.com/
 * Text Domain: starter-plugin
 * License:     GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /assets/lang
 *
 * @package   SeoThemes\StarterPlugin
 * @author    SEO Themes <info@seothemes.com>
 * @license   GPL-2.0-or-later
 * @link      https://seothemes.com/
 * @copyright 2019 SEO Themes
 */

namespace SeoThemes\StarterPlugin;

/*
|--------------------------------------------------------------------------
| Bootstrap Plugin
|--------------------------------------------------------------------------
|
| Wrapping all of the plugins bootstrapping code inside of an immediately
| invoked function expression (IIFE) allows us to initialize the plugin
| classes without needing to create variables in the global namespace.
|
*/

\call_user_func( function () {

	/*
	|--------------------------------------------------------------------------
	| Prevent Direct File Access
	|--------------------------------------------------------------------------
	|
	| As this is the only PHP file having side effects, we need to provide a
	| safeguard, so we want to make sure this file is only run from within
	| WordPress and cannot be directly called through a public request.
	|
	*/

	\defined( 'ABSPATH' ) || die;

	/*
	|--------------------------------------------------------------------------
	| Autoload Classes
	|--------------------------------------------------------------------------
	|
	| Register our own PSR-4 autoloader so that we don't need to use Composer.
	| The autoloader automatically locates all of our classes and includes
	| the correct file if one exists in the Plugins `/src/` directory.
	|
	*/

	\spl_autoload_register( function ( $class ) {
		if ( \strpos( $class, __NAMESPACE__ ) !== false ) {
			require_once __DIR__ . '/src' . \str_replace( '\\', DIRECTORY_SEPARATOR, \substr( $class, \strlen( __NAMESPACE__ ) ) ) . '.php';
		}
	} );

	/*
	|--------------------------------------------------------------------------
	| Check PHP Version Requirements
	|--------------------------------------------------------------------------
	|
	| Checks that the server meets the minimum PHP version required for the
	| plugin. The version number is defined as a class constant in Plugin.
	| The PHP version can be changed depending on your app requirements.
	|
	*/

	if ( version_compare( PHP_VERSION, Plugin::MIN_PHP_VERSION, '<' ) ) {
		return;
	}

	/*
	|--------------------------------------------------------------------------
	| Configure Dependency Injection Container
	|--------------------------------------------------------------------------
	|
	| Creates the Dependency Injection Container instance and passes in config
	| rules. We could use a factory here to create the Container if we need
	| to allow access to the one true instance in plugin addons or themes.
	|
	*/

	$container = ( new Container() )->add_rules( [
		Plugin::class   => [ Container::CONSTRUCT_PARAMS => [ __FILE__ ] ],
		Provider::class => [ Container::CALL => [ [ Provider::SETUP, [ Plugin::class ] ] ] ],
	] );

	/*
	|--------------------------------------------------------------------------
	| Register Activation and Deactivation Hooks
	|--------------------------------------------------------------------------
	|
	| Registers activation and deactivation hooks using closures, as these
	| need static access to work correctly. This enables the use of non
	| static methods for the callbacks which is not usually possible.
	|
	*/

	\register_activation_hook( __FILE__, function () use ( $container ) {
		$container->create( Plugin::class )->activate();
	} );

	\register_deactivation_hook( __FILE__, function () use ( $container ) {
		$container->create( Plugin::class )->deactivate();
	} );

	/*
	|--------------------------------------------------------------------------
	| Register Service Providers
	|--------------------------------------------------------------------------
	|
	| Finally, create and register plugin Service Providers with the WordPress
	| lifecycle. Because they're created by the Container, all dependencies
	| are autowired and injected. We then register the hooks for each one.
	|
	*/

	$container->create( Providers\I18n::class );
	$container->create( Providers\Admin::class );
	$container->create( Providers\Frontend::class );

} );

