<?php
/**
 * Plugin Name: Starter Plugin
 * Plugin URI:  https://wordpress.org/plugins/starter-plugin/
 * Description: Simple starter plugin for WordPress.
 * Version:     1.0.0
 * Author:      SEO Themes
 * Author URI:  https://seothemes.com/
 * Text Domain: starter-plugin
 * License:     GPL-3.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /assets/lang
 */

namespace SeoThemes\StarterPlugin;

// Prevent direct file access.
defined( 'ABSPATH' ) || exit;

// Load Composer packages.
require_once __DIR__ . '/vendor/autoload.php';

// Instantiate plugin.
$plugin = ( new Factory() )->create( new Config( __FILE__ ) );

// Register activation hook.
\register_activation_hook( __FILE__, function () use ( $plugin ) {
	$plugin->activate();
} );

// Register deactivation hook.
\register_deactivation_hook( __FILE__, function () use ( $plugin ) {
	$plugin->deactivate();
} );

// Run plugin.
$plugin->register();
