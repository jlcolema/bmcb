<?php
/**
 * Caldera Easy Pods - Utility Functions
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */


/**
 * Return a rendered pod query
 *
 * @since 0.0.1
 * @param string	$slug			Pod Query Slug
 * @param array		$params			pod $params overide for pod query
 * @param array		$template		pod template name, template code or template file location.
 *
 * @return string	rendered HTML putput.
 */
function cep_render_easy_pod( $slug, $params = null, $template = null ){

	$query = cep_find_easy_pod_by_slug( $slug );

	if( ! is_array( $query ) || empty( $query ) ){
		return;

	}

	if ( ! empty( $query['search_form' ] ) && class_exists( 'Caldera_Forms' ) ) {
		$form = cep_get_form( $query['search_form'] );
		if ( is_array( $form ) ) {
			global $easy_pods_form_to_easy_pod;
			if( ! is_array( $easy_pods_form_to_easy_pod ) ) {
				$easy_pods_form_to_easy_pod = array();
			}

			$easy_pods_form_to_easy_pod[ $form[ 'ID' ] ] = $query[ 'id' ];

			return Caldera_Forms::render_form( $form );
		}
	}

	$pod = pods( $query['pod'] );
	
	if( !empty( $template ) ){
		$template_obj = $pod->api->load_template( array( 'name' => $template ) );
		if( false != $template_obj ){
			$template = null;
			$query['params']['template'] = $template_obj['name'];
		}
	}

	$easy_pod = Caldera_Easy_Pods::get_instance();

	$pod_params = $easy_pod->apply_query( array(), $pod, array('query' => $query ) );

	if( !empty( $params ) ){
		$pod_params = array_merge( $pod_params, (array) $params );
	}

	$pod->find( $pod_params );

	/**
	 * Modify Pods object directly before rendering output
	 *
	 * @since 1.1.4
	 *
	 * @param Pods $pod Pods object
	 * @param array $pod_params Params used to create Pods object
	 * @param string $query Easy Pod slug
	 */
	$pod = apply_filters( 'caldera_easy_pods_pod_before_render', $pod, $pod_params, $slug );
	if( !empty( $template ) ){
		if( file_exists( $template ) ){
			ob_start();
			include $template;
			$template = ob_get_clean();
		}
		$output = $pod->template( null, $template );
	}else{

		//don't look at this, it's for the future...
		if( $query['params']['template'] == '_layout_' ){
			$output = '<div class="caldera-easy-grid">';
			foreach( $query['layout_json'] as $row ){
				$output .= '<div class="row">';

					foreach( $row as $column ){
						$output .= '<div class="col-md-' . $column['size'] . '">';
							foreach( $column['item'] as $item ){
								$code = $item['code'];
								preg_match_all("/\{\{#each(.+?)\}\}/", $code, $eaches);
								if( !empty( $eaches[1] ) ){
									foreach( $eaches[1] as $each_key=>$each_in ){
										$code = str_replace($eaches[0][$each_key], '[each' . $each_in . ']', $code);
									}									
								}
								preg_match_all("/\{\{#if(.+?)\}\}/", $code, $eaches);
								if( !empty( $eaches[1] ) ){
									foreach( $eaches[1] as $each_key=>$each_in ){
										$code = str_replace($eaches[0][$each_key], '[if' . $each_in . ']', $code);
									}									
								}

								$code = str_replace('{{/if}}', '[/if]', $code);
								$code = str_replace('{{/each}}', '[/each]', $code);
								$code = str_replace('{{', '{@', $code);
								$code = str_replace('}}', '}', $code);


								$output .= $code;
							}
						$output .= '</div>';

					}

				$output .= '</div>';

			}
			$output .= '</div>';

			$output = $pod->template( null, $output );
		}else{
			$output = $pod->template( $query['params']['template'] );
		}
	}

	if( empty( $output ) ){
		$magic = new calderawp\filter\two\magictag();
		$output = $magic->do_magic_tag( trim( $query['no_results'] ) );
	}
	if( !empty( $query['params']['pagination'] ) ){
		$output .= '<span class="search-pagination" id="' . $query['id'] . '-pagination">';
		$output .= $pod->pagination( array( 'type' => $query['params']['pagination'] ) );
		$output .= '</span>';
		ob_start();
		?>
		<script id="<?php echo $query['id']; ?>" type="text/javascript">
			+(function($){

				$(document).on('click', '#<?php echo $query['id']; ?>-pagination a', function(e){
					e.preventDefault();
					if( $(this).attr('href') ){
						var query = {};
						var a = $(this).attr('href').split('?')[1].split('&');
						for (var i in a){
							var b = a[i].split('=');
							query[decodeURIComponent(b[0])] = decodeURIComponent(b[1]);
						}

						if( query.pg ){
							var form_parent = $(this).closest('.search-pagination'),
								form = $('#' + form_parent.data('form'));

							form.data('pg', query.pg).trigger('submit');

						}
					}
				});

			})(jQuery);
		</script>	
		<?php
		$output .= ob_get_clean();
	}
	// add in wrapper
	$output = '<div id="' . $query['id'] . '_wrapper"><div id="' . $query['id'] . '_wrapper_inner">' . $output . '</div></div>';

	if( !empty( $query['params']['pagination'] ) ){

		$loading_class = 'loading';
		if( !empty( $query['paginate_load_class'] ) ){
			$loading_class = str_replace( '.', '', $query['paginate_load_class'] );
		}

		ob_start();
		?>
		<script id="<?php echo $query['id']; ?>" type="text/javascript">
			+(function($){

				$(document).on('click', '#<?php echo $query['id']; ?>-pagination a', function(e){
					e.preventDefault();
					if( $(this).attr('href') ){
						$('#<?php echo $query['id'] . '_wrapper'; ?>').addClass('<?php echo $loading_class; ?>').load( this.href + ' #<?php echo $query['id'] . '_wrapper_inner'; ?>', function(res){
							$( this ).removeClass('loading');
						} );
					}
				});

			})(jQuery);
		</script>	
		<?php
		$output .= ob_get_clean();
	}

	/**
	 * Filter the rendered HTML for the query, after parsing.
	 *
	 * @since 0.0.1
	 *
	 * @param string $html Render the HTML
	 * @param Pods $pod Pod object used to render. @since 1.1.4
	 * @param array $pod_params Params used to make Pod, used to render. @since 1.1.4
	 */
	return apply_filters( 'caldera_easy_pods_render_query_html', $output, $pod, $pod_params );

}

