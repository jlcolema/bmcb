<?php
/**
 * Find parameters panel.
 *
 * @package   Caldera_Easy_Pods
 * @author    Josh Pollock <Josh@JoshPress.net
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC <http://CalderaWP.com>
 */
?>
<span class="wp-baldrick" id="easy-pods-find-params-trigger" {{#is _current_tab value="#easy-pods-find-params"}}data-autoload="true"{{/is}} data-load-element="#easy-pods-save-indicator-params" data-event="click" data-callback="cep_render_preview_highlight" data-action="cep_find_params" data-before="cep_get_config" data-target="#cep-find-params"></span>

<pre id="cep-find-params" class="language-php"></pre>
<span style="position: absolute; margin-left: 0px;" id="easy-pods-save-indicator-params"><span style="float: none; margin: 16px 0px 0 10px;" class="spinner"></span></span>
<div class="clear"></div>
