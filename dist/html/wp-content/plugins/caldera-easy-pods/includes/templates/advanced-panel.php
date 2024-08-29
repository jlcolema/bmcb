<?php
/**
 * The advanced options admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>

		<div class="easy-pods-config-group">
			<label for="easy_pods-limit"><?php _e( 'Limit', 'easy-pods' ); ?></label>
			<input type="text" name="params[limit]" value="{{params/limit}}" id="easy_pods-limit">
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-pagination"><?php _e( 'Paginate', 'easy-pods' ); ?></label>

			<select name="params[pagination]" id="easy_pods-pagination" style="width: 191px;">
				<option {{#is params/pagination value=""}}selected="selected" {{/is}}value=""><?php _e( 'No Pagination', 'easy-pods' ); ?></option>
				<?php /*<option {{#is params/pagination value="paginate"}}selected="selected" {{/is}}value="paginate"><?php _e( 'Paginate', 'easy-pods' ); ?></option> */ ?>
				<option {{#is params/pagination value="simple"}}selected="selected" {{/is}}value="simple"><?php _e( 'Simple', 'easy-pods' ); ?></option>
				<option {{#is params/pagination value="advanced"}}selected="selected" {{/is}}value="advanced"><?php _e( 'Advanced', 'easy-pods' ); ?></option>
			</select>
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-paginate_load_class"><?php _e( 'Paginate Load Class', 'easy-pods' ); ?></label>
			<input style="width: 115px;" type="text" name="paginate_load_class" value="{{#if paginate_load_class}}{{paginate_load_class}}{{else}}loading{{/if}}" id="easy_pods-paginate_load_class">
			<p class="description" style="margin-left: 185px;"><?php _e( 'CSS Class name to apply to template wrapper on pagination.', 'easy-pods' ); ?></p>
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-no_results"><?php _e( 'No Results Text', 'easy-pods' ); ?></label>
			<input type="text" class="magic-tag-enabled regular-text" name="no_results" value="{{no_results}}" id="easy_pods-no_results">
		</div>


		<div class="easy-pods-config-group">
			<label for="easy_pods-orderby"><?php _e( 'Order By', 'easy-pods' ); ?></label>
			<select name="params[orderby][field]" id="easy_pods-orderby" class="cep-select2" style="min-width: 350px">
			<option></option>
				<optgroup label="<?php _e( 'Fields', 'easy-pods' ); ?>">
				{{#each pod_fields/fields}}				
						<option value="{{this}}" {{#is ../params/orderby/field value=this}}selected="selected"{{/is}}>{{this}}</option>				
				{{/each}}
				</optgroup>
			</select>
			<select name="params[orderby][direct]" id="easy_pods-direct">
			<option {{#is params/orderby/direct value="ASC"}}selected="selected" {{/is}}value="ASC"><?php _e( 'Ascending', 'easy-pods' ); ?></option>
			<option {{#is params/orderby/direct value="DESC"}}selected="selected" {{/is}}value="DESC"><?php _e( 'Descending', 'easy-pods' ); ?></option>
			</select>

		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-cache_mode"><?php _e( 'Cache Type', 'easy-pods' ); ?></label>

			<select name="params[cache_mode]" id="easy_pods-cache_mode" style="width: 191px;">
				<option {{#is params/cache_mode value="none"}}selected="selected" {{/is}}value="none"><?php _e( 'Disable Caching', 'easy-pods' ); ?></option>
				<option {{#is params/cache_mode value="cache"}}selected="selected" {{/is}}value="cache"><?php _e( 'Object Cache', 'easy-pods' ); ?></option>
				<option {{#is params/cache_mode value="transient"}}selected="selected" {{/is}}value="transient"><?php _e( 'Transient', 'easy-pods' ); ?></option>
				<option {{#is params/cache_mode value="site-transient"}}selected="selected" {{/is}}value="site-transient"><?php _e( 'Site Transient', 'easy-pods' ); ?></option>
			</select>
		</div>

		<div class="easy-pods-config-group">
			<label for="easy_pods-expires"><?php _e( 'Cache Expiration', 'easy-pods' ); ?></label>
			<input style="width: 115px;" type="text" name="params[expires]" value="{{params/expires}}" id="easy_pods-expires"> <?php _e( 'Seconds', 'easy-pods' ); ?>
		</div>