/**
 * Get the Easy Pods registry--the list of all registered Easy Pods.
 *
 * @since 0.1.0
 *
 * @return array|bool
 */
function cep_get_registry() {
	$easy_pods = cep_get_easy_pod( '_easy_pods_registry', array() );

	/**
	 * Filter registry--the list of all registered easy Pods before returning
	 *
	 * @since 1.2.0
	 *
	 * @param array $easy_pods The registry
	 */
	$easy_pods = apply_filters( 'easy_pods_get_registry', $easy_pods );

	return $easy_pods;

}

/**
 * Update the Easy Pods registry
 *
 * @since 0.1.0
 *
 * Note: <em>Does not</em> add an item to the registry, replaces whole registry.
 *
 * @param array $easy_pods New value for Easy Pods Registry
 *
 * @return bool Returns true if updated, false if not updated or not allowed.
 */
function cep_update_registry( $easy_pods ) {
	$can = Caldera_Easy_Pods_Options::can();
	if ( $can ) {
		return update_option( '_easy_pods_registry', $easy_pods );
	}else{
		return $can;

	}

}

/**
 * Get a single Easy Pods' settings.
 *
 * @since 0.1.0
 *
 * @param string $easy_pod_id ID of Easy Pod
 *
 * @return bool|array The Easy Pod config or false if not found
 */
function cep_get_easy_pod( $easy_pod_id ) {
	$easy_pod = get_option( $easy_pod_id, false );

	/**
	 * Filter a single Easy Pod's settings before returning.
	 *
	 * @since 1.2.0
	 *
	 * @param array|bool $easy_pod The Easy Pods' setting or false if non Easy Pod with ID $easy_pod_id exists in registry.
	 * @param string $easy_pod_id The ID of the Easy Pod
	 */
	$easy_pod = apply_filters( 'easy_pods_get_easy_pod', $easy_pod, $easy_pod_id );

	return $easy_pod;


}

/**
 * Update the settings for a single Easy Pod
 *
 * @since 0.1.0
 *
 * @param string $easy_pod_id ID of Easy Pod
 * @param array $easy_pod The new settings array.
 *
 * @return bool Returns true if updated, false if not saved or not allowed.
 */
function cep_update_easy_pod( $easy_pod_id, $easy_pod ) {
	$can = Caldera_Easy_Pods_Options::can();
	if ( $can ) {
		return update_option( $easy_pod_id, $easy_pod );

	}else{
		return $can;

	}

}

/**
 * Function to run before returning an Easy Pod config in the editor.
 *
 * As of 0.2.6, just removes unsupported fields from taxonomies. Will probably need to evolve into a class if we have a 2nd need for this.
 *
 * @since 0.2.6
 *
 * @param array $easy_pod Easy Pod config.
 * @param array $_pods Array of all Pods.
 *
 * @return array The Easy Pod Config.
 */
