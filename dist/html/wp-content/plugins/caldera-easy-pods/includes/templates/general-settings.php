<?php
/**
 * The general admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

	// set templates list
	$templates = $api->load_templates();
	$template_count = count( $templates );
?>

		<div class="easy-pods-config-group">
			<label for="easy_pods-name"><?php _e( 'Query Name', 'easy-pods' ); ?></label>
			<input type="text" name="name" value="{{name}}" data-sync="#easy_pods-name-title" id="easy_pods-name" required>
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-slug"><?php _e( 'Slug', 'easy-pods' ); ?></label>
			<input type="text" name="slug" value="{{slug}}" data-format="slug" data-sync=".easy-pods-subline" data-master="#easy_pods-name" id="easy_pods-slug" required>
		</div>

		<div class="easy-pods-config-group">
			<input type="hidden" id="pod_fields" value="{{json pod_fields}}" name="pod_fields">
			<label for="easy_pods-pod"><?php _e( 'Pod', 'easy-pods' ); ?></label>
			<select class="wp-baldrick" {{#unless pod_fields}} data-autoload="true"{{/unless}} data-load-element="#easy-pods-save-indicator" data-event="change" name="pod" id="easy_pods-pod" data-action="cep_loadpod" data-callback="cep_record_change" data-target="#pod_fields" required>
			<?php 
				foreach($_pods as $pod){
					echo "<option value=\"" . $pod['name'] . "\" {{#is pod value=\"" . $pod['name'] . "\"}}selected=\"selected\"{{/is}}>" . $pod['label'] . "</option>\r\n";
				}
			?>
			</select> <button type="button" class="wp-baldrick button" data-for="#easy_pods-pod"><?php _e('Reload Fields', 'easy-pods'); ?></button>
		</div>
		{{#if search_form}}
			{{#is search_form value=""}}
				<div class="easy-pods-config-group">
					<label for="easy_pods-template"><?php _e( 'Template', 'easy-pods' ); ?></label>
					<select data-live-sync="true" name="params[template]" id="easy_pods-template">

						<option value="">- <?php _e( 'Custom Template', 'easy-pods' ); ?> -</option>
						<?php foreach ( $templates as $tmpl ) { ?>
						<option {{#is params/template value="<?php echo $tmpl[ 'name' ]; ?>"}}selected="selected"{{/is}} value="<?php echo $tmpl[ 'name' ]; ?>">
							<?php echo $tmpl[ 'name' ]; ?>
						</option>
						<?php } ?>
					</select>
					{{#is params/template value=""}}<p style="margin-left: 185px;" class="description"><?php _e( 'Be sure to include the custom template between the Shortcode Tags.', 'easy-pods' ); ?></p>{{/is}}
				</div>
			{{/is}}
		{{else}}
			<div class="easy-pods-config-group">
				<label for="easy_pods-template"><?php _e( 'Template', 'easy-pods' ); ?></label>
				<select data-live-sync="true" name="params[template]" id="easy_pods-template">

					<option value="">- <?php _e( 'Custom Template', 'easy-pods' ); ?> -</option>
					<?php foreach ( $templates as $tmpl ) { ?>
					<option {{#is params/template value="<?php echo $tmpl[ 'name' ]; ?>"}}selected="selected"{{/is}} value="<?php echo $tmpl[ 'name' ]; ?>">
						<?php echo $tmpl[ 'name' ]; ?>
					</option>
					<?php } ?>
				</select>
				{{#is params/template value=""}}<p style="margin-left: 185px;" class="description"><?php _e( 'Be sure to include the custom template between the Shortcode Tags.', 'easy-pods' ); ?></p>{{/is}}
			</div>
		{{/if}}
