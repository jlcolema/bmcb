<?php
/**
 * Caldera Easy Pods Options.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

/**
 * Plugin class.
 * @package Caldera_Easy_Pods
 * @author  David Cramer <david@digilab.co.za>
 */
class Caldera_Easy_Pods_Options {

	/**
	 * Get an option from this plugin.
	 *
	 * @param string $option The name of a specific option to get.
	 * @param mixed $default Optional. Default to return if no value found. Default is false.
	 *
	 * @return string|null|array|bool Returns the option or null if it doesn't exist or false if not allowed.
	 */
	public static function get ( $option, $default = false ) {
		$can = self::can();
		if ( $can ) {
			$option = self::get_options( $option );
			if ( is_array( $option ) && empty( $option ) ) {
				return null;

			}

			if ( is_null( $option ) ) {
				return $default;

			}

			return $option;
		}else{
			return $can;

		}

	}

	/**
	 * Get all option from this plugin.
	 *
	 * @return null|array|false Returns the options or null if none are set or false if not allowed.
	 */
	public static function get_all (  ) {
		$can = self::can();
		if ( $can ) {
			return self::get_options( null );
		}else{
			return $can;

		}

	}

	/**
	 * Get an option or all option from this plugin
	 *
	 * @access private
	 *
	 * @param null|string $option Optional. If null, the default, all options for this plugin are returned. Provide the name of a specific option to get just that one.
	 *
	 * @return array|null|string
	 */
	private static function get_options( $option = null ) {
		$options = get_option( "_easy_pods", array() );
		if ( empty( $options ) ) {
			return $options;

		}

		if ( ! is_null( $option ) ) {
			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}
			else {
				return null;

			}

		}

		return $options;

	}

	/**
	 * Generic capability check to use before reading/writing
	 *
	 * @since 1.1.4
	 *
	 * @param string $cap Optional. Capability to check. Defaults to 'manage_options'
	 *
	 * @return bool
	 */
	public static function can( $cap = 'manage_options' ) {
		return current_user_can( $cap );

	}

}
