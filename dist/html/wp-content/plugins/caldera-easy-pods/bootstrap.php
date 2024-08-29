<?php
/**
 * Bootstraps The Plugin
 *
 * @package   Caldera_Easy_Pods
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Pollock
 */

/**
 * Load plugin internals
 */
add_action( 'plugins_loaded', function() {

	//check if Pods is active, if not bail.
	if ( ! defined('PODS_VERSION' ) ) {
		return;

	}

	// load files
	require_once( CEP_PATH . 'classes/easy-pods.php' );
	require_once( CEP_PATH . 'classes/options.php' );
	require_once( CEP_PATH . 'classes/widget.php' );
	include_once( CEP_PATH . 'vendor/autoload.php' );


	// load admin classes
	if( is_admin() ){
		require_once( CEP_PATH . 'classes/pod-fields.php' );
		require_once( CEP_PATH . 'includes/settings.php' );
	}


	// create main class object instance
	Caldera_Easy_Pods::get_instance();

	// include utility functions.
	require_once( CEP_PATH . 'includes/functions.php' );

	// pull in CF support in admin
	if( ! is_admin() || ( defined( 'DOING_AJAX' ) && isset( $_POST['cfajax'] ) ) ){
		if( defined( 'CFCORE_VER') ){
			require_once( CEP_PATH . 'classes/caldera-forms.php' );
			$cf_forms = new Caldera_Easy_Pods_CF();
		}

	}

	//add widget
	add_action( 'widgets_init', function() {
		require_once( CEP_PATH . 'classes/widget.php' );
		register_widget( 'Caldera_Easy_Pods_Widget' );
	} );

} );

/**
 * Software Licensing
 */
add_action( 'admin_init', function( ) {

	$plugin = array(
		'name' =>  'Caldera Easy Pods',
		'slug'		=>	'caldera-easy-pods',
		'url'		=>	'https://calderawp.com/downloads/caldera-easy-pods',
		'version'	=>	CEP_VER,
		'key_store'	=>  'ceP-license',
		'file'		=>  CEP_PATH . 'plugincore.php'
	);

	new \calderawp\licensing_helper\licensing( $plugin );

}, 0 );
