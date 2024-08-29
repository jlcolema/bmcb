<?php
/**
 * Caldera Easy Pods - Caldera Forms Search Integration
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

//do not run if Caldera Forms is not active.
if ( ! class_exists( 'Caldera_Forms') ) {
	return;

}

/**
 * Plugin class.
 * @package Caldera_Easy_Pods_CF
 * @author  David Cramer <david@digilab.co.za>
 */
class Caldera_Easy_Pods_CF extends Caldera_Easy_Pods {

	public function __construct() {

		// override ajax
		add_filter( 'caldera_forms_render_form_attributes', array( $this, 'setup_form_atts' ), 11, 2);

		// hook into a submission
		add_action( 'caldera_forms_submit_start', array( $this, 'run_search_form'), 1 );

		// hook form render
		add_filter( 'caldera_forms_render_form', array( $this, 'render_search_form'), 10, 2 );

		// add magic tag inits
		add_action( 'caldera_easy_pods_start_query_params', array( $this, 'init_magic_tag_filter') );
		
		// bind auto population
		remove_filter('caldera_forms_submit_start', array( Caldera_Forms::get_instance(), 'auto_populate_options_field' ), 10, 2);
		add_filter('caldera_forms_render_get_field_type-radio', array( $this, 'auto_populate_options_field' ), 11, 2);
		add_filter('caldera_forms_render_get_field_type-checkbox', array( $this, 'auto_populate_options_field' ), 11, 2);
		add_filter('caldera_forms_render_get_field_type-dropdown', array( $this, 'auto_populate_options_field' ), 11, 2);
		add_filter('caldera_forms_render_get_field_type-toggle_switch', array( $this, 'auto_populate_options_field' ), 11, 2);

	}

	/**
	 * Holds the Pods instance used by $this->auto_populate_options_field() so it can be accessed inside the filter CB it contains.
	 *
	 * @since 0.2.6
	 *
	 * @access private
	 *
	 * @var object|\Pods
	 */
	private $cf_auto_populate_pod;


	/**
	 * Auto populate search fields
	 *
	 * @since 0.0.1
	 *
	 * @return array The field options
	 */
	public function auto_populate_options_field( $field, $form ) {

		$easy_pod_id = $this->is_form_search_bound( $form );
		if( empty( $easy_pod_id ) ){
			return $field;

		}

		$easy_pod = cep_get_easy_pod( $easy_pod_id );
		if ( ! empty( $field[ 'config' ][ 'auto' ] ) ) {
			$cf = Caldera_Forms::get_instance();
			$field = $cf->auto_populate_options_field( $field, $form );

			return $field;
		}elseif( empty( $easy_pod ) || empty( $easy_pod['auto_populate_enable'][$field['slug']] ) ){
				return $field;

		} else{

			// populate fields
			$easy_pod_query = Caldera_Easy_Pods::get_instance();

			$easy_pod_query_obj = $easy_pod_query->get_easy_pod( $easy_pod[ 'auto_populate_fields' ][ $field[ 'slug' ] ] );

			$pod = pods( $easy_pod_query_obj[ 'pod' ] );
			$pod->find( $easy_pod_query->apply_query( array(), $pod, array( 'query' => $easy_pod_query_obj ) ) );

			//correct fields passed to Pods::export() for taxonomy Pods.
			if ( 'taxonomy' == $pod->pod_data[ 'object_type' ] ) {
				$this->cf_auto_populate_pod = $pod;
				add_filter( 'pods_pods_export', function ( $params ) {
					$pod           = $this->cf_auto_populate_pod;
					$fields        = $pod->fields;
					$object_fields = (array) pods_v( 'object_fields', $pod->pod_data, array(), null, true );
					if ( isset( $object_fields[ 'taxonomy' ] ) ) {
						unset( $object_fields[ 'taxonomy' ] );
					}
					$fields             = array_merge( $fields, $object_fields );
					$params[ 'fields' ] = $fields;

					return $params;

				} );

			}

			$field[ 'config' ][ 'option' ] = array();
			while ( $pod->fetch() ) {

				$pod_data = $pod->export();

				$field[ 'config' ][ 'option' ][ $pod_data[ 'id' ] ] = array(
					'value' => $pod_data[ $easy_pod[ 'auto_populate_values' ][ $field[ 'slug' ] ] ],
					'label' => $pod_data[ $easy_pod[ 'auto_populate_labels' ][ $field[ 'slug' ] ] ],
				);

			}

			return $field;
		}

	}

