<?php
/**
 * Adds the Caldera Easy Pods widget
 *
 * @package   Caldera_Easy_Pods
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Pollock
 */

class Caldera_Easy_Pods_Widget extends WP_Widget {

	/**
	 * Create object
	 *
	 * @since 0.0.1
	 */
	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, __('Caldera Easy Pods', 'caldera-easy-pods' ) );
	}

	/**
	 * Render the  widget
	 *
	 * @since 0.0.1
	 *
	 * @param array $args
	 * @param Caldera_Easy_Pods_Widget $instance
	 */
	function widget( $args, $instance ) {

		if( ! empty( $instance['easy_pod'] ) ){

			extract($args, EXTR_SKIP);

			$out[] = $before_widget;
			$title = empty( $instance[ 'title' ] ) ? ' ' : apply_filters( 'widget_title', $instance[ 'title' ] );
			if ( ! empty( $title ) ) {
				$out[] = $before_title . $title . $after_title;
			};

			$params = null;

			if ( isset( $instance[ 'limit' ] ) && 0 < intval( $instance[ 'limit' ] )  ) {
				$params[ 'limit' ] = $instance[ 'limit' ];
			}

			/**
			 * Override saved params passed to cep_render_easy_pod() when outputting in widget
			 *
			 * @since 0.0.1
			 *
			 * @param null|array $param Params for overriding saved params.
			 * @param Caldera_Easy_Pods_Widget|object Current widget instance
			 */
			$params = apply_filters( 'caldera_easy_pods_widget_params', $params, $instance  );


			$out[] = cep_render_easy_pod( $instance['easy_pod' ], $params  );

			$out[] = $after_widget;

			echo implode( '', $out );


		}
	}

	/**
	 * Update widget settings
	 *
	 * @since 0.0.1
	 *
	 * @param Caldera_Easy_Pods_Widget $new_instance
	 * @param Caldera_Easy_Pods_Widget $old_instance
	 *
	 * @return Caldera_Easy_Pods_Widget
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;

	}

	/**
	 * Render form
	 *
	 * @since 0.1.0
	 *
	 * @param Caldera_Easy_Pods_Widget $instance Class instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
				'title' => '',
				'limit' => -1,
				'easy_pod' => ''
			)
		);
		$title = strip_tags($instance['title']);

		echo "<p><label for=\" " . $this->get_field_id( 'title' ) . "\">" . __( 'Title', 'easy-pods' ) . ": <input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr($title). "\" /></label></p>\r\n";

		// get the Easy Pods
		$easy_pods = cep_get_registry();

		echo "<p><label for=\" " . $this->get_field_id( 'title' ) . "\">" . __( 'Easy Pod', 'easy-pods' ) . ": </label><select style=\"width:100%;\" name=\"" . $this->get_field_name( 'easy_pod' ) . "\">\r\n";
		if( ! empty( $easy_pods ) ){
			foreach( $easy_pods as $easy_pod ){
				$sel = "";
				if( ! empty( $instance[ 'easy_pod' ] ) ){
					if( $instance[ 'easy_pod' ] == $easy_pod[ 'slug' ] ){
						$sel = ' selected="selected"';
					}

				}
				echo "<option value=\"" . $easy_pod[ 'slug' ] . "\"".$sel.">" . $easy_pod[ 'name' ] ."</option>\r\n";

			}

		}

		echo "</select></p>\r\n";

		$limit_description = __( 'Override the number of items to return. Leave empty to used saved value.' );

		echo "<p><label for=\" " . $this->get_field_id( 'limit' ) . "\">" . __( 'Limit', 'easy-pods') . ": <input class=\"widefat\" id=\"" . $this->get_field_id( 'limit' ) . "\" name=\"" . $this->get_field_name( 'limit' ) . "\" type=\"text\" value=\"" . esc_attr( $instance[ 'limit' ] ). "\" /></label><p>" . $limit_description . "</p></p>\r\n";


	}

}


