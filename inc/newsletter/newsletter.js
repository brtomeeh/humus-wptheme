(function($) {

    $(document).ready( function () {
        // I only have one form on the page but you can be more specific if need be.
        var $form = $('form.newsletter-signup');

        $msg = $('<div class="result-message" />');

        $form.append($msg);

        if ($form.length > 0) {
            $form.bind('submit', function() {
                register($form);
                return false;
            });
        }
    });

    function register($form) {
        $.ajax({
            type: $form.attr('method'),
            url: humus_newsletter.ajaxurl,
            data: $form.serialize(),
            cache       : false,
            dataType    : 'json',
            contentType: "application/json; charset=utf-8",
            error       : function(err) { alert("Could not connect to the registration server. Please try again later."); },
            success     : function(data) {
                console.log(data);
                if (data.result != "success") {
                    // Something went wrong, do something to notify the user. maybe alert(data.msg);
                } else {
                    // It worked, carry on...
                }
            }
        });
    }

})(jQuery);
