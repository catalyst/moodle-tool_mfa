define(['jquery'], function($) {
    return {
        init: function() {
            // Clear verification code field.
            $("#id_verificationcode").val('');
        }
    };
});
