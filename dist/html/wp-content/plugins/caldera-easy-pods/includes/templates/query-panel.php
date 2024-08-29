<?php
/**
 * The query editor admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>
<button class="button wp-baldrick" data-request="cep_get_default_setting" data-add-node="filter.line"><?php echo __('Add Filter Group', 'easy-pods'); ?></button>

<div class="easy-pods-filter-groups" id="cep_builder_sql">
{{#each filter}}
	{{#if line}}
	<div class="easy-pods-filter-group">
		<div class="easy-pods-filter-group-or"><?php _e('or', 'easy-pods'); ?></div>
		<span style="color:#cfcfcf;" class="dashicons dashicons-arrow-right-alt2 easy-pods-filter-group-icon"></span>
		<input type="hidden" name="filter[{{_id}}][_id]" value="{{_id}}">
		<div class="easy-pods-filter-group-content">				



			{{#each line}}
				
				<div class="easy-pods-filter-line" style="clear:right;">						
					
					<span class="easy-pods-filter-group-icon" style="color: rgb(207, 207, 207); width: 40px; text-align: center; text-transform: uppercase; font-size: 14px; line-height: 31px;"><?php _e('and', 'easy-pods'); ?></span>

					<input type="hidden" name="filter[{{../_id}}][line][{{_id}}][_id]" value="{{_id}}">

					<select name="filter[{{../_id}}][line][{{_id}}][field]" style="margin-right: 0px; width: 216px;" class="cep-select2">
						{{#if ../../../pod_fields/taxonomies}}
							<optgroup label="<?php _e( 'Taxonomies', 'easy-pods' ); ?>">						
							{{#each ../../../../pod_fields/taxonomies}}
									<option value="{{this}}" {{#is ../field value=this}}selected="selected"{{/is}}>{{this}}</option>
							{{/each}}
							</optgroup>
						{{/if}}
						<optgroup label="<?php _e( 'Fields', 'easy-pods' ); ?>">
						{{#each ../../../pod_fields/fields}}				
								<option value="{{this}}" {{#is ../field value=this}}selected="selected"{{/is}}>{{this}}</option>
						{{/each}}
						</optgroup>

					</select>

					<select name="filter[{{../_id}}][line][{{_id}}][compare]" style="margin-right: 0px; width: 113px; text-align: center;" class="cep-select2">

						<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}>=</option>
						<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}>!=</option>
						<option value="isin" {{#is compare value="isin"}}selected="selected"{{/is}}>IN</option>
						<option value="isnotin" {{#is compare value="isnotin"}}selected="selected"{{/is}}>NOT IN</option>
						<option value="greater" {{#is compare value="greater"}}selected="selected"{{/is}}>&gt;</option>
						<option value="greatereq" {{#is compare value="greatereq"}}selected="selected"{{/is}}>&gt;=</option>
						<option value="smaller" {{#is compare value="smaller"}}selected="selected"{{/is}}>&lt;</option>
						<option value="smallereq" {{#is compare value="smallereq"}}selected="selected"{{/is}}>&lt;=</option>
						<!--<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}>=</option>
						<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}>=</option>-->
						<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}>CONTAINS</option>


					<?php /*
						<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}><?php echo __('is', 'easy-pods'); ?></option>
						<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}><?php echo __('is not', 'easy-pods'); ?></option>
						<option value="isin" {{#is compare value="isin"}}selected="selected"{{/is}}><?php echo __('is in', 'easy-pods'); ?></option>
						<option value="isnotin" {{#is compare value="isnotin"}}selected="selected"{{/is}}><?php echo __('is not in', 'easy-pods'); ?></option>
						<option value="greater" {{#is compare value="greater"}}selected="selected"{{/is}}><?php echo __('is greater than', 'easy-pods'); ?></option>
						<option value="smaller" {{#is compare value="smaller"}}selected="selected"{{/is}}><?php echo __('is less than', 'easy-pods'); ?></option>
						<!--<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}><?php echo __('starts with', 'easy-pods'); ?></option>
						<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}><?php echo __('ends with', 'easy-pods'); ?></option>-->
						<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}><?php echo __('contains', 'easy-pods'); ?></option>
						*/?>
					</select>
					<div style="position: relative; display: inline-block;">
						<input type="text" class="magic-tag-enabled" name="filter[{{../_id}}][line][{{_id}}][value]" value="{{value}}" style="position: relative; top: 2px; width: 250px;">
					</div>
					<label title="<?php _e('When enabled, filter is ignored if the value is not available.', 'easy-pods'); ?>"><input type="checkbox" name="filter[{{../_id}}][line][{{_id}}][if_available]" value="1" {{#if if_available}}checked="checked"{{/if}}> <?php _e('Optional', 'easy-pods'); ?></label>
					<button type="button" class="button button-small" style="padding: 0px; margin: 5px 0px; float: right;" data-remove-parent=".easy-pods-filter-line"><span class="dashicons dashicons-no-alt" style="padding: 0px; margin: 0px; line-height: 23px;"></span></button>
				
				</div>


			{{/each}}


			<div class="clear"></div>
			<button type="button" class="wp-baldrick button button-small" style="position: absolute; bottom: 9px;" data-request="cep_get_default_setting" data-group="{{_id}}" data-script="filter-line"><?php _e('Add Filter Line', 'easy-pods'); ?></button>
		</div>

		<button type="button" class="button button-small" style="padding: 0px; margin: 3px 0px; position: absolute; left: 14px; bottom: 6px;" data-remove-parent=".easy-pods-filter-group"><span class="dashicons dashicons-no-alt" style="padding: 0px; margin: 0px; line-height: 23px;"></span></button>

	</div>
	{{/if}}
{{/each}}
</div>
{{#script}}
jQuery( function($){
$(".cep-select2").select2();
});
{{/script}}
