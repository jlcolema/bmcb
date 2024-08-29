<?php
/**
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 David Cramer <david@digilab.co.za>
 *
 * @wordpress-plugin
 * Plugin Name: Caldera Easy Pods
 * Plugin URI:  
 * Description: Visually Create Queries for Pods Data
 * Version: 1.1.10
 * Author:      David Cramer <david@digilab.co.za>
 * Author URI:  http://calderawp.com
 * Text Domain: easy-pods
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CEP_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CEP_URL',  plugin_dir_url( __FILE__ ) );
define( 'CEP_VER', '1.1.10' );


/**
 * Load the plugin if PHP version is sufficient.
 */
add_action( 'plugins_loaded', 'cep_load_or_die', 0 );
function cep_load_or_die() {
	if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		include_once CEP_PATH . 'vendor/calderawp/dismissible-notice/src/functions.php';
	}

	if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		if ( is_admin() ) {
			$message = __( sprintf( 'Caldera Easy Pods requires PHP version %1s or later. We strongly recommend PHP 5.4 or later for security and performance reasons. Current version is %2s.', '5.3.0', PHP_VERSION ), 'caldera-easy-pods' );

			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );

		}

	} else {
		//update this to 2.5.4, once that is a thing?
		if ( ! defined( 'PODS_VERSION' ) || ! version_compare( PODS_VERSION, '2.5.4-a-1', '>=' ) ) {
			include_once CEP_PATH . 'vendor/calderawp/dismissible-notice/src/functions.php';
			$message = __( 'Caldera Easy Pods Requires Pods 2.5.4 or later.', 'caldera-easy-pods' );
			echo caldera_warnings_dismissible_notice( $message, false );

		}

		include_once( CEP_PATH .'vendor/autoload.php' );
		include_once( CEP_PATH . 'bootstrap.php' );

	}

}


