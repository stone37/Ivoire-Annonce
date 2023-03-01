$(document).ready(function () {

    $('.advert-favorite-btn').click(function(e){
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
            let message = 'Vous devez vous connecter avant de pouvoir ajouter une annonce à vos favoris';
            notification('', message, {'timeOut': '10000', 'closeButton': true}, 'info');
        }
    });

});