	/**
	 * Checks if a form is bound to an easy Pod
	 *
	 * @since 0.1.0
	 *
	 * @param array|string The form's name or settings array
	 *
	 * @return bool|int False if not found or the form's ID if found
	 */
	private function is_form_search_bound( $form ) {
		
		if( is_string( $form ) ){
			$form_id = $form;
		}else{
			if(!empty( $form['ID'] ) ){
				$form_id = $form['ID'];
			}
		}
		if( empty( $form_id ) ){
			return false;

		}

		//ensure we load the right Easy Pod from the form.
		global $easy_pods_form_to_easy_pod;
		if ( is_array( $easy_pods_form_to_easy_pod ) && isset( $easy_pods_form_to_easy_pod[ $form_id ] ) ) {
			$easy_pod_id = $easy_pods_form_to_easy_pod[ $form_id ];
			return $easy_pod_id;
		}

		if ( isset( $_POST[ 'podquery' ] ) ) {
			if ( cep_easy_pod_exists( strip_tags( $_POST[ 'podquery' ] ) ) ) {
				return $_POST[ 'podquery' ];
			}

		}

		$easy_pods = cep_get_registry();
		
		if( empty( $easy_pods ) ){
			return false;

		}

		foreach( $easy_pods as $easy_pod ){
			if( empty( $easy_pod['search_form'] ) || $easy_pod['search_form'] != $form_id ){
				continue;
			}
			return $easy_pod['id'];

		}

		return false;

	}

	/**
	 * Adds magic tag filter
	 *
	 * @since 0.1.0
	 */
	public function init_magic_tag_filter() {
		add_filter( 'caldera_magic_tag-search', array( $this, 'render_form_tags') );		
	}

	/**
	 * Render a form tag
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function render_form_tags( $tag ) {
		global $form;
		$field = Caldera_Forms::get_field_by_slug( $tag, $form );
		if( isset( $_POST[ $field[ 'ID'  ] ] ) ){
			return Caldera_Forms_Sanitize::sanitize( $_POST[ $field[ 'ID' ] ] );
		}

		$out_tag = (string) trim( Caldera_Forms::do_magic_tags( '%' . $tag . '%' , null, $field ) );
		if( $out_tag == '%' . $tag . '%' ){
			return null;

		}

		return $out_tag;

	}

	/**
	 * Adds scripts and target to form render
	 *
	 * @since 0.0.1
	 *
	 * @param string $html Current markup
	 * @param array|int $form Form ID or settings array
	 *
	 * @return string
	 */
	public function render_search_form( $html, $form ) {

		$easy_pod_id = $this->is_form_search_bound( $form );

		if( empty( $easy_pod_id ) ){
			return $html;
		}

		$easy_pod = cep_get_easy_pod( $easy_pod_id );
		
			ob_start();
		?>
		<script type="text/javascript">
			var my_cf_handler_<?php echo $easy_pod_id; ?>, my_cf_before_handler_<?php echo $easy_pod_id; ?>;

			jQuery(function($){
				my_cf_before_handler_<?php echo $easy_pod_id; ?> = function( el ){
					$( el ).data( '_parent', el.id );
				}

				my_cf_handler_<?php echo $easy_pod_id; ?> = function(obj){
					var buttons = 	obj.params.trigger.find(':submit');
					buttons.prop('disabled', false);
					// clear pagination
					obj.params.trigger.data('pg', false);					
					if( typeof window[obj.params.trigger.data('target')] === 'function' ){
						window[obj.params.trigger.data('target')]( obj.data, obj );
					}
				}

				$(document).on('click', '#search_results_<?php echo $easy_pod_id; ?> .pods-pagination-number, #search_results_<?php echo $easy_pod_id; ?> .page-numbers', function(e){
					e.preventDefault();
					if( $(this).attr('href') ){
						var query = {};
						var a = $(this).attr('href').split('?')[1].split('&');
						for (var i in a){
							var b = a[i].split('=');
							query[decodeURIComponent(b[0])] = decodeURIComponent(b[1]);
						}

						if( query.pg ){
							var	form = $( '<?php printf( '.%s', $form[ 'ID' ] ); ?>' );
							form.data('pg', query.pg ).trigger('submit');


						}
					}
				});

				<?php if( !empty( $easy_pod['trigger_change'] ) ){ ?>
				var search_form 	= $('.<?php echo $easy_pod['search_form']; ?>'),
					init_search;
				$(document).on('keyup change', '.<?php echo $easy_pod['search_form']; ?> [data-field]', function(e){
					if( search_form.hasClass('_tisBound') ){
						if( init_search ){
							clearTimeout( init_search );
						}
						if( this.required ){
							if( !this.value.trim().length ){
								
								return;
							}
						}						
						init_search = setTimeout(function(){
							search_form.trigger('submit');
						}, 250);
					}
				});
				<?php } ?>
			});
		</script>
		<?php if( empty( $easy_pod['output_selector'] ) ){ ?>
		<div style="min-height:35px;" class="caldera-grid <?php echo $easy_pod['result_class']; ?>" id="search_results_<?php echo $easy_pod['id']; ?>"></div>
		<?php
		}

		$template = ob_get_clean();


		return $html.$template;

	}

