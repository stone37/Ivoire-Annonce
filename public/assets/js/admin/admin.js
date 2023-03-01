$(document).ready(function() {
    // Gestion des checkbox dans la liste
    let $principal_checkbox = $('#principal-checkbox'),
        $list_checkbook = $('.list-checkbook'),
        $list_checkbook_length = $list_checkbook.length,
        $list_checkbook_number = 0,
        $btn_bulk_delete = $('#entity-list-delete-bulk-btn'),
        $btn_class_bulk_delete = $('.entity-list-delete-bulk-btn');

    $principal_checkbox.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $('.list-checkbook').prop('checked', true);

            $list_checkbook_number = $list_checkbook_length;

            if ($list_checkbook_length > 0) {
                $btn_bulk_delete.removeClass('d-none');
                $btn_class_bulk_delete.removeClass('d-none');
            }

        } else {
            $('.list-checkbook').prop('checked', false);
            $btn_bulk_delete.addClass('d-none');
            $btn_class_bulk_delete.addClass('d-none');

            $list_checkbook_number = 0;
        }
    });

    $list_checkbook.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $list_checkbook_number++;
            $btn_bulk_delete.removeClass('d-none');
            $btn_class_bulk_delete.removeClass('d-none');

            if ($list_checkbook_number === $list_checkbook_length)
                $principal_checkbox.prop('checked', true)
        } else {
            $list_checkbook_number--;

            if ($list_checkbook_number === 0) {
                $btn_bulk_delete.addClass('d-none');
                $btn_class_bulk_delete.addClass('d-none');
            }

            if ($list_checkbook_number < $list_checkbook_length)
                $principal_checkbox.prop('checked', false)
        }
    });

    let $container = $('#modal-container'),
        $checkbook_container = $('#list-checkbook-container');

    // Administrateur
    simpleModals($('.entity-admin-delete'), 'app_admin_admin_delete', $container);
    bulkModals($('.entity-admin-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_admin_bulk_delete', $container);

    // Advert
    simpleModals($('.entity-advert-delete'), 'app_admin_advert_delete', $container);
    bulkModals($('.entity-advert-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_advert_bulk_delete', $container);

    simpleModals($('.entity-advert-validate'), 'app_admin_advert_validate', $container);
    bulkModals($('.entity-advert-validate-bulk-btn a.btn-success'), $checkbook_container, 'app_admin_advert_bulk_validate', $container);

    simpleModals($('.entity-advert-denied'), 'app_admin_advert_denied', $container);
    bulkModals($('.entity-advert-denied-bulk-btn a.btn-amber'), $checkbook_container, 'app_admin_advert_bulk_denied', $container);

    // Category
    simpleModals($('.entity-category-delete'), 'app_admin_category_delete', $container);
    bulkModals($('.entity-category-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_category_bulk_delete', $container);

    // Category premium
    simpleModals($('.entity-category-premium-delete'), 'app_admin_category_premium_delete', $container);
    bulkModals($('.entity-category-premium-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_category_premium_bulk_delete', $container);

    // City
    simpleModals($('.entity-city-delete'), 'app_admin_city_delete', $container);
    bulkModals($('.entity-city-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_city_bulk_delete', $container);

    // Credit
    simpleModals($('.entity-credit-delete'), 'app_admin_credit_delete', $container);
    bulkModals($('.entity-credit-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_credit_bulk_delete', $container);

    // Commande
    simpleModals($('.entity-order-delete'), 'app_admin_commande_delete', $container);
    bulkModals($('.entity-order-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_commande_bulk_delete', $container);

    // Currency
    simpleModals($('.entity-currency-delete'), 'app_admin_currency_delete', $container);
    bulkModals($('.entity-currency-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_currency_bulk_delete', $container);

    // Discount
    simpleModals($('.entity-discount-delete'), 'app_admin_discount_delete', $container);
    bulkModals($('.entity-discount-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_discount_bulk_delete', $container);

    // Emailing
    simpleModals($('.entity-emailing-delete'), 'app_admin_emailing_delete', $container);
    bulkModals($('.entity-emailing-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_emailing_bulk_delete', $container);

    // Exchange Rate
    simpleModals($('.entity-exchange-delete'), 'app_admin_exchange_rate_delete', $container);
    bulkModals($('.entity-exchange-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_exchange_rate_bulk_delete', $container);

    // Locale
    simpleModals($('.entity-locale-delete'), 'app_admin_locale_delete', $container);
    bulkModals($('.entity-locale-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_locale_bulk_delete', $container);

    // Option
    simpleModals($('.entity-option-delete'), 'app_admin_option_delete', $container);
    bulkModals($('.entity-option-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_option_bulk_delete', $container);

    // Pack
    simpleModals($('.entity-pack-delete'), 'app_admin_pack_delete', $container);
    bulkModals($('.entity-pack-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_pack_bulk_delete', $container);

    // Payment
    simpleModals($('.entity-payment-delete'), 'app_admin_payment_delete', $container);
    bulkModals($('.entity-payment-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_payment_bulk_delete', $container);

    // Report
    simpleModals($('.entity-report-delete'), 'app_admin_report_delete', $container);
    bulkModals($('.entity-report-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_report_bulk_delete', $container);


    // Review
    simpleModals($('.entity-review-delete'), 'app_admin_review_delete', $container);
    bulkModals($('.entity-review-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_review_bulk_delete', $container);

    // Suggestion
    simpleModals($('.entity-suggestion-delete'), 'app_admin_suggestion_delete', $container);
    bulkModals($('.entity-suggestion-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_suggestion_bulk_delete', $container);

    // User
    simpleModals($('.entity-user-delete'), 'app_admin_user_delete', $container);
    bulkModals($('.entity-user-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_user_bulk_delete', $container);

    // Notification
    if (window.hostel.USER) {
        setInterval(function(){
            getNotification($('#notification-bulk'), 'app_notification_unread');
        }, 180000);

        $('.skin-light .dropdown.notification').on('show.bs.dropdown', function () {
            readAll('app_notification_read');
        });
    }

    let $settings_radio = $('.settings-module-form .card .form-check.data');

    $settings_radio.click(function () {
        let $this = $(this);

        //$settings_radio.removeClass('active');

        $this.parents('.form-row.d-flex').find('.form-check.data').removeClass('active');

        $this.find('input[type="radio"]').prop('checked', true);
        $this.addClass('active');
    });
});

function getNotification(container, route) {
    $.ajax({
        url: Routing.generate(route),
        type: 'GET',
        success: function(data) {
            let $result = $.parseJSON(data);

            if ($result.length) {
                $('.skin-light .dropdown.notification .dropdown-menu .not-notification-bulk').addClass('d-none');
                $('.skin-light .dropdown.notification > .icon').removeClass('d-none')

                $.each($result, function(index, element) {
                    container.prepend(notificationItemView(element))
                });
            }
        },
    });
}

function readAll(route) {
    $.ajax({
        url: Routing.generate(route),
        type: 'GET',
        success: function() {
            $('.skin-light .dropdown.notification > .icon').addClass('d-none');
        },
    });
}

function notificationItemView(notification) {
    return $('<a class="dropdown-item d-flex" href="' + notification.url + '">' +
        '<div class="content">' +
        '<div class="data">' + notification.message + '</div>' +
        '<div class="time">' + jsDateFormater(new Date(notification.createdAt)) + '</div>' +
        '</div>' +
        '<div class="icon-notification ml-auto d-flex align-items-center pl-3"><i class="fas fa-circle"></i></div>' +
        '</a>');
}

function jsDateFormater(date) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        return `Il y a ${term.text.replace('%d', Math.round(seconds / term.divide))}`;
    } else {
        return `Dans ${term.text.replace('%d', Math.round(seconds / term.divide))}`;
    }
}

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