function cep_pre_editor( $easy_pod, $_pods ) {
	$names = wp_list_pluck( $_pods, 'name' );
	if ( ! empty ( $names ) ) {
		$this_pod = array_search( pods_v( 'pod', $easy_pod ), $names );
		if ( intval( $this_pod ) > 0 ) {
			$this_pod = pods_v( $this_pod, $_pods );
			if ( ! is_null( $this_pod ) ) {
				$type     = pods_v( 'type', $this_pod );
				if ( 'taxonomy' == $type ) {
					if ( isset( $easy_pod[ 'pod_fields' ] ) && isset( $easy_pod[ 'pod_fields' ][ 'fields' ] ) && ! empty( $easy_pod[ 'pod_fields' ][ 'fields' ] ) ) {
						foreach( $easy_pod[ 'pod_fields' ][ 'fields' ] as $i => $name ) {
							if ( ! in_array( $name, array( 'slug', 'term_id', 'name', 'term_group', 'parent' ) ) ) {
								unset( $easy_pod[ 'pod_fields' ][ 'fields' ][ $i ] );

							}

						}

					}

				}

			}

		}

	}

	return $easy_pod;

}

/**
 * Find Pod type form Pods object or Easy Pod ID
 *
 * @param object|\Pods|string $pod
 *
 * @return string|void
 */
function cep_pod_type( $pod ) {
	if ( ! is_a( $pod, 'Pods' ) ) {
		$easy_pod = false;
		if ( is_string( $pod ) ) {
			$easy_pod = cep_get_easy_pod( $pod );

		}

		if ( $easy_pod && is_array( $easy_pod ) && isset( $easy_pod[ 'pod' ] ) ) {
			$pod = pods( $easy_pod[ 'pod' ], null, true );

		}

		return cep_pod_type( $pod );


	}else {
		$type = $pod->pod_data[ 'object_type' ];
		return $type;

	}

}

/**
 * Find an Easy Pod by slug, and return its config or ID.
 *
 * @since 1.1.0
 *
 * @param string $slug Easy Pods
 * @param bool $return_config. Optional. If true, the default, the config for the matching Easy Pod is returned. If false, its ID is returned.
 *
 * @return array|string|void If found config or ID is returned.
 */
function cep_find_easy_pod_by_slug( $slug, $return_config = true ) {
	$easy_pods = cep_get_registry();
	foreach( $easy_pods as $id => $easy_pod ){
		if( $easy_pod['slug'] === $slug ){
			if ( $return_config ) {
				return cep_get_easy_pod( $id );
			}else{
				return $id;
			}

		}

	}

}

/**
 * Find out if an Easy Pod exists by Slug or ID
 *
 * @since 1.1.0
 *
 * @param string $slug_or_id ID or slug of Easy Pod
 *
 * @return bool
 */
function cep_easy_pod_exists( $slug_or_id ) {
	if ( is_array( cep_find_easy_pod_by_slug( $slug_or_id ) ) ) {
		return true;

	}

	if ( array_key_exists( $slug_or_id, cep_get_registry() ) && is_array( cep_get_easy_pod( $slug_or_id ) ) ) {
		return true;

	}

}

/**
 * Get Caldera Forms
 *
 * Includes backwards compat for pre-Caldera Forms 1.3.4
 *
 * @since 1.1.7
 *
 * @return array|void
 */
function cep_get_forms(){
	if ( class_exists( 'Caldera_Forms_Forms' )  ) {
		$forms = Caldera_Forms_Forms::get_forms( true );
	} else {
		$forms = Caldera_Forms::get_forms();
	}

	return $forms;
}

/**
 * Get Caldera Forms
 *
 * Includes backwards compat for pre-Caldera Forms 1.3.4
 *
 * @since 1.1.7
 *
 * @param string $id_name ID or name of form
 *
 * @return array|void
 */
function cep_get_form( $id_name ){
	if ( class_exists( 'Caldera_Forms_Forms' )  ) {
		$form = Caldera_Forms_Forms::get_form( $id_name );
	} else {
		$form = Caldera_Forms::get_form( $id_name );
	}

	if( isset( $form[ 'ID' ] ) && ! isset( $form[ 'id' ] ) ){
		$form[ 'id' ] = $form[ 'ID' ];
	}

	return $form;

}

/**
 * Allow searches where the WHERE has quotes in it -- beacuse non-english languages
 *
 * @since 1.0.8
 */
add_filter( 'caldera_easy_pods_query_params', function( $params ){
	if( isset( $params[ 'where' ] ) && ! empty( $params[ 'where' ] ) ) {
		foreach ( $params[ 'where' ] as $i => $where ) {
			if ( ! is_numeric( $i ) ) {
				continue;
			}
			foreach ( $where as $wi => $where_part ) {
				if ( ! is_numeric( $wi ) ) {
					continue;
				}
				$params[ 'where' ][ $i ][ $wi] [ 'value' ] = wp_unslash( $params[ 'where' ][ $i ][ $wi] [ 'value' ] );
			}
		}
	}



	return $params;

});