	/**
	 * Checks and runs a search from
	 *
	 */
	public function run_search_form() {

			global $form;

			$has_pod_search = $this->is_form_search_bound( $form );
			$form_parent = pods_v_sanitized( '_parent', 'post' );
			$nav_to = pods_v_sanitized( 'pg', 'request' );

			if( empty( $has_pod_search ) ){
				return;
			}

			$easy_pods = cep_get_registry();
			if( !isset( $easy_pods[ $has_pod_search ] ) ){
				return;
			}

			$query = cep_get_easy_pod( $has_pod_search );

			$pod = pods( $query['pod'] );
			
			$easy_pod = Caldera_Easy_Pods::get_instance();

			$params = $easy_pod->apply_query( array() , $pod, array('query' => $query ) );
		
			if( !is_wp_error( $params ) ){ 
				if( !empty( $nav_to ) ){
					$params['page'] = $nav_to;
				}				
				$pod->find( $params );
			}

			if( $pod->total_found() > 0 ){
				
				echo $pod->template( $query['params']['template'] );
				if( !empty( $query['params']['pagination'] ) ) {
					echo '<span class="search-pagination" data-form="' . $form_parent . '">';
					echo $pod->pagination( array( 'type' => $query['params']['pagination'] ) );
					echo '</span>';
				}
			}else{
				$magic = new calderawp\filter\two\magictag();
				$no_results = $magic->do_magic_tag( trim( $query['no_results'] ) );
				echo $no_results;
			}
			exit;
	}

	/**
	 * Bind attributes to search form.
	 *
	 * @since 0.2.0
	 *
	 * @uses 'caldera_forms_render_form_attributes' filter
	 *
	 * @param array $atts Form attribiutes
	 * @param array $form Form config
	 *
	 * @return array Form attributes
	 */
	public function setup_form_atts( $atts, $form ){

		$easy_pod_id = $this->is_form_search_bound( $form );
		if( empty( $easy_pod_id ) ){
			return $atts;
		}

		$easy_pod 			= cep_get_easy_pod( $easy_pod_id );

		$atts['data-podquery'] 		= $easy_pod['id'];
		$atts['data-before'] 		= 'my_cf_before_handler_' . $easy_pod['id'];
		$atts['data-callback'] 		= 'my_cf_handler_' . $easy_pod['id'];
		$atts['data-target'] 		= ( !empty( $easy_pod['output_selector'] ) ? $easy_pod['output_selector'] : '#search_results_' . $easy_pod['id'] );
		$atts['data-load-element'] 	= '#search_results_' + $easy_pod['id'];

		//backwards compat with CF older than 1.3.2 -- remove later
		if( function_exists( 'cf_ajax_api_url' ) ) {
			$api_url = cf_ajax_api_url( $form[ 'ID' ] );
		}else{
			$api_url = home_url( '/cf-api/' . $form['ID'] );
		}
		$atts[ 'data-request' ]     = $api_url;

		if( !empty( $easy_pod['init_search'] ) ){
			$atts['data-autoload'] 	= 'true';
		}
		
		$nav_to = pods_v_sanitized( 'pg', 'request' );
		if( !empty( $nav_to ) ){
			$atts['data-pg'] 		= $nav_to;
			$atts['data-autoload'] 	= 'true';
		}

		/**
		 * Filter attributes for a search form
		 *
		 * @since 0.1.0
		 *
		 * @param array $atts Search for atts
		 * @param array $easy_pod Setting for the Easy Pod
		 * @param array $form The form's options array.
		 */
		$atts = apply_filters( 'caldera_easy_pods_search_atts', $atts, $easy_pod, $form );

		
		return $atts;

	}

}

