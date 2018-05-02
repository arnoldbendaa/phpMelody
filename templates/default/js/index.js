/**
 * Created by SteelBright on 5/2/2018.
 */
$(document).ready(function() {
    $("#register-form").validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            username: {
                required: true,
                minlength: 2
            },
            pass: {
                required: true,
                minlength: 5
            },
            confirm_pass: {
                required: true,
                minlength: 5,
                equalTo: "#register-form #pass"
            },
            imagetext: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            agree: "required"
        },
        messages: {
            name: {
                required: pm_lang.validate_name,
                minlength: pm_lang.validate_name_long
            },
            username: {
                required: pm_lang.validate_username,
                minlength: pm_lang.validate_username_long
            },
            pass: {
                required: pm_lang.validate_pass,
                minlength: pm_lang.validate_pass_long
            },
            confirm_pass: {
                required: pm_lang.validate_pass,
                minlength: pm_lang.validate_pass_long,
                equalTo: pm_lang.validate_confirm_pass_long
            },
            imagetext: {
                required: pm_lang.validate_captcha
            },
            email: pm_lang.validate_email,
            agree: pm_lang.validate_agree
        },
        errorClass: "error"
    });
});
