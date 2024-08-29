
<?php
/**
 * The main ui.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>
<div class="easy-pods-main-headercaldera">
		<h2>
		<span id="easy_pods-name-title">{{name}}</span> <span class="easy-pods-subline">{{slug}}</span>
		<span class="add-new-h2 wp-baldrick" data-action="cep_save_config" data-load-element="#easy-pods-save-indicator" data-before="cep_get_config_object" ><?php _e('Save Changes', 'easy-pods') ; ?></span>
		<span style="position: absolute; margin-left: -18px;" id="easy-pods-save-indicator"><span style="float: none; margin: 16px 0px -5px 10px;" class="spinner"></span></span>
	</h2>
		<ul class="easy-pods-header-tabs easy-pods-nav-tabs">
			<li class="{{#is _current_tab value="#easy-pods-panel-general"}}active {{/is}}easy-pods-nav-tab"><a href="#easy-pods-panel-general"><?php _e('General', 'easy-pods') ; ?></a></li>
				
		</ul>
	<span class="wp-baldrick" id="easy-pods-field-sync" data-event="refresh" data-target="#easy-pods-main-canvas" data-callback="cep_canvas_init" data-type="json" data-request="#easy-pods-live-config" data-template="#main-ui-template"></span>
</div>
<div class="easy-pods-sub-headercaldera">
	<ul class="easy-pods-sub-tabs easy-pods-nav-tabs">
		<li class="{{#is _current_tab value="#easy-pods-panel-query"}}active {{/is}}easy-pods-nav-tab"><a href="#easy-pods-panel-query"><?php _e('Query Builder', 'easy-pods') ; ?></a></li>

		<li class="{{#is _current_tab value="#easy-pods-panel-search"}}active {{/is}}easy-pods-nav-tab"><a href="#easy-pods-panel-search"><?php _e('Search Form', 'easy-pods') ; ?></a></li>
		
		<li class="{{#is _current_tab value="#easy-pods-panel-advanced"}}active {{/is}}easy-pods-nav-tab"><a href="#easy-pods-panel-advanced"><?php _e('Advanced', 'easy-pods') ; ?></a></li>

		<li class="{{#is _current_tab value="#easy-pods-find-params"}}active {{/is}}easy-pods-nav-tab"><a data-trigger="#easy-pods-find-params-trigger" href="#easy-pods-find-params"><?php _e( 'Find Params', 'easy-pods') ; ?></a></li>

		<li class="{{#is _current_tab value="#easy-pods-sql-preview"}}active {{/is}}easy-pods-nav-tab"><a data-trigger="#easy-pods-sql-preview-trigger" href="#easy-pods-sql-preview"><?php _e('Effective SQL', 'easy-pods') ; ?></a></li>

		<?php
			/**
			 * Runs after the last admin tab link.
			 *
			 * Use to add additional tabs.
			 *
			 * @since 0.2.0
			 *
			 * @param array $easy_pods The array from Easy Pods registry.
			 */
			do_action( 'caldera_easy_pods_editor_tabs', $easy_pods );
		?>
	</ul>
</div>

<form id="easy-pods-main-form" action="?page=easy_pods" method="POST">
	<?php wp_nonce_field( 'easy-pods', 'easy-pods-setup' ); ?>
	<input type="hidden" value="{{id}}" name="id" id="easy_pods-id">
	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="easy-pods-active-tab">


	<div id="easy-pods-panel-general" class="easy-pods-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#easy-pods-panel-general"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4><?php _e( 'General Settings', 'easy-pods' ); ?> <small class="description"><?php _e( 'General', 'easy-pods' ); ?></small></h4>
		<?php

			// pull in the general settings template
			include CEP_PATH . 'includes/templates/general-settings.php';
		?>
	</div>
	
	<div id="easy-pods-panel-query" class="easy-pods-editor-panel" {{#is _current_tab value="#easy-pods-panel-query"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Add Query Filters', 'easy-pods') ; ?> <small class="description"><?php _e('Query Builder', 'easy-pods') ; ?></small></h4>
		<?php

			// pull in the query panel settings template
			include CEP_PATH . 'includes/templates/query-panel.php';
		?>
	</div>
	
	<div id="easy-pods-sql-preview" class="easy-pods-editor-panel" {{#is _current_tab value="#easy-pods-sql-preview"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Effective SQL query', 'easy-pods') ; ?> <small class="description"><?php _e('SQL', 'easy-pods') ; ?></small></h4>
		<?php

			// pull in the SQL preview template
			include CEP_PATH . 'includes/templates/sql-preview.php';
		?>
	</div>	

	<div id="easy-pods-panel-advanced" class="easy-pods-editor-panel" {{#is _current_tab value="#easy-pods-panel-advanced"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Additional Settings', 'easy-pods') ; ?> <small class="description"><?php _e('Advanced', 'easy-pods') ; ?></small></h4>
		<?php

			// pull in the advanced settings template
			include CEP_PATH . 'includes/templates/advanced-panel.php';
		?>
	</div>

	<div id="easy-pods-find-params" class="easy-pods-editor-panel" {{#is _current_tab value="#easy-pods-find-params"}}{{else}} style="display:none;" {{/is}}>
		<h4><?php _e( 'Pods Find Param Reference', 'easy-pods') ; ?> <small class="description"><?php _e( 'Find Params', 'easy-pods') ; ?></small></h4>
		<?php

		// pull in the find params template
		include CEP_PATH . 'includes/templates/find-params.php';
		?>
	</div>



	<div id="easy-pods-panel-search" class="easy-pods-editor-panel" {{#is _current_tab value="#easy-pods-panel-search"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Search Form', 'easy-pods') ; ?> <small class="description"><?php _e('Caldera Forms', 'easy-pods') ; ?></small></h4>
		<?php

			// pull in the search settings template
			include CEP_PATH . 'includes/templates/search-panel.php';
		?>
	</div>


	<?php
		/**
		 * Runs after the editor panel links.
		 *
		 * Use to add another sub-tab.
		 *
		 * @since 0.0.1
		 *
		 * @param array $easy_pods Easy Pods data from registry.
		 */
		do_action( 'caldera_easy_pods_editor_panels', $easy_pods );
	?>

</form>
<div class="clear"></div>

{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.easy-pods-nav-tab').first().find('a').trigger('click');
		});
	{{/script}}
{{/unless}}
