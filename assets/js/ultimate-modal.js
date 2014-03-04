/*global ultimatemodal_params: true */
(function ( $ ) {
	'use strict';

	$(function () {

		var modal = $( '.ultimate-modal' );

		// Controls the display of the modal
		if ( 'active' !== $.cookie( ultimatemodal_params.cookie_name ) ) {
			modal.delay( parseInt( ultimatemodal_params.delay, 10 ) ).fadeIn( 700 );

			$( '#ultimate-modal, #ultimate-modal-close' ).click( function () {
				modal.stop().fadeOut( 700 ).remove();
			} );
		}

		// Set the cookie of the modal.
		$.cookie( ultimatemodal_params.cookie_name, 'active', {
			path: '/',
			expires: parseInt( ultimatemodal_params.time, 10 )
		});

	});

}(jQuery));
