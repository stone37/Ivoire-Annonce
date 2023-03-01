$(document).ready(function() {
    let $booking_date_input = document.querySelector('#booking-form .booking-form-bulk .booking-form-item.booking-date input'),
        $booking_checkin_label = $('#booking-form .booking-form-bulk .booking-form-item.booking-date .data .checkin'),
        $booking_checkout_label = $('#booking-form .booking-form-bulk .booking-form-item.booking-date .data .checkout'),
        //$booking_booker_data = $('#booking-form .booking-form-bulk .booking-form-item.booker-data'),
        $booking_booker_data_btn = $('#booking-form .booking-form-bulk .booking-form-item.booker-data .dropdown-btn'),
        $booking_booker_dropdown_content = $('#booking-form .booking-form-item.booker-data .content-dropdown'),
        $booking_booker_adult_field = $('#booking-form .booking-booker-data .content-dropdown .content-inner .adults .data a'),
        $booking_booker_children_field = $('#booking-form .booking-booker-data .content-dropdown .content-inner .children a'),
        $booking_booker_room_field = $('#booking-form .booking-booker-data .content-dropdown .content-inner .room a'),
        $booking_booker_btn_content = $('#booking-form .booking-booker-data .dropdown-btn .data .date-content'),
        $booking_adult_field = $('input.booking_data_adult'),
        $booking_children_field = $('input.booking_data_children'),
        $booking_room_number_field = $('input.booking_data_roomNumber'),
        $adults = window.hostel.DEFAULT_ADULT,
        $children = window.hostel.DEFAULT_CHILDREN,
        $rooms = window.hostel.DEFAULT_ROOM,
       // $booking_location = $('#booking-form .booking-form-bulk .booking-form-item.location'),
        $booking_location_field = $('#booking-form .booking-form-bulk .booking-form-item.location input.booking_data_location'),
        $booking_location_mobile_search_field = $('#booking-form .booking-form-bulk .booking-form-item.location .content-dropdown input#mobile-booking-data-location-input'),
        $booking_location_field_icon_clear = $('#booking-form .booking-form-bulk .booking-form-item.location .input-with-pre-icon .input-prefix.fa-times '),
        $booking_location_dropdown_content = $('#booking-form .booking-form-bulk .booking-form-item.location .content-dropdown'),
        $booking_location_dropdown_content_item = $('#booking-form .booking-form-bulk .booking-form-item.location .content-dropdown ul li'),
        $booking_location_mobile_modal_close_btn = $('#booking-form .booking-form-bulk .booking-form-item.location .content-dropdown .mobile-bulk .icon i'),
        $booking_booker_data_mobile_modal_close_btn = $('#booking-form .booking-form-bulk .booking-form-item.booker-data .content-dropdown .mobile-bulk .footer a'),
        $booking_booker_data_mobile_modal_close_btn_icon = $('#booking-form .booking-form-bulk .booking-form-item.booker-data .content-dropdown .mobile-bulk .icon i')
    ;

    // Retire le curseur du champ de date
    bookingInputEvent($booking_date_input);

    // Gestion du champ date
    let $booking_date = new HotelDatepicker($booking_date_input, {
        format: 'YYYY-MM-D',
        showTopbar: true,
        moveBothMonths: true,
        maxNights: 30,
        selectForward: true,
        animationSpeed: '.1s',
        onDayClick: function() {
            $booking_checkin_label.text(fecha.format(new Date($booking_date.start), 'ddd. DD MMM.'));
        },
        onSelectRange: function() {
            $booking_checkin_label.text(fecha.format(new Date($booking_date.start), 'ddd. DD MMM.'));
            $booking_checkout_label.text(fecha.format(new Date($booking_date.end), 'ddd. DD MMM.'));
        },
        hoveringTooltip: function(nights, startTime, hoverTime) {
            return '';
        },
        onOpenDatepicker: function() {
            $booking_location_dropdown_content.addClass('d-none');
        },
        i18n: {
            selected: '',
            night: (window.hostel.locale === 'en') ? 'Night' : 'Nuitée',
            nights: (window.hostel.locale === 'en') ? 'Nights' : 'Nuitées',
            button: '<i class="fas fa-times"></i>',
            clearButton: 'Clear',
            submitButton: 'Submit',
            'checkin-disabled': 'Check-in disabled',
            'checkout-disabled': 'Check-out disabled',
            'day-names-short': getDayNamesShort(),
            'day-names': getDayNames(),
            'month-names-short': getMonthNamesShort(),
            'month-names': getMonthNames(),
            'error-more': 'Date range should not be more than 1 night',
            'error-more-plural': 'Date range should not be more than %d nights',
            'error-less': 'Date range should not be less than 1 night',
            'error-less-plural': 'Date range should not be less than %d nights',
            'info-more': '',
            'info-more-plural': '',
            'info-range': 'Please select a date range between %d and %d nights',
            'info-range-equal': 'Please select a date range of %d nights',
            'info-default': 'Please select a date range',
            'aria-application': 'Calendar',
            'aria-selected-checkin': 'Selected as check-in date, %s',
            'aria-selected-checkout': 'Selected as check-out date, %s',
            'aria-selected': 'Selected, %s',
            'aria-disabled': 'Not available, %s',
            'aria-choose-checkin': 'Choose %s as your check-in date',
            'aria-choose-checkout': 'Choose %s as your check-out date',
            'aria-prev-month': 'Move backward to switch to the previous month',
            'aria-next-month': 'Move forward to switch to the next month',
            'aria-close-button': 'Close the datepicker',
            'aria-clear-button': 'Clear the selected dates',
            'aria-submit-button': 'Submit the form'
        }
    });

    $booking_date_input.addEventListener("afterClose", function () {
        let data = $booking_date_input.value.split(" - ");

        $booking_checkin_label.text(fecha.format(new Date(data[0]), 'ddd DD MMM.'));
        $booking_checkout_label.text(fecha.format(new Date(data[1]), 'ddd DD MMM.'));
    }, false);


    // Gestion du champ utilisateur
    $booking_booker_data_btn.click(function (e) {
        /*$(document).click(function() {
            if (!$booking_booker_dropdown_content.hasClass('d-none')) {
                $booking_booker_dropdown_content.addClass('d-none');
            }
        });

        $booking_booker_dropdown_content.removeClass('d-none');

        $booking_location_dropdown_content.addClass('d-none');
        $booking_date.close();

        e.stopPropagation();*/

        e.stopPropagation();

        $(document).click(function(event) {
            if (!$(event.target).closest( "#navbarDropdownBookerData" ).length) {
                $booking_booker_dropdown_content.addClass('d-none');
            }
        });

        $booking_booker_dropdown_content.removeClass('d-none');
        $booking_location_dropdown_content.addClass('d-none');
        $booking_date.close();
    });

    $booking_booker_adult_field.click(function (e) {
        e.preventDefault();

        let $this = $(this),
            $customer = $booking_booker_btn_content.find('.customer');

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $adults--;

            if ($adults === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($adults);
            $customer.text(adultText($adults));
            $booking_adult_field.val($adults)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $adults++;

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($adults);
            $customer.text(adultText($adults));
            $booking_adult_field.val($adults)
        }
    });

    $booking_booker_children_field.click(function (e) {
        e.preventDefault();

        let $this = $(this),
            $child = $booking_booker_btn_content.find('.children');

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $children--;

            if ($children === 0) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($children)
            $child.text(childrenText($children));
            $booking_children_field.val($children);
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $children++;

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($children);
            $child.text(childrenText($children));
            $booking_children_field.val($children);
        }
    });

    $booking_booker_room_field.click(function (e) {
        e.preventDefault();

        let $this = $(this),
            $room = $booking_booker_btn_content.find('.room');

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $rooms--;

            if ($rooms === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($rooms);
            $room.text(roomText($rooms));
            $booking_room_number_field.val($rooms);
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $rooms++;

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($rooms);
            $room.text(roomText($rooms));
            $booking_room_number_field.val($rooms);
        }
    });

    // Gestion du champ localisation
    /*$booking_location.click(function(e) {

        $(document).click(function() {
            if (!$booking_location_dropdown_content.hasClass('d-none')) {
                $booking_location_dropdown_content.addClass('d-none');
            }
        });

        $booking_location_dropdown_content.toggleClass('d-none');

        $booking_booker_dropdown_content.addClass('d-none');
        $booking_date.close();

        e.stopPropagation();
    });*/


    $booking_location_field.click(function (e) {
        e.stopPropagation();

        $(document).click(function(event) {
            /*if (!$booking_location_dropdown_content.hasClass('d-none')) {
                $booking_location_dropdown_content.addClass('d-none');
            }*/

            //console.log(event.target)

            if (!$(event.target).closest( "#navbarDropdownLocation" ).length) {
                $booking_location_dropdown_content.addClass('d-none');

                /*console.log(event.target)
                console.log($(event.target).closest( "#navbarDropdownLocation" ).length)*/
            }

        });

        $booking_location_dropdown_content.toggleClass('d-none');
        $booking_booker_dropdown_content.addClass('d-none');
        $booking_date.close();
    });

    // Affiche les information dans le champ localisation
    /*$booking_location_dropdown_content_item.click(function() {
        $booking_location_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
        $booking_location_field_icon_clear.removeClass('d-none');
    });*/

    $booking_location_dropdown_content_item.click(function() {
        $booking_location_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
        $booking_location_mobile_search_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
        $booking_location_field_icon_clear.removeClass('d-none');
        $booking_location_dropdown_content.addClass('d-none');
    })


    let $container = $booking_location_dropdown_content.find('ul'),
        $container_title = $booking_location_dropdown_content.find('.title'),
        $default_data = $container.html();
    //  $booking_location_mobile_search_field

    $booking_location_field.add($booking_location_mobile_search_field).keyup(function (){
        let $q = $(this).val();

        $.ajax({
            type: 'POST',
            url: Routing.generate('app_city_search'),
            data: {'q': $q},
            success: function(data) {
                let $result = $.parseJSON(data);

                if ($result.length && ($booking_location_field.val() || $booking_location_mobile_search_field.val())) {
                    $container.html('');
                    $container_title.addClass('d-none');

                    $.each($result, function(index, element){
                        $container.append(cityItemView(element));
                    });

                    $booking_location_dropdown_content.removeClass('d-none');
                } else {
                    if ($booking_location_field.val() || $booking_location_mobile_search_field.val()) {
                        $booking_location_dropdown_content.addClass('d-none');
                    } else {
                        $container.html('');
                        $container.append($default_data);
                        $container_title.removeClass('d-none');
                        $booking_location_dropdown_content.removeClass('d-none');
                    }
                }

                $('li.content-item').click(function() {
                    $booking_location_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
                    $booking_location_mobile_search_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
                    $booking_location_field_icon_clear.removeClass('d-none');
                    $booking_location_dropdown_content.addClass('d-none');
                })
            }
        });
    });

    // Gestion du bouton de suppression don champ localisation
    $booking_location_field_icon_clear.click(function (e) {

        $(this).addClass('d-none');
        $booking_location_field.val('').focus();
        $booking_location_mobile_search_field.val('').focus();
        $booking_location_dropdown_content.removeClass('d-none');

        $container.html('');
        $container.append($default_data);
        $container_title.removeClass('d-none');

        $('li.content-item').click(function() {
            $booking_location_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
            $booking_location_mobile_search_field.val($(this).attr('data-city') + ', ' + countryName($(this).attr('data-country')));
            $booking_location_field_icon_clear.removeClass('d-none');
            $booking_location_dropdown_content.addClass('d-none');
        })

        e.stopPropagation();
    });

    // Ferme la fenêtre modal pour la localisation sur mobile
    $booking_location_mobile_modal_close_btn.click(function () {
        $booking_location_dropdown_content.addClass('d-none');
    });

    // Ferme la fenêtre modal pour les details de reservation sur mobile
    $booking_booker_data_mobile_modal_close_btn.add($booking_booker_data_mobile_modal_close_btn_icon).click(function (e){
        e.preventDefault();

        $booking_booker_dropdown_content.addClass('d-none');
    });

    // Filter collapse
    let $equipment_collapse = $('.hostel-list-filter .card .card-body .equipment-field .collapse'),
        $room_equipment_collapse = $('.hostel-list-filter .card .card-body .room-equipment-field .collapse'),
        $equipment_mobile_collapse = $('.hostel-list-mobile-filter .modal.hostel-mobile-filter-modal .modal-body .equipment-field .collapse'),
        $room_equipment_mobile_collapse = $('.hostel-list-mobile-filter .modal.hostel-mobile-filter-modal .modal-body .room-equipment-field .collapse');

    $equipment_collapse.on('hide.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher plus').blur();
    });

    $room_equipment_collapse.on('hide.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher plus').blur();
    });

    $equipment_mobile_collapse.on('hide.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher plus').blur();
    });

    $room_equipment_mobile_collapse.on('hide.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher plus').blur();
    });

    $equipment_collapse.on('show.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher moins').blur();
    });

    $room_equipment_collapse.on('show.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher moins').blur();
    });

    $equipment_mobile_collapse.on('show.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher moins').blur();
    });

    $room_equipment_mobile_collapse.on('show.bs.collapse', function() {
        let $this = $(this);
        $this.siblings('.collapse-link').find('a').text('Afficher moins').blur();
    });

    // Filter price
    $('#multi-filter-price').mdbRange({
        value: {
            min: window.hostel.PRICE_FILTER_MIN,
            max: window.hostel.PRICE_FILTER_MAX
        },
        single: {
            active: true,
            value: {
                step: window.hostel.PRICE_FILTER_STEP,
                symbol: ''
            },
            counting: true,
            countingTarget: '#price_min_price',
            bgThumbColor: '#7E22CE',
            textThumbColor: '#fff',
            multi: {
                active: true,
                value: {
                    step: window.hostel.PRICE_FILTER_STEP,
                    symbol: ''
                },
                rangeLength: 1,
                counting: true,
                countingTarget: ['#price_max_price'],
                bgThumbColor: '#7E22CE',
                textThumbColor: '#fff'
            }
        }
    });

   /* let $price_min_field = $('input#price_min_price'),
        $price_max_field = $('input#price_max_price'),
        $data = $('.multi-range-field input[type="range"]::-webkit-slider-thumb');

    $data.click(function() {
        alert(33)
    })

    console.log($price_min_field.val())

    $( "input[type='text']" ).change(function() {
        console.log($price_min_field.val())
    });*/

    /*$price_min_field.bind("change paste keyup", function() {
        console.log($(this).val());
    });*/

    /*$price_min_field.change(function() {
        console.log($(this).val())
    })*/

    /*$price_min_field.on("input", function() {
        alert($(this).val());
    });*/

    /** Hostel gallery lightBox */
    $('.skin-light .hostel-gallery .mdb-lightbox .lightbox-plus').click(function () {
        $('#hostel-img-plus').trigger('click');
    });

    // Gestion des favoris
    $('.hostel-favorite-btn').click(function(e){
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('connected')) {
            if ($this.hasClass('activated')) {
                $.ajax({
                    url: Routing.generate('app_favorite_delete', {id: $(this).attr('data-id')}),
                    type: 'GET',
                    success: function(data){

                        if (data.code === 200) {
                            $this.removeClass('activated');
                            $this.children('i').removeClass('fas').addClass('far');
                            notification('', data.message, {'timeOut': '10000', 'closeButton': true}, 'success');
                        } else {
                            notification('', data.message, {'timeOut': '10000', 'closeButton': true}, 'error');
                        }
                    }
                });
            } else {
                $.ajax({
                    url: Routing.generate('app_favorite_add', {id: $this.attr('data-id')}),
                    type: 'GET',
                    success: function(data) {

                        if (data.code === 200) {
                            $this.addClass('activated');
                            $this.children('i').removeClass('far').addClass('fas');
                            notification('', data.message, {'timeOut': '10000', 'closeButton': true}, 'success');
                        } else {
                            notification('', data.message, {'timeOut': '10000', 'closeButton': true}, 'error');
                        }
                    }
                });
            }
        } else {
            let message = 'Vous devez vous connecter avant de pouvoir ajouter un établissement à vos favoris';
            notification('', message, {'timeOut': '10000', 'closeButton': true}, 'info');
        }
    });

    let $booking_form_btn = $('.hostel-list.hostel-show .search-form-data'),
        $booking_form_close_btn = $('.hostel-list.hostel-show .search-form-close a'),
        $booking_form = $('.hostel-list.hostel-show .booking-form');

    $booking_form_btn.click(function (e) {
        e.preventDefault();

        $booking_form_btn.removeClass('d-block').addClass('d-none');
        $booking_form.addClass('active');
        $booking_form_close_btn.removeClass('d-none').addClass('d-block')
    })

    $booking_form_close_btn.click(function (e) {
        e.preventDefault();

        $booking_form_btn.removeClass('d-none').addClass('d-block');
        $booking_form.removeClass('active');
        $booking_form_close_btn.removeClass('d-block').addClass('d-none');
    })





});

