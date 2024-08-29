<?php
/**
 * Caldera Easy Pods -- Get field data from Pods.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

/**
 * Plugin class.
 * @package Caldera_Easy_Pods_Fields
 * @author  David Cramer <david@digilab.co.za>
 */
class Caldera_Easy_Pods_Fields extends Caldera_Easy_Pods {

	/**
	 * the pod IDs that have been traversed
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $pod_treversal_log = array();

	// init
	public function __construct(){
		$this->pod_treversal_log = array();
	}

	/**
	 * Loads a pod by name and return a json string of fields
	 *
	 * @since 0.0.1
	 *
	 * @param string $podname The Pods' name.
	 *
	 * @return string JSON encoded array
	 */
	public function load_fields( $podname = false ) {
		if(!empty($_POST['pod_reference']['pod'])){
			$podname = $_POST['pod_reference']['pod'];
		}
		if(!empty($_POST['pod'])){
			$podname = $_POST['pod'];

		}
		$fields = array('No reference Pod selected');
		if(!empty($podname)){
			$pod = pods( $podname );
			$fields = array();
			foreach( $pod->pod_data['object_fields'] as $name=>$field ){
				$fields[] = $name;
			}
			$pod_fields = $pod->fields();
			if( post_type_supports( $podname, 'thumbnail' ) ){
				$fields[] = 'post_thumbnail';
				$fields[] = 'post_thumbnail_url';
				$sizes = get_intermediate_image_sizes();
				foreach( $sizes as &$size){
					$fields[] = 'post_thumbnail.'.$size;
					$fields[] = 'post_thumbnail_url.'.$size;
				}
			}
			$fields = array_merge( $fields, $this->pq_tunnel_pod_field( $pod_fields ) );
		}		
		$out['fields'] 		= $fields;
		// taxomomies
		$taxonomy_names 	= get_object_taxonomies( $podname );
		if( !empty( $taxonomy_names ) ){
			foreach( $taxonomy_names as $taxonomy ){
				$out['taxonomies'][] 		= $taxonomy . '.term_id';
				$out['taxonomies'][] 		= $taxonomy . '.name';
				$out['taxonomies'][] 		= $taxonomy . '.parent';
			}
		}

		if(!empty($_POST['pod_reference']['pod']) || !empty($_POST['pod'])){
			header("Content-Type:application/json");
			echo json_encode($out);
			die;

		}
		return $out;
	}

	/**
	 * Get field data
	 *
	 * @since 0.0.1
	 *
	 * @param array $fields
	 * @param null|string $prefix
	 *
	 * @return array
	 */
	private function pq_tunnel_pod_field( $fields, $prefix = null ){

		$out = array();
		// return out if fields are empty
		if(empty($fields)){
			return $out;
		}

		foreach($fields as $name=>$field){
			
			if( !empty( $field['table_info']['pod'] ) ){
				$this->pod_treversal_log[] = $field['table_info']['pod']['name'];
			}
			if( !empty( $field['table_info']['field_id'] ) && !in_array( $prefix .  $field['table_info']['field_id'], $out ) ){
				$out[] = $prefix .  $field['table_info']['field_id'];
			}
			if( $prefix !== null || empty( $field['table_info']['pod'] ) ){
				$out[] = $prefix . $name;
			}
			if($field['type'] === 'file' && $field['options']['file_uploader'] == 'attachment'){

			$out[] = $prefix . $name .'._src';
			$out[] = $prefix . $name .'._img';

				$sizes = get_intermediate_image_sizes();
				foreach( $sizes as &$size){
					$out[] = $prefix . $name . '._src.'.$size;
				}
				if( 'multi' != $field['options']['file_format_type']){
					foreach( $sizes as &$size){
						$out[] = $prefix . $name . '._src_relative.'.$size;
					}
					foreach( $sizes as &$size){
						$out[] = $prefix . $name . '._src_schemeless.'.$size;
					}
				}
				foreach( $sizes as &$size){
					$out[] = $prefix . $name . '._img.'.$size;
				}
			}
			if( !empty( $field['table_info'] ) ){
				if( !empty( $field['table_info']['pod'] ) ){

					// add to pod_treversal_log else continue
					if( !empty( $prefix ) && in_array( $field['table_info']['pod']['name'], $this->pod_treversal_log) ){
						$count = array_keys( $this->pod_treversal_log, $field['table_info']['pod']['name'] );
						continue;
					}

					if( false === strpos( $prefix, $name . '.' ) ){
						$pod = pods( $field['table_info']['pod']['name'] );
						// only tunnel in if there are object fields
						if(!empty($field['table_info']['object_fields'])){
							$out = array_merge( $out, $this->pq_tunnel_pod_field( $field['table_info']['object_fields'], $prefix . $name . '.' ) );
						}
						if( post_type_supports( $field['table_info']['pod']['name'], 'thumbnail' ) && $prefix !== null ){
							$out[] = $prefix . 'post_thumbnail';
							$out[] = $prefix . 'post_thumbnail_url';
							$sizes = get_intermediate_image_sizes();
							foreach( $sizes as &$size){
								$out[] = $prefix . 'post_thumbnail.'.$size;
								$out[] = $prefix . 'post_thumbnail_url.'.$size;
							}
						}
						$pod_fields = $pod->fields();
						$out = array_merge( $out, $this->pq_tunnel_pod_field( $pod_fields, $prefix . $name . '.') );
					}
				}else{
					
					if(!empty($field['table_info']['object_fields'])){
						
						$out = array_merge( $out, $this->pq_tunnel_pod_field( $field['table_info']['object_fields'], $prefix . $name . '.') );
					
					}

				}
			}
		}

		return $out;

	}

}
