var grid_modal_form_handler, grid_modal_form_sync, grid_get_item_data,grid_field_form_check;

jQuery(function($){

	var dragstate = 0;
	
	grid_field_form_check = function(obj){

		if( !$(obj).closest('form').formJSON() ){
			return false;
		}
		return true;
	}
	
	grid_get_item_data = function(obj){
		if( obj.trigger.data('data') ){
			return JSON.parse( $(obj.trigger.data('data')).val() );
		}
		return {};
	}

	grid_modal_form_handler = function( obj ){
		if( obj.target.is('input') ){
			return obj.trigger.closest('form').formJSON();
		}

		var id = 'item_' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766),
			object = {
				id			:	id,
				fragment	:	obj.target.prop('id'),
				data		: 	obj.trigger.closest('form').formJSON()
			};
			
		return object;
	}

	grid_modal_form_sync = function( obj ){
		update_grid_input( obj.params.trigger.data('panel') );
	}

	function init_sorter(){
		$( ".caldera-grid" ).sortable({
			handle    : ".column-body",
			axis    : "y",
			update : function(e, ui){
				update_grid_input( $(ui.item).closest('.caldera-grid').data('panel') );
			}
		});
		$( ".column-body" ).sortable({
			appendTo	: document.body,
			helper: "clone",
			forceHelperSize: true,
			forcePlaceholderSize: true,
			connectWith : ".column-body",
			update : function(e, ui){
				update_grid_input( $(ui.item).closest('.caldera-grid').data('panel') );
			}
		});
	}

	function update_grid_input( panel ){
		var input     = $('#' + panel + '-input'),
			input_json  = $('#' + panel + '-input-json'),
			rows    = [],
			row_obj = [];

		$('.' + panel + '-grid .row').each( function(){

			var row 	= [],
				columns = {};
			$(this).children().each( function(){

				var size 	= 	this.className.split('-')[2],
					id = 'rcol_' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766),
					column	=	{
						id		:	id,
						size	:	size,
						item	:	{}
					};
				
				row.push( size );
				// get nodes
				$(this).find('.column-body input').each( function(){
					var value = this.value;
					try {
					  value = JSON.parse( value );
					} catch (e) {
					  //console.log( e );
					}					
					column.item[$(this).prop('id')] = value;
				
				} );
				columns[id] = column;
			});
			rows.push( row.join(':') );
			row_obj.push( columns );
		});

		input.val( rows.join('|') ).trigger('change');
		input_json.val( JSON.stringify( row_obj ) ).trigger('change');
		init_sorter();
	}

	$(document).on('click', '.dashicons-no', function(e){
		var clicked = $(this),
				parent = clicked.closest('.caldera-grid');

		if( parent.data('confirm').length ){
			if( !confirm( parent.data('confirm') ) ){
				return;
			}
		}
		clicked.closest('.row').remove();
		update_grid_input( parent.data('panel') );

	});

	$(document).on('click', '.dashicons-plus-alt, .element-item-edit', function(e){
		var clicked = $(this),
			parent = clicked.closest('.caldera-grid'),
			modal_trigger = parent.find('.insert-item'),
			grid_slug = parent.data('panel'),
			buttons = {
				'data-panel'			: grid_slug,
				'data-request'			: 'grid_modal_form_handler',
				'data-modal-autoclose'	: grid_slug,
				'data-callback'			: 'grid_modal_form_sync',
				'data-before'			: 'grid_field_form_check'
			},
			button_text;
		// clicked if data -append
		if( clicked.data('data') ){
			modal_trigger.data('data', '#' + clicked.data('data'));
			buttons['data-target'] 			= '#element_' + clicked.data('data'),
			buttons['data-template'] 		= '#' + grid_slug + '_item';
			buttons['data-target-insert'] 	= 'replace';
			button_text = modal_trigger.data('saveText');
		}else{
			modal_trigger.data('data', null);
			buttons['data-target'] 			= '#' + clicked.data('fragment'),
			buttons['data-template'] 		= '#' + grid_slug + '_item';
			buttons['data-target-insert'] 	= 'append';
			button_text = modal_trigger.data('insertText');
		}
		modal_trigger.data('modalButtons', button_text + '|' + JSON.stringify(buttons) ).trigger('click');

	});

	$(document).on('click', '.add-grid-row', function(e){
		var clicked = $(this),
			id = 'rcol_' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766);

		$('.' + clicked.data('panel') + '-grid').append('<div class="row"><div class="col-xs-12"><div class="column-resize-handle"><span class="dashicons dashicons-minus"></span></div><div class="row-toolbar"><span class="dashicons dashicons-plus"></span><span class="dashicons dashicons-no"></span></div><div class="column-body" id="' + id + '"></div><div class="row-toolbar row-column-action"><span class="dashicons dashicons-plus-alt" data-fragment="' + id + '"></span></div></div></div>');
		update_grid_input( clicked.data('panel') );

	});

	$(document).on('click', '.dashicons-plus', function(e){
		var clicked = $(this),
			panel	= clicked.closest('.caldera-grid').data('panel'),
			parent = $(this).parent().parent();
			prev = parent.prev(),
			id = 'rcol_' + Math.round(Math.random() * 99887766) + '' + Math.round(Math.random() * 99887766),
			parentSize = parent[0].className.split('-'),
			newparent = Math.ceil( parseInt( parentSize[parentSize.length-1] ) / 2 ),
			newcol = Math.floor( parseInt( parentSize[parentSize.length-1] ) / 2 ),
			newinsert = $('<div class="col-xs-' + newcol + '"><div class="column-resize-handle "><span class="dashicons dashicons-minus"></span></div><div class="row-toolbar"><span class="dashicons dashicons-plus"></span></div><div class="column-body" id="' + id + '"></div><div class="row-toolbar row-column-action"><span class="dashicons dashicons-plus-alt" data-fragment="' + id + '"></span></div></div>');

			if( newcol > 0){
				newinsert.insertAfter(parent);
				parent[0].className = 'col-xs-' + newparent;
			}
			
			update_grid_input( panel );
			
	});

	$(document).on('click', '.dashicons-minus', function(e){
		var clicked = $(this),
			parent = clicked.parent().parent(),
			panel	= clicked.closest('.caldera-grid').data('panel'),
			prev = parent.prev(),
			parentSize = parent[0].className.split('-'),
			prevSize = prev[0].className.split('-'),
			from  = parent.find('.column-body').contents();

		prev.find('.column-body').append( from );
		parent.remove();

		prev[0].className = 'col-xs-' + ( parseInt( parentSize[parentSize.length-1] ) + parseInt( prevSize[prevSize.length-1] ) );

		update_grid_input( panel );

	});
	$(document).on('mousedown', '.column-resize-handle', function(e){
		if( $( e.target ).hasClass('dashicons') ){
			return;
		}
		var parent = $(this).parent(),
			prev = parent.prev(),
			parentSize = parent[0].className.split('-'),
			prevSize = prev[0].className.split('-'),
			right = {
				"span"      : parseInt( parentSize[parentSize.length-1] ),
				"partsize"    : parent.outerWidth() / parseInt( parentSize[parentSize.length-1] ),
				"start"     : e.clientX
			};

		dragstate = parent.data('grid', right)[0];
		prev.data('span', parseInt( prevSize[prevSize.length-1] ));

		document.body.focus();
		return false;
	});
	$(document).on('mouseup', function(e){
		if( dragstate ){

			var column = $(dragstate),
				panel = column.closest('.caldera-grid').data('panel'),
				grid = column.data('grid'),
				handle = column.find('.column-resize-handle');

			handle.find('.dashicons').show();
			handle.css( {'position':'','left': '' } );

			dragstate = 0;

			update_grid_input( panel );
		}
	});

	// handle moveing
	$(document).on('mousemove', function(e){
		if( dragstate ){

			var column    = $(dragstate),
				grid    = column.data('grid'),
				handle    = column.find('.column-resize-handle'),        
				previous  = column.prev(),
				prevsize  = previous.data('span'),
				shift   = Math.round(( grid.start - e.clientX )  /  grid.partsize);

			handle.find('.dashicons').hide();

			if( prevsize-shift > 0 && grid.span+shift > 0 ){
				//handle.css('left', e.clientX - start );
				if( e.clientX - grid.start > 6 ){
					handle.css( {'position':'fixed', 'left': e.clientX } );
				}else if( e.clientX - grid.start < 0 ){         
					handle.css( {'position':'fixed', 'left': e.clientX } );
				}

			}
			
			// sec colomns
			left = prevsize-shift;
			right = grid.span+shift;

			if( left > 0 && right > 0 ){
				previous[0].className = 'col-xs-' + left;
				column[0].className = 'col-xs-' + right;
			}

		}

	});

	// lock modals to nope!
	$(document).on('submit','.baldrick-modal-wrap', function(e){
		e.preventDefault();
		$( e.target ).find('.baldrick-modal-footer > button').trigger('click');
	});

	$(document).on('record_change', function(e){

		$('.caldera-grid').each(function(){
			update_grid_input( $(this).data('panel') );
		});
		
	});
	$(document).on('canvas_init', function(e){		
		
		init_sorter();
		$('.wp-modals').baldrick();	
	});

	
	$('.wp-modals').baldrick();
	init_sorter();


});
