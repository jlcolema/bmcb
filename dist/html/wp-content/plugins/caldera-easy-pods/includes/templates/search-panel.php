<?php
/**
 * The search admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */


if( defined( 'CFCORE_VER' ) ){ ?>
		<p class="description"><?php _e('Selecting a Caldera Form below will override the form\'s functionality and turn it into a search form using the Caldera Easy Pods query elements.', 'easy-pods'); ?></p><br>

		<div class="easy-pods-config-group">
			<label for="easy_pods-search_form"><?php _e('Search Form', 'easy-pods'); ?></label>
			<select id="easy_pods-search_form" data-load-element="#easy-pods-save-indicator" name="search_form" class="wp-baldrick cep-select2" data-action="cep_load_search_form_fields" data-script="set-search-fields" data-callback="cep_get_default_setting" data-type="json" data-event="change">
				<?php 

				$forms = cep_get_forms( true );
				if(empty($forms)){
					echo '<option value="">'.__("You don't have any Caldera Forms", 'easy-pods').'</option>';
				}else{
					echo '<option value="">' . __("Search Disabled", 'easy-pods') . '</option>';
					foreach( $forms as $form_id=>$form ){
						echo '<option value="'.$form_id.'" {{#is search_form value="'.$form_id.'"}}selected="selected"{{/is}}>'.$form['name'].'</option>';
					}
				}

				?>
			</select>
			<button type="button" class="wp-baldrick button" data-for="#easy_pods-search_form"><?php _e('Reload Fields', 'easy-pods'); ?></button> 
			<p class="description" style="margin-left: 185px;"><?php _e( "If you've made changes to the form and fields are not showing, click reload fields to fetch the changes", 'easy-pods' ); ?></p>

			<input type="hidden" name="form_fields" value="{{json form_fields}}">
	
		</div>
	<div {{#if search_form}}{{#is search_form value=""}}style="display:none;"{{/is}}{{else}}style="display:none;"{{/if}}>

		{{#is search_form value=""}}
		{{else}}
		<div class="easy-pods-config-group">
			<label for="easy_pods-template"><?php _e( 'Template', 'easy-pods' ); ?></label>
			<select data-live-sync="true" name="params[template]" id="easy_pods-template">
				<?php foreach ( $templates as $tmpl ) { ?>
				<option {{#is params/template value="<?php echo $tmpl[ 'name' ]; ?>"}}selected="selected"{{/is}} value="<?php echo $tmpl[ 'name' ]; ?>">
					<?php echo $tmpl[ 'name' ]; ?>
				</option>
				<?php } ?>
			</select>
			
		</div>
		{{/is}}

		<div class="easy-pods-config-group">
			<label><?php _e('Live Search', 'easy-pods'); ?></label>
			<label style="width: auto;" for="easy_pods-trigger_change"><input type="checkbox" name="trigger_change" value="1" id="easy_pods-trigger_change" {{#if trigger_change}}checked="checked"{{/if}}> <?php _e( 'Search while typing or change.', 'easy-pods' ); ?>
		</div>

		<div class="easy-pods-config-group">
			<label></label>
			<label style="width: auto;" for="easy_pods-init_search"><input type="checkbox" name="init_search" value="1" id="easy_pods-init_search" {{#if init_search}}checked="checked"{{/if}}> <?php _e( 'Run search on page load.', 'easy-pods' ); ?>
		</div>


		<div class="easy-pods-config-group">
			<label for="easy_pods-result_class"><?php _e( 'Results Class', 'easy-pods' ); ?></label>
			<input type="text" name="result_class" value="{{result_class}}" id="easy_pods-result_class">
			<p class="description" style="margin-left: 185px;"><?php _e( 'Custom CSS class names for results wrapper', 'easy-pods' ); ?></p>
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-output_selector"><?php _e( 'Output Target', 'easy-pods' ); ?></label>
			<input type="text" name="output_selector" value="{{output_selector}}" id="easy_pods-output_selector">
			<p class="description" style="margin-left: 185px;"><?php _e( 'Custom jQuery selector or Javascript Function name for search results placement.', 'easy-pods' ); ?></p>
		</div>
		<hr>
		<h4><?php _e('Form Fields', 'easy-pods'); ?> <small class="description"><?php _e('Search Form', 'easy-pods'); ?></small></h4>
		<div id="form-field-reference-description">
			<p class="description"><?php _e( 'Use these tags in the query builder to use the result of the from field to be the value for that part of the query.', 'easy-pods' ); ?></p>
			<p class="description"><?php _e( 'The name of the tag corresponds to the name of the field in your Caldera Form. To add a new tag, simply add a new field to your Caldera Form.', 'easy-pods' ); ?></p>

		</div>
		<br>
		{{#each form_fields}}

		<div class="easy-pods-config-group">
			<label>&lcub;search:{{slug}}&rcub;</label>
			
			{{#if option}}<label style="display: inline-block; width: auto; margin: 6px;"><input type="checkbox" name="auto_populate_enable[{{slug}}]" value="1" data-live-sync="true" {{#find ../../auto_populate_enable slug}}checked="checked"{{/find}}> {{#find ../../auto_populate_enable slug}}<?php _e('Override field options with results from', 'easy-pods'); ?>{{else}}<?php _e('Override field options', 'easy-pods'); ?>{{/find}}</label>
			
			<select{{#find ../../auto_populate_enable slug}}{{else}} style="display:none;"{{/find}} name="auto_populate_fields[{{slug}}]" data-live-sync="true">
				<?php
					$easy_podss = cep_get_registry();
					$easy_pods_binds = array();
					if( !empty( $easy_podss ) ){
					?>
					<optgroup label="<?php _e('Easy Pod', 'easy-pods'); ?>">
					<?php
						foreach( $easy_podss as $easy_pods_populate){
							if( !empty( $easy_pods_populate['search_form'] ) || $easy_pods_populate['id'] == $easy_pods['id'] ){
								continue;
							}
							?>
							<option value="<?php echo $easy_pods_populate['slug']; ?>" {{#find ../../auto_populate_fields slug}}{{#is this value="<?php echo $easy_pods_populate['slug']; ?>"}}selected="selected"{{/is}}{{/find}}><?php echo $easy_pods_populate['name']; ?></option>
							<?php

							// add to field binds 
							$query = cep_get_easy_pod( $easy_pods_populate['id'] );
							$easy_pods_binds[$easy_pods_populate['slug']] = $query['pod_fields']['fields'];

						}
					?></optgroup>
					<?php
					}
				?>
			</select> <p class="description" {{#find ../../auto_populate_enable slug}}style="display:inline;"{{else}} style="display:none;"{{/find}}>

			{{#find ../../auto_populate_fields slug}}
			<?php foreach( $easy_pods_binds as $easy_pod_name=>$easy_pod_fields){ ?>					
				{{#is this value="<?php echo $easy_pod_name; ?>"}}					
					<?php _e( 'with the value from: ', 'easy-pods' ); ?>
					<select name="auto_populate_values[{{../../slug}}]">
						<?php foreach( $easy_pod_fields as $field ){ ?>
							<option value="<?php echo $field; ?>" {{#find ../../../../auto_populate_values ../../../slug}} {{#is this value="<?php echo $field; ?>"}}selected="selected"{{/is}} {{/find}}><?php echo $field; ?></option>
						<?php } ?>
					</select> 
				{{/is}}					
				{{#is this value="<?php echo $easy_pod_name; ?>"}}
					<?php _e( 'and the label from: ', 'easy-pods' ); ?>
					<select name="auto_populate_labels[{{../../slug}}]">
						<?php foreach( $easy_pod_fields as $field ){ ?>
							<option value="<?php echo $field; ?>" {{#find ../../../../auto_populate_labels ../../../slug}} {{#is this value="<?php echo $field; ?>"}}selected="selected"{{/is}} {{/find}}><?php echo $field; ?></option>
						<?php } ?>
					</select>
				{{/is}}	


			<?php } ?>
			{{/find}}
			</p>

			{{else}}
			<?php _e('Input Field', 'easy-pods'); ?>
			{{/if}}
		</div>
		{{/each}}
		<br>
		<p class="description"><?php _e( 'For fields that have preset options, such as dropdowns and checkbox fields, the options can be set in one of three ways. The options may be manually set in the Caldera Forms editor, or they can be automatically populated with all items in a post type, or taxonomy using the Caldera Forms Editor. You may also select another Caldera Easy Pod here to use the results of that query as the options.', 'easy-pods' ); ?></p>
		
	</div>
<?php }else{ ?>

<?php esc_html_e( 'Search requires the free plugin Caldera Forms.', 'easy-pods' ); ?>
<?php echo '&nbsp;<a href="https://wordpress.org/plugins/caldera-forms/" target="_blank">' . esc_html__( 'Vew Details.', 'easy-pods' ) .'</a>'; ?>

<?php } ?>
