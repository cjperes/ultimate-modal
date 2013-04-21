jQuery(document).ready(function($) {

    var modal = $('.simplemodal');

    // Controls the display of the modal
    if ($.cookie('simplemodal') !== 'active') {
        modal.delay(300).fadeIn(700);

        $('#simplemodal, #simplemodal-close').click(function() {
            modal.stop().fadeOut(700);
        });
    }

    // Set the cookie of the modal.
    $.cookie('simplemodal', 'active', {
        path: '/',
        expires: parseInt(simplemodal_params.time, 10)
    });

});
