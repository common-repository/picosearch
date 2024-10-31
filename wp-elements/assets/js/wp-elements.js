//Global wpElements

( function( $ ) {
	
	//Select boxes
	if ( typeof ( $.fn.selectize ) != "undefined" ) {
		$('select').selectize();
	}
	
	//Tooltips
	if ( typeof ( $.fn.tooltip ) != "undefined" ) {
		$('[data-toggle="tooltip"]').tooltip();
	}
	
	//Popovers too 
	if ( typeof ( $.fn.popover ) != "undefined" ) {
		$('[data-toggle="popover"]').popover();
	}
	
	//Date fields
	if ( typeof ( $.fn.datepicker ) != "undefined" ) {
		
		$.fn.wp_elements_datepicker = $.fn.datepicker.noConflict();
		$('.wpe-date-control').wp_elements_datepicker();
		
	}
	
	//Multitext fields
	$('.wp-elements-new-text-box:not(.dashicons-plus)').on('click', function(){
		$( this ).off().closest('.wp-elements-multi').remove();
	});
	
	$('.wp-elements-new-text-box:not(.dashicons-minus)').on('click', function(){
		
		var new_input = $( this ).closest('.wp-elements-multi').clone();
		var container = $( this ).closest('.wp-elements-multi').parent();
		var add_btn = $( new_input ).find('.wp-elements-new-text-box');
		
		//Unset the id and value then replace dashicons-plus with dashicons-minus
		$( new_input ).find('input').attr('id', '').attr('value', '');
		$( add_btn ).removeClass('dashicons-plus')	
		.addClass('dashicons-minus')
		.on('click', function(){
			//This is a new element so we reattach the click handler
			$( this ).off().closest('.wp-elements-multi').remove();
		});
		
		$( container ).append( new_input );
		
	});
	
	//Colorpicker
	if ( typeof ( $.fn.wpColorPicker ) != "undefined" ) {
		
		$('.wpe-colorpick').wpColorPicker({
			change: function( event, ui ) {
				$( this ).closest( '.forminp' ).find( '.wpe-colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			clear: function() {
				$( this ).closest( '.forminp' ).find( '.wpe-colorpickpreview' ).css({ backgroundColor: '' });
			}
		});
		
	}
	
	//Export and imports
	$('.wpe-export-btn').on('click', function( e ){
		e.preventDefault();
		
		//Hide the import button and show the export button
		var parent = $( this ).closest('.forminp');
		$( parent ).find( '.wpe-import' ).addClass('d-none');
		$( parent ).find( '.wpe-export' ).removeClass('d-none');
	});
	
	var log_feedaback = function ( el, data, type ) {
		return el.removeClass( 'alert-danger alert-warning alert-info' )
				 .addClass('alert-' + type )
				 .html( data )
				 .show();
	}
	
	$('.wpe-import-btn').on('click', function( e ){
		e.preventDefault();
		
		//Imported data cant be empty; should be true JSON
		var form = $( this ).closest('form');
		var parent = $( this ).closest('.forminp');
		$( parent ).find( '.wpe-export' ).addClass('d-none');
		$( parent ).find( '.wpe-import' ).removeClass('d-none');
		$( parent ).find( '.wpe-finish-import-btn' ).off().on('click', function( e ){
			
			e.preventDefault();
			var importdata = $( this ).closest( '.wpe-import' ).find( 'textarea' ).val();
			var feedaback_el = $( this ).closest( '.wpe-import' ).find( '.wpe-import-feedback' );
			feedaback_el.hide();
			
			if( importdata.length == 0 ){
				log_feedaback(feedaback_el, wpElements.emptyData, 'danger');
				return;
			}
			
			try {
				
				_importdata = $.parseJSON( importdata );
				
				log_feedaback(feedaback_el, wpElements.importing, 'info');

				if (_.isEmpty( _importdata )) {
					//Was valid json but still empty
					log_feedaback(feedaback_el, wpElements.emptyJson, 'warning');
					return;
				}
				
				log_feedaback(feedaback_el, wpElements.finalising, 'info');
				$( form ).append('<textarea name="wpe-importer-data" class="d-none">' + importdata + '</textarea>').submit();
				log_feedaback(feedaback_el, wpElements.finished, 'info');
				
			} catch ( error ) { console.log( error );
				//Invalid JSON 
				log_feedaback(feedaback_el, wpElements.badFormat, 'danger');
				return;				
			}
		});
	});

} )( jQuery );