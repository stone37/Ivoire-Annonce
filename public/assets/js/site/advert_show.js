$(document).ready(function () {
    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    /** Advert images lightBox */
    $('.skin-light .advert-view-image .mdb-lightbox .lightbox-plus').click(function () {
        $('#advert-img-plus').trigger('click');
    });

    // Envoie de message en ajax
    messageAjax($('#advert-message-form'));

    // Signalement
    reportAjax($('#advert-report-form'));
});

function messageAjax(element) {
    element.submit(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                if (data.success) {
                    let $errorContent = $('#advert-message-form-error');

                    $errorContent.html("").removeClass('mt-3');

                    $('textarea#content').val("")

                    notification("", data.message, {}, 'success')
                } else {
                    let $errors = $.parseJSON(data.errors), $errorContent = $('#advert-message-form-error');

                    $errorContent.html("").addClass('mt-3');

                    $($errors).each(function (key, value) {
                        $errorContent.append('<div class="small text-danger font-weight-stone-500">' + value + '</div>');
                    });

                    notification("Messagerie", "Erreur de validation, votre message n'a pas pu être envoyer", {}, 'error')
                }

                hideLoading();
            }
        })
    });
}

function reportAjax(element) {
    element.submit(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                if (data.success) {
                    $('#advert-report-modal').modal('hide')

                    let $radio = $('#advert-report-form input[type="radio"]'),
                        $errorContent = $('#advert-report-form-error');

                    $radio.val("")
                    $radio.prop('checked', false);

                    $('input#reportEmail').val("")
                    $('textarea#reportContent').val("")

                    $errorContent.html("").removeClass('my-2');

                    notification("", data.message, {}, 'success')
                } else {
                    let $errors = $.parseJSON(data.errors),
                        $errorContent = $('#advert-report-form-error');

                    $errorContent.html("").addClass('my-2');

                    $($errors).each(function (key, value) {
                        $errorContent.append('<div class="small text-danger font-weight-stone-500">' + value + '</div>');
                    });

                    notification("Messagerie", "Erreur de validation, votre signalement n'a pas pu être envoyer", {}, 'error')
                }

                hideLoading();
            }
        })
    });
}