function cityItemView(city) {
    return $('<li class="d-flex content-item" data-country="' + city.country + '" data-city="' + city.name + '">' +
        '            <div class="icon"><i class="fas fa-map-marker-alt"></i></div>' +
        '            <div class="data">' +
        '                <div class="city">' + city.name + '</div>' +
        '                <div class="country">' + countryName(city.country) + '</div>' +
        '            </div>' +
        '        </li>');
}

function countryName(code) {
    switch (code) {
        case 'CI':
            country = 'Côte d’Ivoire';
            break;
        default:
            country = 'Côte d’Ivoire';
    }

    return country;
}

function adultText(element) {
    return (element > 1) ? element + ' adultes' : element + ' adulte';
}

function childrenText(element) {
    return (element > 1) ? element + ' enfants' : element + ' enfant';
}

function roomText(element) {
    return (element > 1) ? element + ' chambres' : element + ' chambre';
}

function bookingInputEvent(input) {
    $(input).mousedown(function (e) {
        e.preventDefault;
        $(this).blur();
        return false;
    });
}

function getMonthNames() {
    let monthNamesLists;

    switch (window.hostel.LOCALE_CODE) {
        case 'en':
            monthNamesLists = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            break;
        default:
            monthNamesLists = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    }

    return monthNamesLists;
}

function getMonthNamesShort() {
    let monthNamesShortLists;

    switch (window.hostel.LOCALE_CODE) {
        case 'en':
            monthNamesShortLists = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            break;
        default:
            monthNamesShortLists = ['Janv', 'Févr', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
    }

    return monthNamesShortLists;
}

function getDayNames() {
    let dayNamesLists;

    switch (window.hostel.LOCALE_CODE) {
        case 'en':
            dayNamesLists = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            break;
        default:
            dayNamesLists = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    }

    return dayNamesLists;
}

function getDayNamesShort() {
    let dayNamesShort;

    switch (window.hostel.LOCALE_CODE) {
        case 'en':
            dayNamesShort = ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'];
            break;
        default:
            dayNamesShort = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
    }

    return dayNamesShort;
}

function getMonthsShort(month, locale) {
    let monthShortLists;

    switch (locale) {
        case 'en':
            monthShortLists = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
            break;
        default:
            monthShortLists = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];
    }

    return monthShortLists[month];
}




