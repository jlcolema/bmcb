<?php
/**
 * Create Main Admin
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>

<div class="wrap">
	<div class="easy-pods-main-headercaldera">
		<h2>
			<?php _e( 'Caldera Easy Pods', 'easy-pods' ); ?> <span class="easy-pods-version"><?php echo CEP_VER; ?></span>
			<span class="add-new-h2 wp-baldrick" data-modal="new-easy_pods" data-modal-height="222" data-modal-width="402" data-modal-buttons='<?php _e( 'Create Easy Pod', 'easy-pods' ); ?>|{"data-action":"cep_create_easy_pods","data-before":"cep_create_new_easy_pods", "data-callback": "bds_redirect_to_easy_pods"}' data-modal-title="<?php _e('New Easy Pod', 'easy-pods') ; ?>" data-request="#new-easy_pods-form"><?php _e('Add New', 'easy-pods') ; ?></span>


		</h2>
	</div>

<?php
	// get pods list
	$api = pods_api();
	$_pods = $api->load_pods();

	$easy_podss = cep_get_registry();
	if( empty( $easy_podss ) ){
		$easy_podss = array();
		echo '<br><p class="description">' . __( 'You dont have any Caldera Easy Pods yet.', 'easy-pods' ) . '</p>';
	}

	foreach( $easy_podss as $easy_pods_id => $easy_pods ){

?>

	<div class="easy-pods-card-item" id="easy_pods-<?php echo $easy_pods['id']; ?>">
		<span class="easy-pods-card-icon"></span>
		<div class="easy-pods-card-content">
			<h4>
				<?php echo $easy_pods['name']; ?>
			</h4>
			<div class="description" style="text-overflow: ellipsis; white-space: nowrap; width: 239px; overflow: hidden;">
				<?php _e( 'Pod', 'easy-pods' ); ?> <?php echo $easy_pods['pod']; ?>
			</div>

			<div class="description" style="text-overflow: ellipsis; white-space: nowrap; width: 239px; overflow: hidden;">[easy_pod name="<?php echo $easy_pods['slug']; ?>"]<?php if( empty($easy_pods['template'] ) ){ echo __('Template', 'easy-pods') . '[/easy_pod]'; } ?></div>
			<div class="easy-pods-card-actions" style="margin: 4px 0px 0px;">
				<div class="row-actions">
					<span class="edit"><a href="?page=easy_pods&amp;edit=<?php echo $easy_pods['id']; ?>"><?php _e( 'Edit', 'easy-pods' ); ?></a> | </span>
					<span class="trash confirm"><a href="?page=easy_pods&amp;delete=<?php echo $easy_pods['id']; ?>" data-block="<?php echo $easy_pods['id']; ?>" class="submitdelete"><?php _e( 'Delete', 'easy-pods' ); ?></a></span>
				</div>
				<div class="row-actions" style="display:none;">
					<span class="trash"><a class="wp-baldrick" style="cursor:pointer;" data-action="cep_delete_easy_pods" data-callback="cep_remove_deleted" data-block="<?php echo $easy_pods['id']; ?>" class="submitdelete"><?php _e( 'Confirm Delete', 'easy-pods' ); ?></a> | </span>
					<span class="edit confirm"><a href="?page=easy_pods&amp;edit=<?php echo $easy_pods['id']; ?>"><?php _e( 'Cancel', 'easy-pods' ); ?></a></span>
				</div>
			</div>
		</div>
	</div>

	<?php } ?>

</div>
<div class="clear"></div>
<script type="text/javascript">

	function cep_create_new_easy_pods(el){
		var easy_pods 	= jQuery(el),
			name 	= jQuery("#new-easy_pods-name"),
			slug 	= jQuery('#new-easy_pods-slug'),
			pod 	= jQuery('#new-easy_pods-pod');

		if( slug.val().length === 0 ){
			name.focus();
			return false;
		}
		if( slug.val().length === 0 ){
			slug.focus();
			return false;
		}
		if( pod.val().length === 0 ){
			pod.focus();
			return false;
		}

		easy_pods.data({ name: name.val(), slug : slug.val(), pod : pod.val() } );

	}

	function bds_redirect_to_easy_pods(obj){

		if( obj.data.success ){

			obj.params.trigger.prop('disabled', true).html('<?php _e('Loading Easy Pod', 'easy-pods'); ?>');
			window.location = '?page=easy_pods&edit=' + obj.data.data.id;

		}else{

			jQuery('#new-block-slug').focus().select();

		}
	}
	function cep_remove_deleted(obj){

		if( obj.data.success ){
			jQuery( '#easy_pods-' + obj.data.data.block ).fadeOut(function(){
				jQuery(this).remove();
			});
		}else{
			alert('<?php echo __('Sorry, something went wrong. Try again.', 'easy-pods'); ?>');
		}


	}
</script>
<script type="text/html" id="new-easy_pods-form">
	<div class="easy-pods-config-group">
		<label><?php _e('Name', 'easy-pods'); ?></label>
		<input type="text" name="name" id="new-easy_pods-name" data-sync="#new-easy_pods-slug" autocomplete="off">
	</div>
	<div class="easy-pods-config-group">
		<label><?php _e('Slug', 'easy-pods'); ?></label>
		<input type="text" name="slug" id="new-easy_pods-slug" data-format="slug" autocomplete="off">
	</div>
	<div class="easy-pods-config-group">
		<label><?php _e( 'Pod', 'easy-pods' ); ?></label>
		<select name="pod" id="new-easy_pods-pod" style="width: 190px;">
		<option></option>
		<?php
			foreach($_pods as $pod){
				echo "<option value=\"" . $pod['name'] . "\" {{#is pod value=\"" . $pod['name'] . "\"}}selected=\"selected\"{{/is}}>" . $pod['label'] . "</option>\r\n";
			}
		?>
		</select>
	</div>
</script>


