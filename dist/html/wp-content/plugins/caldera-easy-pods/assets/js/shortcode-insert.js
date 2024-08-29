jQuery(function($){


	$('body').on('click', '#easy-pods-insert', function(e){
		e.preventDefault();
		var modal = $('.easy-pods-insert-modal'),
			clicked = $(this);

		if( clicked.data('selected') ){
			clicked.data('selected', null);
		}


		modal.fadeIn(100);

	});

	$('body').on('click', '.pod-query-modal-closer', function(e){
		e.preventDefault();
		var modal = $('.easy-pods-insert-modal');
		modal.fadeOut(100);		
	});

	$('body').on('click', '.easy-pods-shortcode-insert', function(e){
	 	
	 	e.preventDefault();
	 	var query = $('.selected-query-shortcode:checked'),code;

	 	if(!query.length){
	 		return;
	 	}

	 	if( !query.data('search') ){
		 	code = '[easy_pod name="' + query.val() + '"]';

		 	if( !query.data('template').length ){
		 		code += ' Custom Template [/easy_pod]';
		 	}
		 }else{
		 	code = '[caldera_form id="' + query.data('search') + '"]';
		 }

	 	query.prop('checked', false);	 	
		window.send_to_editor(code);
		$('.pod-query-modal-closer').trigger('click');

	});

	if( wp && wp.media ){

			var media = wp.media,
				views = wp.media.View;

			if( typeof views.register === "function"){
				views.register( 'easy_pod', {
					View: {
						template: media.template( 'editor-caldera-easy-pods' ),

						initialize: function( options ) {
							this.shortcode = options.shortcode;
							this.fetch();
							/*var editors = this.getEditors(),
								view = this;

							editors[0].on( 'mousedown mouseup click touchend', function( event ) {
								if ( ( event.type === 'touchend' || event.type === 'mousedown' ) && ! event.metaKey && ! event.ctrlKey ) {
									if ( editors[0].dom.hasClass( event.target, 'reload' ) ) {
										view.fetch();
										editors[0].focus();
										return false;
									}
								}

							}, true );*/


							var instance = this;
							setInterval( function(){
								instance.fetch();
							}, 20000 );
						},
						loadingPlaceholder: function() {
							return '' +
								'<div class="loading-placeholder" style="color:#a3be5f;">' +
									'<div class="dashicons dashicons-arrow-right-alt2"></div>' +
									'<div class="wpview-loading"><ins style="background-color:#a3be5f;"></ins></div>' +
								'</div>';
						},
						fetch: function() {
							options = {};
							options.context = this;
							options.data = {
								action:  'cep_editor_live_preview',
								post_id: $('#post_ID').val(),
								atts: this.shortcode.attrs
							};

							this.form = media.ajax( options );
							this.dfd = this.form.done( function(form) {
								this.form.data = form;
								this.render( true );
							} );
						},
						getHtml: function() {
							var attrs = this.shortcode.attrs.named,
								attachments = false,
								options;

							// Don't render errors while still fetching attachments
							if ( this.dfd && 'pending' === this.dfd.state() && ! this.form.length ) {
								return '';
							}

							return this.template( this.form.data );
						}
					},
					edit: function( node ) {
						var shortcode = $(node).find( '.cep-selected-shortcode' ).val();
						jQuery('#easy-pods-insert').trigger('click');
					}
				} );
			}
		}

});//
