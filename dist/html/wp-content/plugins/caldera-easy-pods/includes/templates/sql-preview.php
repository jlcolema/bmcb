<?php
/**
 * The SQL view admin panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */
?>
<span class="wp-baldrick" id="easy-pods-sql-preview-trigger" data-format="{{format_sql_preview}}" {{#is _current_tab value="#easy-pods-sql-preview"}}data-autoload="true"{{/is}} data-event="click" data-load-element="#easy-pods-save-indicator-sql" data-callback="cep_render_preview_highlight" data-action="cep_query_preview" data-before="cep_get_config" data-target="#cep_preview_sql"></span> <label><input type="checkbox" id="toggle-sql-format" name="format_sql_preview" data-live-sync="true" value="1" {{#if format_sql_preview}}checked="checked"{{/if}}> <?php _e('Disable Pretty Format', 'easy-pods') ; ?></label>

<pre id="cep_preview_sql" class="language-php {{#if format_sql_preview}}no-format{{/if}}"></pre>
<span style="position: absolute; margin-left: 0px;" id="easy-pods-save-indicator-sql"><span style="float: none; margin: 16px 0px 0 10px;" class="spinner"></span></span>
