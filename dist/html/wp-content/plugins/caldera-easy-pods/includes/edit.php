<?php
/**
 * Creates the editor admin for an Easy Pod.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
$easy_pods = cep_get_easy_pod( $_GET['edit'] );
if( !empty( $easy_pods['pod'] ) ){
	//update pod fields
	$loadpod = new Caldera_Easy_Pods_Fields();
	$easy_pods['pod_fields'] = $loadpod->load_fields( $easy_pods['pod'] );
}

// get pods list
$api = pods_api();
$_pods = $api->load_pods();

$easy_pods = cep_pre_editor( $easy_pods, $_pods );

?>
<div class="wrap" id="easy-pods-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#easy-pods-main-canvas" data-callback="cep_canvas_init" data-type="json" data-request="#easy-pods-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="easy-pods-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($easy_pods) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
	// pull in the join table card template
	include CEP_PATH . 'includes/templates/main-ui.php';
	?>	
</script>

