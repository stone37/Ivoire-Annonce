$(document).ready(function() {
    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    // Auto brand and model
    let $advert_auto_brand_select = $('select.advert-auto-brand'),
        $advert_auto_model_select = $('select.advert-auto-model');

    $advert_auto_brand_select.on('change', function() {
        let $this = $(this);

        if ($this.val()) {
            let $model_title = $('<option>').attr({value: '', selected: 'selected'}).text('Modèle'),
                $models = $auto_model[$this.val()];

            $advert_auto_model_select.empty().html('');
            $advert_auto_model_select.append($model_title);

            $.each($models, function(i, obj) {
                let $content = $('<option>').attr({value: obj}).text(obj);

                $advert_auto_model_select.append($content);
            });

            $advert_auto_model_select.materialSelect();
        }
    });

    // Radio
    let $radio_bulk = $('.radio-bulk-parent .radio-bulk');

    $radio_bulk.click(function () {
        let $this = $(this),
            $input = $this.find('.form-check-input[type="radio"]'),
            $radio_inputs = $this.parents('.radio-bulk-parent').find('.form-check-input[type="radio"]');

        if (!$input.prop('checked')) {
            $radio_inputs.prop('checked', false);
            $this.siblings('.radio-bulk').removeClass('active');

            $input.prop('checked', true);
            $this.addClass('active');
        }
    });

    // Upload file
    let $upload_btn = $('#btn-photos'),
        $upload_container = $('#advert-image'),
        $ordPhoto = [],
        $body = $('body');

    $upload_btn.click(function (e) {
        e.preventDefault();
        $('input[type="file"].input-photo').trigger('click');
    });

    $upload_container.dmUploader({
        allowedTypes: 'image/*',
        maxFileSize: 8388608, // 8 Megs max
        extFilter: ['jpg', 'jpeg','png','gif'],
        url: Routing.generate('app_image_upload_add'),
        onFallbackMode: function () {},
        onDragEnter: function () {
            this.addClass('active');
        },
        onDragLeave: function () {
            this.removeClass('active');
        },
        onInit: function () {
            //console.log('Callback: Plugin initialized');
        },
        onNewFile: function (id, file) {

            if (typeof FileReader !== "undefined"){
                let reader = new FileReader();
                reader.onload = function (e) {ajoutPhotoLoaded({id: id, src: e.target.result});};
                reader.readAsDataURL(file);
            }

            // Compteur nbPhoto
            $ordPhoto.push(id);

            ajoutPhoto({id: id, name: file.name, size: file.size, uploaded: false});
        },
        onUploadProgress: function(id, percent) {
            $('#' + id + ' .progresslabel').html("Envoi " + percent + '%');
            $('#' + id + ' circle').css({"stroke-dasharray": percent + " 100"});
        },
        onUploadSuccess: function(id, data) {
            $('#' + id).addClass('ok');
            $('#' + id + ' .progresslabel').html("Envoi terminé");
            $('#' + id + ' .progress').addClass('hide');
            $('#' + id + ' .remove').show().addClass('show');

            ajoutPhotoLoaded({id: id, src: data.url});

            if ($ordPhoto[0]===id) changePrincipale(id);
        },
        onUploadError: function(id) {
            $('#' + id).addClass('error');
            $('#' + id + ' .progresslabel').html("Envoi échoué");
            $('#' + id + ' .progress').hide();
            $('#' + id + ' .remove').show().addClass('show');
        },
        onFileSizeError: function(file) {
            console.error('File \'' + file.name + '\' cannot be added: size excess limit');
            notification('Fichier refusé', 'L\'image est trop volumineuse (supérieur a 8Mo).', {'timeOut': '8000', 'closeButton': true}, 'error');
        },
        onFileTypeError: function(file) {
            console.error('File \'' + file.name + '\' cannot be added: must be an image (type error)');
            notification('Fichier refusé', 'Le type du fichier n\'est pas supporté.', {'timeOut': '8000', 'closeButton': true}, 'error');
        },
        onFileExtError: function(file) {
            console.error('File \'' + file.name + '\' cannot be added: must be an image (extension error)');
            notification('Fichier refusé', 'L\'extension du fichier n\'est pas supporté.', {'timeOut': '8000', 'closeButton': true}, 'error');
        }
    });

    $body.on('click', 'button.remove', function(e) {
        e.preventDefault();

        uploadRemove($(this).attr('data-id'), $ordPhoto);
    });

    $body.on('click', 'button.progress', function(e) {
        e.preventDefault();

        uploadCancel($(this).attr('data-id'));
    });

    $body.on('click', 'div.img-principale', function(e) {
        e.preventDefault();

        changePrincipale($(this).attr('data-id'));
    });


    function ajoutPhoto(file) {
        let filesize = getReadableFileSizeString(file.size);

        let $html = '<div class="col-lg-2 col-md-4 col-6 scale-up-ver-top my-md-3 mb-2 ' +
            (($ordPhoto[0] === file.id) ? 'principale' : '' ) + '" id="' + file.id + '">';

        $html +='	<img src="' + file.src + '" class="img-fluid" alt="Image">';
        $html +='	    <div class="info small font-weight-stone-500">';
        $html +='	        <span>' + file.name + '</span>';
        $html +='	        <span>' + filesize + '</span>';
        $html +='	        <span class="progresslabel">Envoi ' + ((file.uploaded) ? 'terminé' : '0%') + '</span>';
        $html +='	    </div>';
        $html +='	    <div class="action">';
        $html +='		    <button class="progress ' + ((file.uploaded) ? 'hide' : '') + '" data-id="' + file.id + '"  title="Annuler"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><circle r="16" cx="16" cy="16"></circle></svg></button>';
        $html +='		    <button class="remove ' + ((file.uploaded) ? 'show' : '') + '" data-id="' + file.id + '" title="Supprimer"><svg viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"></path></svg></button>';
        $html +='	    </div>';
        $html +='	    <div class="img-principale" data-id="' + file.id + '">';
        $html +='		    Photo principale';
        $html +='	    </div>';
        $html +='	</div>';

        $('#img-upload-list').append($html);
    }

    function ajoutPhotoLoaded(file) {
        $('#'+file.id+' img').attr('src', file.src);
    }

    function getReadableFileSizeString(fileSizeInBytes) {
        let i = -1, byteUnits = [' Ko', ' Mo', ' Go', ' To', 'Po', 'Eo', 'Zo', 'Yo'];

        do {
            fileSizeInBytes = fileSizeInBytes / 1024;
            i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 1).toFixed(0) + byteUnits[i];
    }

    function uploadRemove(id, ordPhoto) {
        showLoading();

        let pos = ordPhoto.indexOf(id);
        ordPhoto.splice(pos, 1);

        if ($('#' + id + '').hasClass('error')) {
            $('#' + id).addClass('fade-out-bck');
            setTimeout(function(){ $('#' + id).remove(); }, 400);

            hideLoading();

            return;
        }

        $.ajax({
            url: Routing.generate('app_image_upload_delete', {'pos': pos}),
            success: function() {
                $('#' + id).addClass('fade-out-bck');
                setTimeout(function() {$('#' + id).remove(); }, 400);

                if (pos === 0) {
                    $('.img-upload-list > div:eq(1)').addClass('principale');
                }

                hideLoading();
            }
        });
    }

    function uploadCancel(id) {
        $upload_container.dmUploader("cancel", id);

        $('#' + id).remove();

        return false;
    }

    function changePrincipale(id) {
        showLoading();

        const el = $('#' + id);
        if (el.hasClass('disabled')) return;

        let pos = $ordPhoto.indexOf(id);

        $.ajax({
            url: Routing.generate('app_image_upload_principale', {'pos': pos}),
            success: function() {
                $('.img-upload-list > div').removeClass('principale');
                el.addClass('principale');

                hideLoading();
            }
        });
    }

    $('#no-image').click(function (e) {
        e.preventDefault();

        notification('Pas de photos ?', 'vous pourrez ajouter gratuitement vos photos plus tard en quelques secondes.', {'timeOut': '8000', 'closeButton': true})
    });

    // Cart
    let $option_input_select = $('.advert-options .option-list .option-durante .option-select'),
        $option_input = $('.advert-options .option-list .option-data input'),
        $option_btn = $('.advert-options .advert-option-carousel .btn-option');

    $option_input_select.change(function (e) {
        e.preventDefault();

        let $this = $(this), $product_id = $this.val(), $option_selected = $('option:selected', this),
            $product_option = $option_selected.attr('data-option'), $product_price = $option_selected.attr('data-price'),
            $input_checkbox = $('#option_' + $product_option),
            $price_bulk = $this.parents('.option-data-bulk').find('.price');

        if ($input_checkbox.prop('checked')) {

            showLoading();

            $.ajax({
                url: Routing.generate('app_cart_replace', {'id': $input_checkbox.val(), 'newId': $product_id}),
                success: function(data) {
                    if (data) {
                        notification('Panier', 'Votre panier a été mis à jour', {}, 'success')
                    } else {
                        notification('Panier', 'Erreur: cette option n\'est pas dans le panier', {}, 'error')
                    }

                    hideLoading();
                }
            });
        }

        $input_checkbox.val($product_id);
        $price_bulk.text($product_price)

        e.stopPropagation();
    });

    $option_input.click(function () {
        let $this = $(this), $option = $this.attr('data-option');

        showLoading();

        if ($this.prop('checked')) {
            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $this.val()}),
                success: function (data) {
                    if (data) {
                        let $carousel_btn = $('#advert-option-carousel-' + $option);

                        $carousel_btn.removeClass('btn-primary').addClass('btn-default has-cart');
                        $carousel_btn.html('<i class="fas fa-check"></i>');

                        notification('Panier', 'L\'option a été ajouter au panier', {}, 'success');
                    } else {
                        notification('Panier', 'Erreur: cette option est déjà dans le panier', {}, 'error');
                    }

                    hideLoading();
                }
            });
        } else {
            $.ajax({
                url: Routing.generate('app_cart_delete', {'id': $this.val()}),
                success: function(data) {
                    if (data) {
                        let $carousel_btn = $('#advert-option-carousel-' + $option);

                        $carousel_btn.addClass('btn-primary').removeClass('btn-default has-cart');
                        $carousel_btn.html('Ajouter au panier');

                        notification('Panier', 'L\'option a été retirer du panier', {}, 'success');
                    } else {
                        notification('Panier', "Erreur: cette option n'est pas dans le panier", {}, 'error');
                    }

                    hideLoading();
                }
            });
        }
    });

    $option_btn.click(function(e) {
        e.preventDefault();

        let $this = $(this), $option = $this.attr('data-option'), $option_input = $('#option_' + $option);

        showLoading();

        console.log($option_input);

        if (!$this.hasClass('has-cart')) {
            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $option_input.val()}),
                success: function(data) {
                    if (data) {
                        $option_input.prop('checked', true)
                        $this.removeClass('btn-primary').addClass('has-cart btn-default');
                        $this.html('<i class="fas fa-check"></i>');

                        notification('Panier', 'L\'option a été ajouter au panier', {}, 'success');
                    } else {
                        notification('Panier', 'Erreur: cette option est déjà dans le panier', {}, 'error');
                    }

                    hideLoading();
                }
            });
        } else {
            $.ajax({
                url: Routing.generate('app_cart_delete', {'id': $option_input.val()}),
                success: function(data) {
                    if (data) {
                        $option_input.prop('checked', false)
                        $this.addClass('btn-primary').removeClass('has-cart btn-default');
                        $this.html('Ajouter au panier');

                        notification('Panier', 'L\'option a été retirer du panier', {}, 'success');
                    } else {
                        notification('Panier', "Erreur: cette option n'est pas dans le panier", {}, 'error');
                    }

                    hideLoading();
                }
            });
        }
    });
});