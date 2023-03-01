// Time js
const terms = [
    { time: 45, divide: 60, text: "moins d'une minute" },
    { time: 90, divide: 60, text: 'environ une minute' },
    { time: 45 * 60, divide: 60, text: '%d minutes' },
    { time: 90 * 60, divide: 60 * 60, text: 'environ une heure' },
    { time: 24 * 60 * 60, divide: 60 * 60, text: '%d heures' },
    { time: 42 * 60 * 60, divide: 24 * 60 * 60, text: 'environ un jour' },
    { time: 30 * 24 * 60 * 60, divide: 24 * 60 * 60, text: '%d jours' },
    { time: 45 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: 'environ un mois' },
    { time: 365 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: '%d mois' },
    { time: 365 * 1.5 * 24 * 60 * 60, divide: 24 * 60 * 60 * 365, text: 'environ un an' },
    { time: Infinity, divide: 24 * 60 * 60 * 365, text: '%d ans' }
];

let $dataTime = $('[data-time]');

$dataTime.each(function (index, element) {
    const timestamp = parseInt(element.getAttribute('data-time'), 10) * 1000;
    const date = new Date(timestamp);

    updateText(date, element, terms);
});

function updateText(date, element, terms) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;
    const prefix = element.getAttribute('prefix');

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        element.innerHTML = `${prefix || 'Il y a'} ${term.text.replace('%d', Math.round(seconds / term.divide))}`
    } else {
        element.innerHTML = `${prefix || 'Dans'} ${term.text.replace('%d', Math.round(Math.abs(seconds) / term.divide))}`
    }
}

function navbarModal(element, route, modalName, container) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: Routing.generate(route),
            type: 'GET',
            success: function(data) {
                hideLoading();

                $(container).html(data.html);
                $('#' + modalName).modal()
            }
        });
    });
}

function showLoading() {
    $('body .loader').show();
}

function hideLoading() {
    $('body .loader').hide();
}

function notification(titre, message, options, type) {
    if(typeof options == 'undefined') options = {};
    if(typeof type == 'undefined') type = "info";

    toastr[type](message, titre, options);
}

function passwordView(element) {
    if (element.hasClass('fa-eye')) {
        element.removeClass('fa-eye').addClass('fa-eye-slash view');

        element.siblings('input').get(0).type = 'text';
    } else {
        element.removeClass('fa-eye-slash view').addClass('fa-eye');

        element.siblings('input').get(0).type = 'password';
    }
}

function generatePassword($elements) {
    showLoading();

    let $request = new Request('https://api.motdepasse.xyz/create/?include_digits&include_lowercase&include_uppercase&password_length=8&quantity=1');

    fetch($request)
        .then((response) => response.json())
        .then(function(json_response) {
            json_response.passwords.forEach((password) => {
                $.each($elements, function(index, element){
                    $(element).val(password);
                })

                hideLoading();
            });
        });
}

function flashes(selector) {
    selector.each(function (index, element) {
        if ($(element).html() !== undefined) {
            toastr[$(element).attr('app-data')]($(element).html());
        }
    })
}

function newsletter(element) {
    element.submit(function (e) {
        showLoading();

        e.preventDefault();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                hideLoading();

                if (data.success) {
                    notification('Newsletter', data.message, {}, 'success')
                } else {
                    let errors = $.parseJSON(data.errors);

                    $(errors).each(function (key, value) {
                        notification('Newsletter', value, {}, 'error')
                    });
                }
            }
        })
    });
}

function simpleModals(element, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        let $id = $(this).attr('data-id'), $modal = '#confirm'+$id;

        $.ajax({
            url: Routing.generate(route, {id: $id}),
            type: 'GET',
            success: function(data) {
                hideLoading();

                $(elementRacine).html(data.html);
                $($modal).modal()
            }
        });
    });
}

function bulkModals(element, container, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        let ids = [];

        container.find('.list-checkbook').each(function () {
            if ($(this).prop('checked')) {
                ids.push($(this).val());
            }
        });

        if (ids.length) {
            let $modal = '#confirmMulti'+ids.length;

            $.ajax({
                url: Routing.generate(route),
                data: {'data': JSON.stringify(ids)},
                type: 'GET',
                success: function(data) {
                    hideLoading();

                    $(elementRacine).html(data.html);
                    $($modal).modal();
                },
            });
        }
    });
}








