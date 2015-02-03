<?php
/*
 * Plugin Name: Our People
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: our-people
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-our-people.php' );
require_once( 'includes/class-our-people-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-our-people-admin-api.php' );
require_once( 'includes/lib/class-our-people-post-type.php' );
require_once( 'includes/lib/class-our-people-taxonomy.php' );

/**
 * Returns the main instance of Our_People to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Our_People
 */
function Our_People () {
	$instance = Our_People::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Our_People_Settings::instance( $instance );
	}

	return $instance;
}

Our_People();
