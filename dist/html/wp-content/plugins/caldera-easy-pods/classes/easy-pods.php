<?php
/**
 * Caldera Easy Pods.
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
class Caldera_Easy_Pods {

	/**
	 * The plugin's slug
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'easy-pods';

	/**
	 * Will hold class instance
	 *
	 * @since 0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * The plugin's screen hook suffix
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.0.1
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// Load front style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_stylescripts' ) );


		// add shortcode hook
		//add_filter( 'pods_shortcode', array( $this, 'query_tags') );

		// shortcode
		add_shortcode( 'easy_pod', array( $this, 'render_easy_pod') );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.1
	 * 
	 * @return Caldera_Easy_Pods|object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CEP_PATH ) . '/languages');

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_front_stylescripts() {
		wp_enqueue_style( 'easy_pods-grid', CEP_URL . '/assets/css/front-grid.css' );
	}
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		if( $screen->base == 'post' ){
			wp_enqueue_style( 'easy_pods-baldrick-modals', CEP_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'easy_pods-shortcode-insert', CEP_URL . '/assets/js/shortcode-insert.js', array( 'jquery' ) , false, true );
		}
		if( false !== strpos( $screen->base, 'easy_pods' ) ){

			wp_enqueue_style( 'easy_pods-core-style', CEP_URL . '/assets/css/styles.css' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'easy_pods-baldrick-modals', CEP_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'easy_pods-wp-baldrick', CEP_URL . '/assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'easy_pods-core-script', CEP_URL . '/assets/js/scripts.js', array( 'easy_pods-wp-baldrick' ) , false );

			if( !empty( $_GET['edit'] ) ){
				wp_enqueue_style( 'easy-pods-core-grid', CEP_URL . '/assets/css/grid.css' );
				wp_enqueue_script( 'easy_pods-core-grid-script', CEP_URL . '/assets/js/grid.js', array( 'jquery' ) , false );
				wp_enqueue_style( 'easy_pods-prism-style', CEP_URL . '/assets/css/prism.css' );
				wp_enqueue_script( 'easy_pods-prism', CEP_URL . '/assets/js/prism.js', array( 'jquery' ) , false, true );
				
				wp_enqueue_style( 'easy_pods-select2-style', CEP_URL . 'assets/css/select2.css' );
				wp_enqueue_script( 'easy_pods-select2-script', CEP_URL . 'assets/js/select2.min.js', array( 'jquery' ) , false, true );


			}
		
		}


	}

	/**
	 * Find an easy pod by name/slug
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Name or slug to find
	 *
	 * @return array|bool
	 */
	public function get_easy_pod( $name ) {
		
		//slug		
		$easy_pods = cep_get_registry();
		foreach( $easy_pods as $easy_pod ){
			if( $easy_pod['slug'] === $name ){
				return cep_get_easy_pod( $easy_pod['id'] );

			}
		}
		// name
		foreach( $easy_pods as $easy_pod ){
			if( $easy_pod['name'] === $name ){
				return cep_get_easy_pod( $easy_pod['id'] );
			}
		}

		return false;

	}

	/**
	 * Add a query tag to a easy Pods query
	 *
	 * @since 0.0.1
	 *
	 * @return mixed
	 */
	public function render_easy_pod($params, $content, $code) {

		$slug = $params['name'];
		unset( $params['name'] );

		$template = null;
		if( isset( $params['template'] ) ){
			$template = $params['template'];
			unset( $params['template'] );
		}
		if( !empty( $content ) ){
			$template = $content;
		}

		return cep_render_easy_pod( $slug, $params, $template );

	}

	/**
	 * Build and apply query for pod shortcode
	 *
	 * @since 0.0.1
	 *
	 * @return array Pods::find() params
	 */
	public function apply_query(  $params, $pod, $tags ) {

		$easy_pod_slug = $tags[ 'query' ][ 'slug' ];

		if ( ! isset( $tags['query']['params'] ) || empty( $tags['query']['params'] ) ) {
			$tags['query']['params'] = array();
		}

		if( !empty( $tags['query']['filter'] ) ){

			/**
			 * Fires before query params are applied.
			 *
			 * @since 0.0.1
			 *
			 * @param array $params Parameters array we started with. Is most likely empty at this point.
			 * @param object|\Pods Pods object
			 * @param array $tags The tags we are using to build query after this action.
			 */
			do_action( 'caldera_easy_pods_start_query_params', $params, $pod, $tags );

			$magic = new calderawp\filter\two\magictag();
			$comps = array(
				"is"			=>	array( 'format' => '%s', 	'compare' => '=' ),
				"isnot"			=>	array( 'format' => '%s',	'compare' => '!=' ),
				"isin"			=>	array( 'format' => array(),	'compare' => 'IN' ),
				"isnotin"		=>	array( 'format' => array(),	'compare' => 'NOT IN' ),
				"greater"		=>	array( 'format' => '%s',	'compare' => '>' ),
				"smaller"		=>	array( 'format' => '%s',	'compare' => '<' ),
				"greatereq"		=>	array( 'format' => '%s','compare' => '>=' ),
				"smallereq"		=>	array( 'format' => '%s','compare' => '<=' ),				
				"startswith"	=>	array( 'format' => '%%%s',	'compare' => 'LIKE' ),
				"endswith"		=>	array( 'format' => '%s%%', 	'compare' => 'LIKE' ),
				"contains"		=>	array( 'format' => '%s','compare' => 'LIKE' ),				
			);

			$groups = $tags['query']['filter'];			
			
			$params['where']	= array(
				'relation'	=>	'OR'
			);

			foreach( $groups as $group ){

				$lines = array(
					'relation'	=>	'AND'
				);

				foreach( $group['line'] as $line ){

					$value = $magic->do_magic_tag( trim( $line['value'] ) );
					if ( 'taxonomy' == cep_pod_type( $pod ) && isset( $line[ 'field' ] ) &&  $line[ 'field' ] && 'parent' == $line[ 'field'] ) {
						$line[ 'field' ] = 'tt.parent';
					}

					/**
					 * Filter the value of an individual filter being used to build the query
					 *
					 * @since 0.0.1
					 *
					 * @param string|null $value The value for the query part.
					 * @param array $group The group the query part is from.
					 * @param array $line The line the query part if from.
					 * @param string $easy_pod_slug The slug for the Easy Pod we are currently rendering.
					 */
					$value = apply_filters( 'caldera_easy_pods_filter_value', $value, $group, $line, $easy_pod_slug );

					if( strlen( $line['value'] ) > 0 && ( strlen( $value ) === 0 || $value === null ) ){

						if( empty( $line['if_available'] ) ){
							return new WP_Error('error', esc_html__( sprintf( 'Field %1s Is Required', $line[ 'field' ] ), 'easy-pods') );
						}else{
							continue;
						}

					}

					if( is_array( $comps[ $line['compare'] ]['format'] ) ){
						$value = array_map('trim', explode( ',', $value ) );
					}else{
						$value = sprintf( $comps[ $line['compare'] ]['format'], $value );
					}

					if ( ! isset( $line[ 'field' ] ) ) {
						$line[ 'field' ] = '';
					}

					$lines[] = array(
						'field'		=>	$line['field'],
						'value'		=>	$value,
						'compare'	=>	$comps[ $line['compare'] ]['compare'],
					);
				}
				if( count( $lines ) > 1 ){
					if( count( $lines ) === 2 ){
						unset( $lines['relation'] );
						$params['where'][] = $lines[0];
					}else{
						$params['where'][] = $lines;
					}
				}

			}

		}
		if( isset( $params['where'] ) && count( $params['where'] ) === 1 ){
			unset( $params['where'] );
		}
		if( isset( $params['where'] ) && count( $params['where'] ) === 2 ){
			unset( $params['where']['relation'] );
		}

		// order
		if( !empty( $tags['query']['params']['orderby']['field'] ) ){
			$params['orderby'] = $tags['query']['params']['orderby']['field'] .' '. $tags['query']['params']['orderby']['direct'];
			if ( 'post_type' == $pod->pod_data[ 'type'] && 'meta' == $pod->pod_data[ 'storage'] && isset( $params[ 'orderby' ] ) ) {
				$fields = $pod->fields();
				$fields = array_combine( array_keys( $fields ), wp_list_pluck( $fields, 'type' ) );
				$orderby = $params[ 'orderby' ];

				if ( strpos( $orderby, 'ASC' ) ) {
					$direction = 'ASC';
				}else{
					$direction = 'DESC';
				}

				$orderby = trim( str_replace( $direction, '', $orderby ) );
				if ( isset( $fields[ $orderby ] ) ) {
					$type = $fields[ $orderby ];
					if ( 'date' == $type ) {
						$params[ 'orderby' ] = sprintf( 'CAST( %1s.meta_value as DATE) %2s', $orderby, $direction );
					}elseif ( 'number' == $type ) {
						$params[ 'orderby' ] = sprintf( 'CAST( %1s.meta_value as DECIMAL) %2s', $orderby, $direction );
					}

				}


			}
		}

		// cache mode
		if ( ! isset( $tags['query']['params']['cache_mode'] ) ) {
			$params['cache_mode'] = 'cache';
		} else {
			$params['cache_mode'] = $tags['query']['params']['cache_mode'];
		}

		// limit
		if( !empty( $tags['query']['params']['limit'] ) ){
			$params['limit'] = $tags['query']['params']['limit'];
		}

		// expires
		if( !empty( $tags['query']['params']['expires'] ) ){
			$params['expires'] = $tags['query']['params']['expires'];
		}


		/**
		 * Filter the query's params before returning it
		 *
		 * @since 0.0.1
		 *
		 * @param array $params The query params to be passed to Pods::find()
		 * @param string $pod The Name of the Pod
		 * @param array $tags The tags from the shortcode
		 * @param string $easy_pod_slug The slug for the Easy Pod we are currently rendering.
		 */
		return apply_filters( 'caldera_easy_pods_query_params', $params, $pod, $tags, $easy_pod_slug );

	}


}















