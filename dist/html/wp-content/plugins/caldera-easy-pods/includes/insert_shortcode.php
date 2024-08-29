<?php
/**
 *The shortcode insert modal.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>

<div class="pod-query-backdrop easy-pods-insert-modal" style="display: none;"></div>
<div id="easy_pods_shortcode_modal" class="pod-query-modal-wrap easy-pods-insert-modal" style="display: none; width: 600px; max-height: 500px; margin-left: -300px;">
	<div class="pod-query-modal-title" id="easy_pods_shortcode_modalTitle" style="display: block;">
		<a href="#close" class="pod-query-modal-closer" data-dismiss="modal" aria-hidden="true" id="easy_pods_shortcode_modalCloser">×</a>
		<h3 class="modal-label" id="easy_pods_shortcode_modalLable"><?php echo __('Insert Easy Pod', 'easy-pods'); ?></h3>
	</div>
	<div class="pod-query-modal-body none" id="easy_pods_shortcode_modalBody">
		<div class="modal-body">
		<?php

			$easy_podss = cep_get_registry();
			

			if(!empty($easy_podss)){
				foreach( $easy_podss as $easy_pods_id => $easy_pods ){

					$is_message ='';

					echo '<div class="modal-list-item-cep"><label><input name="insert_query_id" autocomplete="off" class="selected-query-shortcode" data-template="'.$easy_pods['template'].'" value="' . $easy_pods['slug'] . '" type="radio">' . $easy_pods['name'];
					if(!empty($easy_pods['pod'])){
						echo '<span class="description"> '.$easy_pods['pod'] .' ' . $is_message . '</span>';
					}
					echo ' </label></div>';
					

				}
			}else{
				echo '<p>' . __('You don\'t have any Caldera Easy Pods to insert.', 'easy-pods') .'</p>';
			}

		?>
		</div>
	</div>
	<div class="pod-query-modal-footer" id="easy_pods_shortcode_modalFooter" style="display: block;">
	<?php if(!empty($easy_podss)){ ?>
		<p class="modal-label-subtitle"><button class="button easy-pods-shortcode-insert" style="float:left;margin:5px 25px 0 15px;"><?php echo __('Insert Selected', 'easy-pods'); ?></button></p>
	<?php }else{ ?>
		<button class="button pod-query-modal-closer"><?php echo __('Close', 'easy-pods'); ?></button>
	<?php } ?>
	</div>
</div>
