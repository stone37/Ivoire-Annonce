$(document).ready(function() {
    // Navbar second
    const scrollBarWidths = 40;

    const getLeftPosition = function() {
        return $('.skin-light .navbar.second .navbar-nav').position().left;
    };

    const widthOfList = function () {
        let itemsWidth = 0;

        $('.skin-light .navbar.second .navbar-nav li').each(function () {
            const itemWidth = $(this).outerWidth();
            itemsWidth += itemWidth;
        });

        return itemsWidth;
    };

    const widthOfHidden = function () {
        return (($('.skin-light .navbar.second .navbar-collapse').outerWidth()) - widthOfList() - getLeftPosition()) - scrollBarWidths;
    };

    const reAdjust = function () {
        if (($('.skin-light .navbar.second .navbar-collapse').outerWidth()) < widthOfList()) {
            $('.skin-light .navbar.second .navbar-collapse .scroller-right').show();
        } else {
            $('.skin-light .navbar.second .navbar-collapse .scroller-right').hide();
        }

        if (getLeftPosition() < 0) {
            $('.skin-light .navbar.second .navbar-collapse .scroller-left').show();
        } else {
            //$('.item').animate({left: "-=" + getLeftPosition() + "px"}, 'slow');
            $('.skin-light .navbar.second .navbar-nav li').animate({left: "-=" + getLeftPosition() + "px"}, 'slow');
            $('.skin-light .navbar.second .navbar-collapse .scroller-left').hide();
        }
    };

    reAdjust();

    $(window).on('resize', function(){
        reAdjust();
    });

    $('.skin-light .navbar.second .navbar-collapse .scroller-right').click(function() {

        $('.skin-light .navbar.second .navbar-collapse .scroller-left').fadeIn('slow');
        $('.skin-light .navbar.second .navbar-collapse .scroller-right').fadeOut('slow');

        $('.skin-light .navbar.second .navbar-nav').animate({left: "+=" + widthOfHidden() + "px" }, 'slow', function(){

        });
    });

    $('.skin-light .navbar.second .navbar-collapse .scroller-left').click(function() {

        $('.skin-light .navbar.second .navbar-collapse .scroller-right').fadeIn('slow');
        $('.skin-light .navbar.second .navbar-collapse .scroller-left').fadeOut('slow');

        $('.skin-light .navbar.second .navbar-nav').animate({left: "-=" + getLeftPosition() + "px"}, 'slow', function(){

        });
    });

    // Mega menu
    let $mega_content = $('.skin-light .mega-menu-container .mega-content'),
        $mega_item = $('.skin-light .mega-menu-container .navbar.second .navbar-nav .nav-item.children');

    $mega_item.click(function (e) {
        e.preventDefault()

        let $this = $(this),
            $content = $this.find('div.content'),
            $url = $content.attr('data-url');

        $mega_item.removeClass('active');
        $this.addClass('active');

        $(document).click(function(e) {
            if (!$(e.target).hasClass('navbar-link')) {
                $mega_content.addClass('d-none');
                $mega_item.removeClass('active');
            }
        });

        $mega_content.html('');
        $mega_content.css({'background-image': $url, 'background-repeat': 'no-repeat', 'background-size': 'cover'});
        $mega_content.append($content.html())
        $mega_content.removeClass('d-none')
    });
});

