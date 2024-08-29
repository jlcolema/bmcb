<?php
/**
 * The layout editor admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

?>

<button class="button add-grid-row" data-panel="layout" type="button"><?php _e( 'Add Row', 'easy-pods' ); ?></button>
<div class="caldera-grid layout-grid" data-panel="layout" data-confirm="<?php echo esc_attr( __( 'Remove this row?', 'easy-pods' ) ); ?>">
<span class="insert-item wp-modals" data-save-text="<?php _e( 'Update Display Field', 'easy-pods' ); ?>" data-insert-text="<?php _e( 'Insert Display Field', 'easy-pods' ); ?>" data-modal-width="600" data-modal-height="400" data-request="grid_get_item_data" data-template="#layout_modal_form" data-modal="layout" data-modal-title="<?php echo esc_attr( __( 'Display Field', 'easy-pods' ) ); ?>" data-panel="layout"></span>
	{{#each layout_json}}
	<div class="row layout-grid-row">
		{{#each this}}	
		<div class="col-xs-{{size}}">
			<div class="column-resize-handle layout-resize">
				<span class="dashicons dashicons-minus layout-minus"></span>			
			</div>
			<div class="row-toolbar">
				<span class="dashicons dashicons-plus layout-plus"></span>
				<span class="dashicons dashicons-no layout-no" data-confirm="<?php echo esc_attr( __( 'Remove this row?', 'easy-pods' ) ); ?>"></span>
			</div>
			<div class="column-body" id="{{id}}">
				{{#each item}}

				

				<span class="node-wrapper" style="min-height: 52px; display: block; width: 100%; padding-left: 20px; position: relative; background-color: rgb(255, 255, 255); box-shadow: 20px 0px 0px rgb(248, 248, 248) inset;" id="element_{{@key}}">
					<div class="easy-pods-card-content" style="min-height: 52px;">
						<input id="{{@key}}" type="hidden" value="{{json this}}">
						{{{code}}}



					</div>
					<span data-data="{{@key}}" data-fragment="{{../id}}" class="element-item-edit" style="padding: 0px; margin: 3px 0px; position: absolute; left: 1px; top: -2px;"><span class="dashicons dashicons-admin-generic" style="padding: 0px; margin: 0px; line-height: 23px; font-size:13px;"></span></span>

					<span style="padding: 0px; margin: 3px 0px; position: absolute; left: 0px; bottom: -2px;" data-remove-parent=".node-wrapper"><span class="dashicons dashicons-no-alt" style="padding: 0px; margin: 0px; line-height: 23px; font-size:13px;"></span></span>
				</span>


				{{/each}}
			</div>
			<div class="row-toolbar row-column-action"><span class="dashicons dashicons-plus-alt" data-fragment="{{id}}"></span></div>
		</div>
		{{/each}}
	</div>
	{{/each}}

</div>
<input type="hidden" id="layout-input" name="layout" value="{{json layout}}">
<input type="hidden" id="layout-input-json" name="layout_json" value="{{json layout_json}}">


{{#each pod_fields}}
<span class="easy-pods-autocomplete-out-entry-mustache" data-slug="{{this}}" data-label="{{this}}"></span>
{{/each}}

{{#script type="text/html" id="layout_modal_form"}}

		<textarea id="layout_field_code" data-mode="mustache" name="code">\{{code}}</textarea>
		<!-- Custom fields and stuff here -->
		\{{#script}}
		jQuery(function($){
			setTimeout( function(){
				var this_editor = cep_init_editor('layout_field_code');
				this_editor.refresh();
				this_editor.focus();
				}, 1 );
		});

		\{{/script}}
{{/script}}
{{#script type="text/html" id="layout_item"}}

	<span class="node-wrapper" style="min-height: 52px; display: block; width: 100%; padding-left: 20px; position: relative; background-color: rgb(255, 255, 255); box-shadow: 20px 0px 0px rgb(248, 248, 248) inset;" id="element_\{{id}}">
		<span style="color:#a1a1a1;" class="dashicons easy-pods-card-icon"></span>
		<div class="easy-pods-card-content" style="min-height: 52px;">
			<input id="\{{id}}" type="hidden" value="\{{json data}}">
			\{{{data/code}}}

		</div>
		<span data-data="\{{id}}" data-fragment="\{{fragment}}" class="element-item-edit" style="padding: 0px; margin: 3px 0px; position: absolute; left: 1px; top: -2px;"><span class="dashicons dashicons-admin-generic" style="padding: 0px; margin: 0px; line-height: 23px; font-size:13px;"></span></span>

		<span style="padding: 0px; margin: 3px 0px; position: absolute; left: 0px; bottom: -2px;" data-remove-parent=".node-wrapper"><span class="dashicons dashicons-no-alt" style="padding: 0px; margin: 0px; line-height: 23px; font-size:13px;"></span></span>

	</span>

{{/script}}
