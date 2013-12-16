(function ( $ ) {
	'use strict';

	$(function () {

		// Color Picker.
		$( '.ultimate-modal-color-field' ).wpColorPicker();

		$( '#ultimate-modal-preview' ).on( 'click', function ( e ) {
			e.preventDefault();
			var background = $( 'input#background' ).val(),
				height = parseInt( $('input#height' ).val(), 10 ),
				width = parseInt( $('input#width' ).val(), 10 ),
				content = $('.mceFirst iframe' ).contents().find( '#tinymce' ).html(),
				modal;

			if ( 0 === background.length ) {
				background = '#000';
			}

			$( 'body' ).append( '<div id="ultimate-modal" class="ultimate-modal" style="background: ' + background + ';"></div>' );
			$( 'body' ).append( '<div id="ultimate-modal-content" class="ultimate-modal" style="width: ' + width + 'px; height: ' + height + 'px; margin: -' + (( height + 10 ) / 2 ) + 'px 0 0 -' + ( ( width + 10 ) / 2 ) + 'px;"><div id="ultimate-modal-close"></div>' + content + '</div>' );

			modal = $( '.ultimate-modal' );
			modal.delay(300).fadeIn(700);

			$( '#ultimate-modal, #ultimate-modal-close' ).click( function () {
				modal.stop().fadeOut( 700, function () {
					modal.remove();
				});
			});
		});

	});

}(jQuery));
