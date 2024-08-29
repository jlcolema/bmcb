<?php
/**
 * Caldera Easy Pods Setting.
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
class Settings_Caldera_Easy_Pods extends Caldera_Easy_Pods{


	/**
	 * Start up
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// save config
		add_action( 'wp_ajax_cep_save_config', array( $this, 'save_config') );

		// create new
		add_action( 'wp_ajax_cep_create_easy_pods', array( $this, 'create_new_easy_pods') );

		// delete
		add_action( 'wp_ajax_cep_delete_easy_pods', array( $this, 'delete_easy_pods') );

		// query SQL preview
		add_action( 'wp_ajax_cep_query_preview', array( $this, 'query_preview') );

		// load pod fields
		add_action( 'wp_ajax_cep_loadpod', array( $this, 'pq_loadpod' ) );

		// load form field bindings
		add_action( 'wp_ajax_cep_load_search_form_fields', array( $this, 'load_search_form_fields') );

		//find params preview
		add_action( 'wp_ajax_cep_find_params', array( $this, 'find_params' ) );

		//render a live preview in wp_editor
		add_action( 'wp_ajax_cep_editor_live_preview', array( $this, 'editor_live_preview' ) );
		
		// get license key
		add_action( 'wp_ajax_cep_get_easy_pods_license', array( $this, 'get_config_license') );
						
		// queue up the shortcode inserter
		add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );

		// shortcode insterter js
		add_action( 'admin_footer', array( $this, 'add_shortcode_inserter'));

		// show notice if pods not active
		add_action( 'admin_notices', array( $this, 'check_pods_active' ) );

		// add live previews
		add_action('admin_footer-edit.php', array( $this, 'render_editor_template')); // Fired on the page with the posts table
		add_action('admin_footer-post.php', array( $this, 'render_editor_template')); // Fired on post edit page
		add_action('admin_footer-post-new.php', array( $this, 'render_editor_template')); // Fired on add new post page		

	}


	/**
	 * load the fields of a pod
	 */
	public function pq_loadpod(){

		$loadpod = new Caldera_Easy_Pods_Fields();
		$loadpod->load_fields();
	}

	/**
	 * Send JSON for the live preview
	 *
	 * @uses 'wp_ajax_cep_editor_live_preview' action
	 *
	 * @since 0.3.0
	 */
	public function editor_live_preview(){
		$post_data = pods_sanitize( $_POST );

		$post = get_post( (int) pods_v( 'post_id', $post_data ) );
		$atts = pods_v( 'atts', $post_data );

		if ( is_array( $atts ) && isset( $atts['named']['name'] ) && $post && is_object( $post ) && is_a( $post, 'WP_Post') ) {
			ob_start();
			echo do_shortcode( '[easy_pod name="' . $atts['named']['name'] . '"]' );
			$html = ob_get_clean();

			$out = array(
				'shortcode' => $post_data['atts']['named']['name'],
				'html'      => $html
			);

			wp_send_json_success( $out );

		}else{
			wp_send_json_error();

		}

		exit;

	}

	/**
	 * Creates the template to use while live previewing in wp_editor
	 *
	 * @uses 'admin_footer-edit.php', 'admin_footer-post.php', 'admin_footer-post-new.php' actions
	 *
	 * @since 0.3.0
	 */
	public function render_editor_template(){
		?>
	<script type="text/html" id="tmpl-editor-caldera-easy-pods">
		<# if ( data.html ) { #>
			<input type="hidden" value="{{{ data.shortcode }}}" class="cep-selected-shortcode">
			{{{ data.html }}}
		<# } else { #>
			<div class="wpview-error" style="color:#cf0000;">
				<div class="dashicons dashicons-arrow-right-alt2"></div><p style="font-size: 13px;"><?php _e( 'Invalid Easy Pod', 'easy-pods' ); ?></p>
			</div>
		<# } #>
	</script>
	<?php

	}
	/**
	 * Loads connection and returns the tables fields via AJAX
	 *
	 * @since 0.0.1
	 *
	 * @uses 'wp_ajax_cep_load_search_form_fields' action
	 */
	public function load_search_form_fields(){

		$forms = cep_get_forms();

		if( isset( $forms[ $_POST['_value'] ] ) ){
			$form = cep_get_form( $forms[ $_POST['_value'] ]['ID'] );

			$new_field_binding = array();
			if( !empty( $form['fields'] ) ){
				foreach( $form['fields'] as $field_id=>$field ){
					if( $field['type'] == 'button' || $field['type'] == 'html' ){
						continue;
					}

					$new_field_binding[$field_id]['slug'] = $field['slug'];
					if( in_array( $field['type'], array('checkbox', 'dropdown', 'radio', 'toggle_switch') ) ){
						$new_field_binding[$field_id]['option'] = true;
					}
				}
			}

			wp_send_json( array('search_form' => $forms[ $_POST['_value'] ]['ID'], 'form_fields' => $new_field_binding ) );

		}

		wp_send_json_error( __('Invalid form selection or form removed.', 'db-streams') );

	}

	/**
	 * Builds query SQL preview via AJAX
	 *
	 * @since 0.0.1
	 *
	 * @uses 'wp_ajax_cep_query_preview' action
	 */
	public function query_preview(){
		$can = Caldera_Easy_Pods_Options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( 'Access denied', 'easy-pods');

		}

		require_once(CEP_PATH . 'classes/sql-formatter.php');

		$pod = $this->preview_config_pods_object();

		$sql = str_replace( "\n", "", trim( $pod->sql ) );
		$sql = str_replace( "\r", "", $sql);
		$sql = str_replace( "\t", "", $sql);
		$sql = str_replace( "  ", " ", $sql);

		if( empty( $_POST['format'] ) ){
			$sql = SqlFormatter::format( $sql, false );
		}else{
			$sql = SqlFormatter::compress( $sql );
			//$sql = SqlFormatter::highlight( $sql );
		}
		echo $sql;// . '</code>';
		die;


	}

	/**
	 * Checks if Pods is Active
	 *
	 * @since 0.0.1
	 *
	 * @uses 'admin_notices' action
	 */
	public function check_pods_active(){
		if( ! defined( 'PODS_VERSION') ){
			echo '<div class="error" id="message-no-pods"><p>' . __('Caldera Easy Pods requires Pods to be installed and activated.','easy-pods') . '</p></div>';
		}

	}

	/**
	 * Insert shortcode media button
	 *
	 * @since 0.0.1
	 *
	 * @uses 'media_buttons' action
	 */
	public function shortcode_insert_button(){
		global $post;
		if(!empty($post)){
			echo "<a id=\"easy-pods-insert\" title=\"".__('Add Easy Pod','easy-pods')."\" class=\"button easy-pods-insert-button\" href=\"#inst\" style=\"padding-left: 20px; background: url(&quot;". CEP_URL . "assets/css/cep-logo.png&quot;) no-repeat scroll -3px 0px / 26px auto transparent;\">\n";
			echo __('Caldera Easy Pods', 'easy-pods')."\n";
			echo "</a>\n";
		}

	}	
	/**
	 * Insert shortcode modal template to post editor.
	 *
	 * @since 0.0.1
	 *
	 * @uses 'admin_footer' action
	 */
	public static function add_shortcode_inserter(){
		
		$screen = get_current_screen();

		if( $screen->base === 'post'){
			include CEP_PATH . 'includes/insert_shortcode.php';
		}

	} 

	/**
	 * Saves an Easy Pods config
	 *
	 * @since 0.0.1
	 *
	 * @uses 'wp_ajax_cep_save_config' action
	 */
	public function save_config(){
		$can = Caldera_Easy_Pods_Options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( 'Access denied', 'easy-pods');

		}

		if( empty( $_POST['easy-pods-setup'] ) || !wp_verify_nonce( $_POST['easy-pods-setup'], 'easy-pods' ) ){
			if( empty( $_POST['config'] ) ){
				return;
			}
		}

		if( !empty( $_POST['easy-pods-setup'] ) && empty( $_POST['config'] ) ){
			$config = stripslashes_deep( $_POST );
			$config = $this->add_sanitization_and_validation( $config );
			$easy_podss = cep_get_registry();

			if( isset( $config['id'] ) && !empty( $easy_podss[ $config['id'] ] ) ){
				$updated_registery = array(
					'id'		=>	$config['id'],
					'name'		=>	$config['name'],
					'slug'		=>	$config['slug'],
					'pod'		=>	$config['pod'],
					'template'	=>	$config['params']['template'],
				);

				// add search form to registery
				if( !empty( $config['search_form'] ) ){
					$updated_registery['search_form'] = $config['search_form'];
				}
				
				$easy_podss[$config['id']] = $updated_registery;
				$saved = cep_update_registry( $easy_podss );
				if( ! $saved ) {
					status_header( 500 );
					return;
				}

			}

			$saved = cep_update_easy_pod( $config['id'], $config );

			wp_redirect( '?page=easy_pods&updated=true' );
			exit;

		}

		if( !empty( $_POST['config'] ) ){
			$config = json_decode( stripslashes_deep( $_POST['config'] ), true );
			$config = $this->add_sanitization_and_validation( $config );
			if(	wp_verify_nonce( $config['easy-pods-setup'], 'easy-pods' ) ){
				$easy_podss = cep_get_registry();

				if( isset( $config['id'] ) && !empty( $easy_podss[ $config['id'] ] ) ){
					$updated_registery = array(
						'id'		=>	$config['id'],
						'name'		=>	$config['name'],
						'slug'		=>	$config['slug'],
						'pod'		=>	$config['pod'],
						'template'	=>	$config['params']['template'],
					);
					// add search form to registery
					if( !empty( $config['search_form'] ) ){
						$updated_registery['search_form'] = $config['search_form'];
					}

					$easy_podss[$config['id']] = $updated_registery;
					$saved = cep_update_registry( $easy_podss );

				}

				$saved = cep_update_easy_pod( $config['id'], $config );

				wp_send_json_success( $config );
			}
		}

		// nope
		status_header( 500 );
		wp_send_json_error( $config );

	}

	/**
	 * Adds the filter for sanization and/ or validation of each setting when saving.
	 *
	 * @param array $config Data being saved
	 *
	 * @return array
	 */
	protected function add_sanitization_and_validation( $config ) {
		foreach( $config as $setting => $value ) {
			if ( ! in_array( $setting, $this->internal_config_fields() ) ) {
				include_once( dirname( __FILE__ ) . '/sanatize.php' );
				$filtered = Settings_Caldera_Easy_Pods_Sanitize::apply_sanitization_and_validation( $setting, $value, $config );
				$config = $filtered;
			}

		}

		return $config;

	}

	/**
	 * Array of "internal" fields not to mess with
	 *
	 * @return array
	 */
	protected function internal_config_fields() {
		return array( '_wp_http_referer', 'id', '_current_tab' );
	}


	/**
	 * Deletes an Easy Pod
	 *
	 * @since 0.0.1
	 *
	 * @uses 'wp_ajax_cep_delete_easy_pods' action
	 */
	public function delete_easy_pods(){

		$search_blocks = cep_get_registry();
		if( isset( $search_blocks[ $_POST['block'] ] ) ){
			delete_option( $search_blocks[$_POST['block']]['id'] );

			unset( $search_blocks[ $_POST['block'] ] );
			$deleted = cep_update_registry( $search_blocks );
			if ( ! $deleted ) {
				status_header( 500 );
				die();
			}

			wp_send_json_success( $_POST );
		}
		
		wp_send_json_error( $_POST );

	}

	/**
	 * Create a new Easy Pod
	 *
	 * @since 0.0.1
	 *
	 * @uses 'wp_ajax_cep_create_easy_pods'
	 */
	public function create_new_easy_pods(){
		$can = Caldera_Easy_Pods_Options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( 'Access denied', 'easy-pods');

		}
		
		$easy_podss = cep_get_registry();
		if( empty( $easy_podss ) ){
			$easy_podss = array();
		}

		$easy_pods_id = uniqid('CEP').rand(100,999);
		if( !isset( $easy_podss[ $easy_pods_id ] ) ){
			$new_easy_pods = array(
				'id'		=>	$easy_pods_id,
				'name'		=>	$_POST['name'],
				'slug'		=>	$_POST['slug'],
				'pod'		=>	$_POST['pod'],
				'template'	=>	'',
				'_current_tab' => '#easy-pods-panel-general'
			);
			$created = cep_update_easy_pod( $easy_pods_id, $new_easy_pods );

			if ( ! $created ) {
				status_header( 500 );
				die();

			}

			$easy_podss[ $easy_pods_id ] = $new_easy_pods;
		}

		cep_update_registry( $easy_podss );

		// end
		wp_send_json_success( $new_easy_pods );

	}

	/**
	 * Add options page
	 *
	 * @uses 'admin_menu'
	 *
	 * @since 0.0.1
	 */
	public function add_settings_pages(){
		if( defined( 'PODS_VERSION' ) ) {
			$slug = $this->plugin_screen_hook_suffix[ 'easy_pods' ] =  add_menu_page(
				__( 'Caldera Easy Pods', 'easy-pods' ),
				__( 'Easy Pods', 'easy-pods' ),
				'manage_options',
				'easy_pods',
				array(
					$this, 'create_admin_page'
				),
				'dashicons-arrow-right-alt2'
			);

			add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix['easy_pods'], array(
				$this,
				'enqueue_admin_stylescripts' )
			);


		}



	}


	/**
	 * Options page callback
	 *
	 * @since 0.0.1
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		if( empty( $_GET['edit'] ) ){
			include CEP_PATH .'includes/admin.php';
		}else{
			include CEP_PATH .'includes/edit.php';
		}


		// php based script include
		if( file_exists( CEP_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
				include CEP_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}


	/**
	 * Display a preview of find params
	 *
	 * @since 0.2.0
	 *
	 * @uses 'wp_ajax_cep_find_params' action
	 */
	public function find_params() {
		$can = Caldera_Easy_Pods_Options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( 'Access denied', 'easy-pods');

		}

		$params = $this->preview_config_pods_object( true );

		if ( is_array( $params ) ) {
			if ( $params ) {
				echo '$params = ';
				var_export( $params );
				echo ';';
			}

		}

		exit;

	}

	/**
	 * Preview a query.
	 *
	 * @since 0.2.0
	 *
	 * @param bool $params_only
	 *
	 * @return array|bool|\Pods
	 */
	protected function preview_config_pods_object( $params_only = false ) {
		$data = stripslashes_deep( $_POST );
		$config = json_decode( $data['config'], true );
		$pod = pods( $config['pod'] );
		$params = $this->apply_query( array() , $pod, array('query' => $config ) );
		if ( $params_only ) {
			return $params;
		}

		$pod->find( $params );

		return $pod;

	}

}

/**
 * Initialize class
 */
if( is_admin() ) {
	$settings_easy_pods = new Settings_Caldera_Easy_Pods();
}

