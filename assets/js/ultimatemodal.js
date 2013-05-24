/*global ultimatemodal_params: true */
jQuery(document).ready(function($) {

    var modal = $('.ultimatemodal');

    // Controls the display of the modal
    if ($.cookie('ultimatemodal') !== 'active') {
        modal.delay(300).fadeIn(700);

        $('#ultimatemodal, #ultimatemodal-close').click(function() {
            modal.stop().fadeOut(700);
        });
    }

    // Set the cookie of the modal.
    $.cookie('ultimatemodal', 'active', {
        path: '/',
        expires: parseInt(ultimatemodal_params.time, 10)
    });

});
