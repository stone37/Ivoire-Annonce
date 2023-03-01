$(document).ready(function() {
    // Photo de profil
    $('input.entity-image').change(function () {readURL(this)});

    let $container = $('#modal-container');

    // Suppression des annonces
    simpleModals($('.user-advert-delete'), 'app_user_advert_delete', $container);

    // Suppression des alertes
    simpleModals($('.advert-alert-delete'), 'app_user_alert_delete', $container);

    // Envoie de message
    messageAjax($('#advert-message-form'));

    // Suppression des messages
    simpleModals($('.advert-thread-delete'), 'app_user_thread_delete', $container);

    // Gestion des credits
    let $credit_item = $('.credit-list .credit-item'),
        $input_credit = $('input#form_credit'),
        $submit_btn = $('button.credit-submit');

    $credit_item.click(function () {
        let $this = $(this), $input = $this.find('input.with-gap');
        $credit_item.removeClass('active');

        $input.prop('checked', true);

        $this.addClass('active');
        $input_credit.val($input.val());
        $submit_btn.removeAttr('disabled');
    });

    // Gestion des parrainages

    $('#parrainage-btn-generate').click(function(e) {
        e.preventDefault();

        $('#parrainage-gestion').removeClass('d-none');
        $(this).addClass('disabled');

        notification('', 'Un lien parrainage a été générer', {}, 'success');
    });

    $('#parrainage-btn-link-copy').click(function(e) {
        e.preventDefault();

        navigator.clipboard.writeText(window.advert.PARRAINAGE_LINK);

        notification('', 'Le lien a été copier', {}, 'success');
    });
});

function readURL(input) {
    let url = input.value;
    let ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

    if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let $img = $(input).parents('.image-bulk-container').find('img.image-view');
            $img.attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0])
    }
}

function messageAjax(element) {
    element.submit(function (e) {
        e.preventDefault();

        showLoading();

        let $content = $('#content').val();

        console.log($content);

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {

                console.log(data);

                if (data.success) {
                    let errorContent = $('#advert-message-form-error');

                    errorContent.html("").removeClass("mt-3");

                    $('textarea#content').val("")

                    $('.message-list').append(messageData($content));

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

function messageData(content) {
    fecha.setGlobalDateI18n({
        dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
    });

    return $('<div class="d-flex justify-content-end message-bulk mt-2">' +
        '<div class="text-left message-data">' +
        '   <div class="message">' + content + '</div>' +
        '</div>' +
        '</div>' +
        ' <div class="d-flex justify-content-end message-info message-data">' +
        fecha.format(new Date(), 'ddd. DD MMM YYYY . HH:mm')
        + '</div>');
}
