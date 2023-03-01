$(document).ready(function() {
    // SideNav Button Initialization
    $('.button-collapse').sideNav({
        edge: 'left',
        closeOnClick: false,
        breakpoint: 1440,
        menuWidth: 270,
        timeDurationOpen: 300,
        timeDurationClose: 200,
        timeDurationOverlayOpen: 50,
        timeDurationOverlayClose: 200,
        easingOpen: 'easeOutQuad',
        easingClose: 'easeOutCubic',
        showOverlay: true,
        showCloseButton: false
    });

    // SideNav Scrollbar Initialization
    var sideNavScrollbar = document.querySelector('.custom-scrollbar');
    var ps = new PerfectScrollbar(sideNavScrollbar);

    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
        template: '<div class="tooltip md-tooltip"><div class="tooltip-arrow md-arrow"></div><div class="tooltip-inner md-inner"></div></div>'
    });

    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    //  Notification
    let options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "md-toast-top-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1500",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('.toast').toast(options)

    // Gestion des notification serveur
    flashes($('.notify'));

    // Animation init
    new WOW().init();
});
