jQuery(document).ready(function($) {
    $('form#requestForm').submit(function (e) {
        e.preventDefault();
        var $form = $(this),
            button = $('.btn-request-submit');
        $.ajax({
            type: 'POST',
            url: requestForm.ajaxUrl,
            data: {
                form: $form.serialize(),
                action: requestForm.action,
                security: requestForm.security
            },
            beforeSend : function ( xhr ) {
                button.text('Loading...');
            },
            success: function (response) {
                button.text('Send');
                console.log(response);
                $form[0].reset();
            },
            error: function (request, txtstatus, errorThrown) {
                console.log(request);
                console.log(txtstatus);
                console.log(errorThrown);
                button.text('Error');
                button.css('background-color','#ff0000');
                setTimeout(function () {
                    button.text('Send');
                    button.css('background-color','initial');
                },2000)
            }
        });
    });
});