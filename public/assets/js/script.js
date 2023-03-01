$(document).ready(function() {
    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
        template: '<div class="tooltip md-tooltip"><div class="tooltip-arrow md-arrow"></div><div class="tooltip-inner md-inner"></div></div>'
    });

    // Dropdown
    let $dropdown = $('.dropdown');

    $dropdown.on('hide.bs.dropdown', function() {
        $(this).removeClass('active')
        $(this).find('.fas.fa-angle-down').removeClass('rotate-180');
    });

    $dropdown.on('shown.bs.dropdown', function() {
        $(this).addClass('active')
        $(this).find('.fas.fa-angle-down').addClass('rotate-180');
    });

    // Bouton top scroll
    $(window).scroll(function() {
        let scroll = $(window).scrollTop();
        let currScrollTop = $(this).scrollTop();

        if (scroll >= 200)
            $('#btn-smooth-scroll').removeClass('d-none').addClass('animated fadeInRight');
        else
            $('#btn-smooth-scroll').addClass('d-none').removeClass('animated fadeInRight');

        lastScrollTop = currScrollTop;
    });

    $(window).scroll(function() {
        let scroll = $(window).scrollTop();
        let currScrollTop = $(this).scrollTop();

        if (scroll >= 300) {
            $('#btn-floating-filter').removeClass('d-none').addClass('animated fadeInRight');
            $('#btn-floating-alert').removeClass('d-none').addClass('animated fadeInRight');
        } else {
            $('#btn-floating-filter').addClass('d-none').removeClass('animated fadeInRight');
            $('#btn-floating-alert').addClass('d-none').removeClass('animated fadeInRight');
        }

        lastScrollTop = currScrollTop;
    });

    // Newsletter
    newsletter($('#newsletter-form'));

    // Password view
    $('.input-prefix.fa-eye').click(function () {
        passwordView($(this));
    });

    // Global search
    let $navbarSearch = $('.advert-search-request-form .advert-search-form-item .advert_search_data'),
        $suggestionSearch = $('.advert-search-request-form .advert-search-form-item .content-dropdown');

    $navbarSearch.keyup(function () {
        let $q = $(this).val();

        $(document).click(function() {
            $suggestionSearch.addClass('d-none');
        })

        $.ajax({
            type: 'POST',
            url: Routing.generate('app_advert_search_by_query'),
            data: {'q': $q},
            success: function (data) {
                let $result = $.parseJSON(data);

                if ($result.length && $navbarSearch.val()) {
                    let $container = $('<ul class="list-unstyled pb-0 mb-0"></ul>');

                    $.each($result, function(index, element){
                        let $content = $('<li class="item-data"><a href="' + element.route + '" class="">' + element.title + '</a></li>')
                        $container.append($content);
                    });

                    $suggestionSearch.html('');
                    $suggestionSearch.append($container);
                    $suggestionSearch.removeClass('d-none');

                    $('.item-data a').click(function () {
                        let $this = $(this);
                        $navbarSearch.val($this.text());
                        $suggestionSearch.addClass('d-none');
                    });
                } else {
                    $suggestionSearch.html('');
                    $suggestionSearch.addClass('d-none');
                }
            }
        });
    });

    // Navbar mobile
    let $icon_bulk = $('.skin-light .navbar .navbar-toggler .button-icon');

    $('.skin-light .navbar .navbar-toggler').on('click', function () {

        if ($icon_bulk.hasClass('open')) {
            $icon_bulk.find('i').removeClass('fa-times').addClass('fa-bars');
            $('html, body').removeClass('stop-scroll');
        } else {
            $icon_bulk.find('i').removeClass('fa-bars').addClass('fa-times');
            $('html, body').addClass('stop-scroll');
        }

        $('.skin-light .navbar .navbar-toggler .button-icon').toggleClass('open');
    });

















    // Carousel
    $('.carousel .carousel-inner.vv-3 .carousel-item').each(function () {
        var next = $(this).next();

        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < 4; i++) {

            next = next.next();

            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }

        $('.carousel').carousel('cycle');
    });



    $container = $('#modal-container');

    // Locale modal
    navbarModal($('#navbar-locale-menu-link'), 'app_locale_get', 'navbar-locale-menu-modal', $container);

    // Currency modal
    navbarModal($('#navbar-currency-menu-link'), 'app_currency_get', 'navbar-currency-menu-modal', $container);

    // Ferme la bannière du haut
    $('.skin-light .alert.banner .close').click(function() {
        $.ajax({
            type: 'POST',
            url: Routing.generate('app_switch_banner'),
            data: {'id': $(this).attr('data-id')},
            success: function(data) {
                $(this).hide();
            }
        });
    });

    // Notification
    if (window.advert.USER) {
        setInterval(function(){
            getNotification($('#notification-bulk'), 'app_notification_unread');
        }, 180000);

        $('.skin-light .dropdown.notification').on('show.bs.dropdown', function () {
            readAll('app_notification_read');
        });

        notificationModal($('#navbar-notification-menu-link'), 'app_notifications_get', 'navbar-notification-menu-modal', $container);
    }

    // Booking Cancellation
    let $booking_cancellation_btn = $('.booking-cancellation-btn'),
        $booking_user_cancellation_btn = $('.booking-user-cancellation-btn');

    $booking_cancellation_btn.click(function (e) {
        e.preventDefault();

        showLoading();

        let $id = $(this).attr('data-id'), $modal = '#modalBookingCancelledForm' + $id;

        $.ajax({
            url: Routing.generate('app_booking_cancelled', {id: $id}),
            type: 'GET',
            success: function(data) {
                hideLoading();

                $($container).html(data.html);
                $($modal).modal()
            }
        });
    });

    $booking_user_cancellation_btn.click(function (e) {
        e.preventDefault();

        showLoading();

        let $id = $(this).attr('data-id'), $modal = '#modalBookingUserCancelledForm' + $id;

        $.ajax({
            url: Routing.generate('app_user_booking_cancelled', {id: $id}),
            type: 'GET',
            success: function(data) {
                hideLoading();

                console.log(data.html)

                $($container).html(data.html);
                $($modal).modal()
            }
        });
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
                $('#navbar-notification-menu-link i.fa-circle').removeClass('d-none');

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
            $('#navbar-notification-menu-link i.fa-circle').addClass('d-none');
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

function notificationModal(element, route, modalName, container) {
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

                readAll('app_notification_read');
            }
        });
    });
}


