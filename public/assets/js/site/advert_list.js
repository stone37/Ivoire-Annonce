$(document).ready(function () {
    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    let $brand_select = $('select.advert-brand-field'),
        $model_select = $('select.advert-model-field'),
        $brand_title = $('<option>').attr({value: '', selected: 'selected'}).text('Marque');


    switch (window.advert.CATEGORY_SLUG) {
        case 'voitures':
            $brand_select.empty().html(" ");
            $brand_select.append($brand_title);

            $.each($auto_brand, function(i, obj) {
                let $content;

                if (window.advert.MARQUE === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $brand_select.append($content);
            });

            $brand_select.materialSelect();

            if (window.advert.MARQUE) {
                let $model_title = $('<option>').attr({value: '', selected: 'selected'}).text('Modèle');

                $model_select.empty().html(" ");
                $model_select.append($model_title);

                $.each($auto_model[window.advert.MARQUE], function(i, obj) {
                    let $content;

                    if (window.advert.MODEL === obj) {
                        $content = $('<option selected>').attr({value: obj}).text(obj);
                    } else {
                        $content = $('<option>').attr({value: obj}).text(obj);
                    }

                    $model_select.append($content);
                });

                $model_select.materialSelect();
            }

            break;
        case 'locations-de-vehicules':
            $brand_select.empty().html(" ");
            $brand_select.append($brand_title);

            $.each($auto_brand, function(i, obj) {
                let $content;

                if (window.advert.MARQUE === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $brand_select.append($content);
            });

            $brand_select.materialSelect();

            if (window.advert.MARQUE) {
                let $model_title = $('<option>').attr({value: '', selected: 'selected'}).text('Modèle');

                $model_select.empty().html(" ");
                $model_select.append($model_title);

                $.each($auto_model[window.advert.MARQUE], function(i, obj) {
                    let $content;

                    if (window.advert.MODEL === obj) {
                        $content = $('<option selected>').attr({value: obj}).text(obj);
                    } else {
                        $content = $('<option>').attr({value: obj}).text(obj);
                    }

                    $model_select.append($content);
                });

                $model_select.materialSelect();
            }

            break;
        case 'motos-et-scooters':
            $brand_select.empty().html(" ");
            $brand_select.append($brand_title);

            $.each($moto_brand, function(i, obj) {
                let $content;

                if (window.advert.MARQUE === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $brand_select.append($content);
            });

            $brand_select.materialSelect();

            break;
        case 'bateaux-et-vehicules-marins':
            $brand_select.empty().html(" ");
            $brand_select.append($brand_title);

            $.each($bateau_brand, function(i, obj) {
                let $content;

                if (window.advert.MARQUE === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $brand_select.append($content);
            });

            $brand_select.materialSelect();

            break;
    }

    $brand_select.on('change', function() {
        let $this = $(this);

        if ($this.val()) {
            if (window.advert.CATEGORY_SLUG === 'voitures' ||
                window.advert.CATEGORY_SLUG === 'locations-de-vehicules') {
                let $model_title = $('<option>').attr({value: '', selected: 'selected'}).text('Modèle');

                $model_select.empty().html(" ");
                $model_select.append($model_title);

                $.each($auto_model[$this.val()], function(i, obj) {
                    let $content = $('<option>').attr({value: obj}).text(obj);

                    $model_select.append($content);
                });

                $model_select.materialSelect();
            }
        }
    });

    let $marque_select = $('select.brand-field');

    switch (window.advert.CATEGORY_SLUG) {
        case 'telephones-mobiles-et-smartphones':
            $marque_select.empty().html(" ");
            $marque_select.append($brand_title);

            $.each($phone_brand, function(i, obj) {
                let $content;

                if (window.advert.BRAND === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $marque_select.append($content);
            });

            $marque_select.materialSelect();

            break;
        case 'tablettes':
            $marque_select.empty().html(" ");
            $marque_select.append($brand_title);

            $.each($tablette_brand, function(i, obj) {
                let $content;

                if (window.advert.BRAND === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $marque_select.append($content);
            });

            $marque_select.materialSelect();

            break;
        case 'ordinateurs-portables':
            $marque_select.empty().html(" ");
            $marque_select.append($brand_title);

            $.each($ordinateur_portable_brand, function(i, obj) {
                let $content;

                if (window.advert.BRAND === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $marque_select.append($content);
            });

            $marque_select.materialSelect();

            break;
        case 'ordinateurs-de-bureau':
            $marque_select.empty().html(" ");
            $marque_select.append($brand_title);

            $.each($ordinateur_bureau_brand, function(i, obj) {
                let $content;

                if (window.advert.BRAND === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $marque_select.append($content);
            });

            $marque_select.materialSelect();
            break;

        case 'jeux-video-et-consoles':
            $marque_select.empty().html(" ");
            $marque_select.append($brand_title);

            $.each($jeux_video_brand , function(i, obj) {
                let $content;

                if (window.advert.BRAND === obj) {
                    $content = $('<option selected>').attr({value: obj}).text(obj);
                } else {
                    $content = $('<option>').attr({value: obj}).text(obj);
                }

                $marque_select.append($content);
            });

            $marque_select.materialSelect();
            break;
    }

    $('.advert-alert-btn').click(function(e){
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('connected')) {
            if (!$this.hasClass('activated')) {
                $.ajax({
                    url: Routing.generate('app_alert_create', {
                        category: $(this).attr('data-category'),
                        subCategory: $(this).attr('data-sub-category')
                    }),
                    type: 'GET',
                    success: function(data) {
                        if (data.success) {
                            //$this.addClass('disabled');
                            $('.advert-alert-btn').addClass('disabled');

                            notification('', data.message, {}, 'success')
                        } else {
                            notification('', data.message, {}, 'error')
                        }
                    }
                });
            } else {
                let message = 'Vous avez deja crée une alerte dans cette catégorie.';
                notification('', message, {'timeOut': '10000', 'closeButton': true}, 'info');
            }
        } else {
            let message = 'Vous devez vous connecter avant de pouvoir créer une alerte.';
            notification('', message, {'timeOut': '10000', 'closeButton': true}, 'info');
        }
    });
});

