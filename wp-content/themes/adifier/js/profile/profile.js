jQuery(document).ready(function($){
	"use strict";

	/* MY ACCOUNT CF */
	var $cFieldsWrapper = $('.category-custom-fields');
	if( $cFieldsWrapper.length > 0 ){
		$('.cf-field').adifierCustomFields();
		$('#category').on('change', function(){
			var $this = $(this);
			var $label = $this.parents('.form-group').find('label');
			$label.prepend('<i class="aficon-spin aficon-circle-notch" style="margin-'+( $('body').hasClass('rtl') ? 'left' : 'right' )+':2px;"></i>');
			$cFieldsWrapper.html( '' );
			$this.prop( 'disabled', true );
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				data: {
					action: 'adifier_get_cf',
					terms: [$(this).val()],
					post_id: $('input[name="advert_id"]').val()
				},
				success: function(response){
					$cFieldsWrapper.html( response );
					$('.cf-field').adifierCustomFields();
					$this.prop( 'disabled', false );
					$cFieldsWrapper.find( ".select2-multiple").select2();
					$cFieldsWrapper.find( ".select2-single").select2();
				},
				complete: function(){
					$label.find('i').remove();
				}
			});
		});
	}	

	$(document).on('adifier_loaded_nested', function(){
		$( ".select2-multiple:not(.select2-hidden-accessible)").select2();
		$( ".select2-single:not(.select2-hidden-accessible)").select2();
	});

	/* STARTS SELECT2 ON CATEGORY/LOCATION */
	if( $('.select2-enabled').length > 0 ){
		$( ".select2-multiple").select2();
		$( ".select2-single").select2();
		$('.select2-enabled').select2({ 
			width: '100%',
			templateSelection: function(element){
				return element.text.trim();
			},			
		}).on('select2:select', function (e) {
			var $this = $(this);
			if( $this.val() ){
				$this.next().addClass('select2-enabled-color');
			}
			else{
				$this.next().removeClass('select2-enabled-color');
			}
		});;
	}

	$(document).on('click', '.another-video', function(e){
		e.preventDefault();
		if( adifier_data.max_videos && adifier_data.max_videos <= $('.video-input-wrap').length ){
			return false;
		}
		var $videoWrap = $('.video-input-wrap:first').clone();
		$videoWrap.find('input').val('');
		$('.video-input-wrap:last').after( $videoWrap );
	});

	$(document).on('click', '.remove-video', function(e){
		e.preventDefault();
		var $this = $(this);
		if( $('.video-input-wrap').length > 1 ){
			$this.parents('.video-input-wrap').remove();
		}
		else{
			$this.parents('.video-input-wrap').find('input').val('');
		}
	});

	$(document).on( 'change', '#user_address', function(){
		var $map = $('.adifier-map');
		var $parent = $('.advert-location-wrap');
		if( $(this).prop('checked') ){
			$parent.addClass('hidden');
		}
		else{
			$parent.removeClass('hidden');
			if( $parent.hasClass('reveal-after') ){
				AdifierMapInit( $map );
				$parent.removeClass('reveal-after');
			}
		}
	});

	$(document).on( 'change', '#user_contact', function(){
		var $this = $(this);
		if( $this.prop('checked') ){
			$('.new-contact').addClass('hidden');
		}
		else{
			$('.new-contact').removeClass('hidden');
		}
	});

	$(document).on('change', '#type', function(){
		var $this = $(this);
		$('div[class*="advert-type-"]').addClass('hidden');
		$('.advert-type-'+$this.val()).removeClass('hidden');
		$('div[class*="advert-type-"] .currency-swap').attr('name', '');
		$('.advert-type-'+$this.val()+' .currency-swap').attr('name', 'currency');
	});

	var ajaxing = false;
	$(document).on( 'submit', '.ajax-save-advert', function(e){
		e.preventDefault();
		if( !ajaxing ){
			ajaxing = true;
			var $this = $(this);
			var $result = $this.find('.ajax-form-result');
			var $submitButton = $this.find( '.submit-ajax-form' );
			var formData;
			var spin = '<i class="aficon-spin aficon-circle-notch"></i>';
			var isIcon = false;
			var oldIcon = '';

			if( $this.find('#description').length > 0 && typeof tinyMCE !== 'undefined' && $('#wp-description-wrap').hasClass('tmce-active') ){
				var tiny = tinyMCE.get('description').getContent();
				$('#description').val( tiny );
			}

			formData = new FormData($(this)[0]);

			if( $submitButton.find('i').length == 0 ){
				$submitButton.append( spin );
			}
			else{
				isIcon = true;
				oldIcon = $submitButton.html();
				$submitButton.html( spin );
			}
			
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				processData: false,
				contentType: false,
				data: formData,
				dataType: "JSON",
				success: function(response){
					$result.html( response.message ); 
					featuredImage = $('[name="featured_image"]:checked') ? $('[name="featured_image"]:checked').val() : '';
					advertId = response.advert_id;					

					if( response.advert_id ){
						$('[name="advert_id"]').val( response.advert_id );
					}

					if( response.parent_id ){
						$('[name="advert_parent_id"]').val( response.parent_id );
					}

					if( response.saved ){
						$('.remove-after-initial-save').remove();
					}

					if( $('.dz-preview').length > 0 && $result.find('.alert-error').length == 0 ){
						$result.hide();

						projectUploadImage.options.autoProcessQueue = true;
						projectUploadImage.processQueue();
					}
					else{
						$submitButton.find('i').remove();
						ajaxing = false;
					}
				}
			});
		}
	});	

	var $imagesHolder = $( ".images-holder" );
	var featuredImage, advertId;
	if( $imagesHolder.length > 0 ){
		$imagesHolder.sortable();

		if( $(".images-uploader").length > 0 ){			
			var projectUploadImage = new Dropzone( $(".images-uploader")[0], {
		        url: adifier_data.ajaxurl+'?action=upload_image',
		        clickable: '.uploader-browse',
		        autoProcessQueue: false,
		        timeout: 0,
		        acceptedFiles: 'image/jpeg,image/png',
		        dictDefaultMessage: dropzone_locale.dictDefaultMessage,
		        dictFallbackMessage: dropzone_locale.dictFallbackMessage,
		        dictFallbackText: dropzone_locale.dictFallbackText,
		        dictFileTooBig: dropzone_locale.dictFileTooBig,
		        dictInvalidFileType: dropzone_locale.dictInvalidFileType,
		        dictResponseError: dropzone_locale.dictResponseError,
		        dictCancelUpload: dropzone_locale.dictCancelUpload,
		        dictUploadCanceled: dropzone_locale.dictUploadCanceled,
		        dictCancelUploadConfirmation: dropzone_locale.dictCancelUploadConfirmation,
		        dictRemoveFile: dropzone_locale.dictRemoveFile,
		        dictMaxFilesExceeded: dropzone_locale.dictMaxFilesExceeded,
		        thumbnailWidth: 150,
		        thumbnailHeight: 150,
		        maxFiles: adifier_data.max_images ? adifier_data.max_images : null,
		        maxFilesize: adifier_data.max_image_size ? adifier_data.max_image_size : 256,
		        previewsContainer: '.images-holder',
		        previewTemplate: '  <div class="dz-preview dz-file-preview image-input-wrap">\
		                                <div class="dz-loading-icon"><i class="aficon-circle-notch aficon-spin"></i></div>\
		                                <div class="dz-details">\
		                                    <img data-dz-thumbnail />\
		                                </div>\
		                                <div class="dz-progress"><i class="aficon-circle-notch aficon-spin"></i></div>\
		                                <div class="dz-success-mark"><i class="aficon-circle-notch aficon-spin"></i></div>\
		                                <div class="dz-error-mark"><span><i class="aficon-circle-notch aficon-spin"></i></span></div>\
										<div class="styled-radio">\
											<input type="radio" name="featured_image" id="featured_image" value="">\
											<label for="featured_image"></label>\
										</div>\
		                                <a href="javascript:;" class="dz-remove" data-dz-remove><i class="aficon-times-circle"></i></a>\
		                                <div class="dz-error-message"><span data-dz-errormessage></span></div>\
		                            </div>',
		        init: function(){
					this.on("processing", function(file) {
						this.options.params = {
							advert_id: advertId,
							featured_image: featuredImage
						}

						$('.ajax-form-images').removeClass('hidden');
					});
					this.on("success", function(file, response) {
						$(file.previewElement).after( response );
						$(file.previewElement).remove();
					});
					this.on("queuecomplete", function(file, response) {
						$.ajax({
							url: adifier_data.ajaxurl,
							method: 'POST',
							data: {
								action: 'adifier_gallery_resave',
								advert_id: $('[name="advert_id"]').val(),
								images: $("input[id='images']").map(function(){return $(this).val();}).get()
							},
							dataType: "JSON",
							complete: function(){
					        	$('.dropzone-uploader i').remove();
					        	$('.ajax-form-result').show();
					        	ajaxing = false;
					        	projectUploadImage.options.autoProcessQueue = false;
					        	$('.ajax-form-images').addClass('hidden');								
							}
						});
					});
					this.on("thumbnail", function(file) {
						if ( file.width < 355 || file.height < 250 ) {
							alert( 'Image is too small' );
							this.removeFile(file);
						}
					});
					this.on("error", function(file, message) { 
					    alert(message);
					    this.removeFile(file); 
					});					
		        },
		        maxfilesexceeded: function( file ){
		        	$(file.previewElement).remove();
		        },
		        thumbnail: function( file, dataUrl ){
					var thumbnailElement, _i, _len, _ref;
					if ( file.previewElement ) {

						var rnd = 'aid-'+Math.random().toString(36).substring(7);

						file.previewElement.classList.remove("dz-file-preview");
						_ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
						
						for (_i = 0, _len = _ref.length; _i < _len; _i++) {
							thumbnailElement = _ref[_i];
							thumbnailElement.alt = file.name;
							thumbnailElement.src = dataUrl;
						}

						if( document.querySelectorAll('input[name="featured_image"]:checked').length == 0 ){
							file.previewElement.querySelectorAll("#featured_image")[0].checked = true;							
						}

						file.previewElement.querySelectorAll("#featured_image")[0].value = file.name;

						file.previewElement.querySelectorAll('[for="featured_image"]')[0].htmlFor = rnd;
						file.previewElement.querySelectorAll("#featured_image")[0].id = rnd;

						return setTimeout((( function(_this) {
							return function() {
								return file.previewElement.classList.add("dz-image-preview");
							};
						})(this)), 1);						
					}
					if (file.width < 355 || file.height < 250) {
						file.rejectDimensions()
					}
					else {
						file.acceptDimensions();
					}
		        }
			});
		}		

		$(document).on('click', '.remove-image', function(e){
			e.preventDefault();
			$(this).parents('.image-input-wrap').remove();
		});		
	}

	$(document).on('click', '.profile-delete-advert', function(e){
		e.preventDefault();
		var $this = $(this);
		var $parent = $this.parents('.profile-advert');

		if( confirm( $this.data('confirm') ) ){
			$this.find('i').attr( 'class', 'aficon-stopwatch' );
			$.ajax({
				url: adifier_data.ajaxurl,
				data: {
					action: 'adifier_delete_advert',
					advert_id: $this.data('id')
				},
				dataType: "JSON",
				method: 'POST',
				success: function( response ){
					if( response.result === true ){
						$parent.fadeOut(100, function(){
							$parent.remove();
						});
					}
					else{
						alert( response.result );
					}
				},
				complete: function(){
					$this.find('i').attr( 'class', 'aficon-trash-alt' );
				}
			});
		}
	});

    $(document).on('click', '.remove-favorites', function(e){
        e.preventDefault();
        var $this = $(this);
        if( confirm( $this.data('confirm') ) ){
	        $.ajax({
	            url: adifier_data.ajaxurl,
	            method: 'POST',
	            data:{
	                action: 'adifier_process_favorites',
	                advert_id: $this.data('id')
	            },
	            dataType: 'JSON',
	            success: function(response){
	                if( typeof response.error == 'undefined'  ){
	                    $this.parents('.profile-advert').remove();
	                }
	                else{
	                    alert( response.error );
	                }
	            }
	        });
	    }
    });	

    /* CURRENCY CHANGE ON SUBMIT */
	function number_format (number, decimals, dec_point, thousands_sep) {
	    // Strip all characters but numerical ones.
	    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	    var n = !isFinite(+number) ? 0 : +number,
	        prec = decimals == 'yes' ? 2 : 0,
	        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	        s = '',
	        toFixedFix = function (n, prec) {
	            var k = Math.pow(10, prec);
	            return '' + Math.round(n * k) / k;
	        };
	    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	    if (s[0].length > 3) {
	        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	    }
	    if ((s[1] || '').length < prec) {
	        s[1] = s[1] || '';
	        s[1] += new Array(prec - s[1].length + 1).join('0');
	    }
	    return s.join(dec);
	}    
    $(document).on('change', '#currency', function(){
    	var $this = $(this);
    	var val = $this.val();
    	$this.parents('div[class*="advert-type-"]').find('#price, #sale_price, #rent_price, #buy_price, #start_price, #salary, #max_salary, #reserved_price ').attr('placeholder', number_format( 0, adifier_currency_specs[val]['show_decimals'], adifier_currency_specs[val]['decimal_separator'], adifier_currency_specs[val]['thousands_separator'] ))
    });

	/* BIDDING HISTORY */
	$(document).on('click', '.bidding-history', function(e){
		e.preventDefault();
		var $this = $(this);
		$this.append('<i class="aficon-circle-notch aficon-spin"></i>');
		$.ajax({
			url: typeof ajaxurl != 'undefined' ? ajaxurl : adifier_data.ajaxurl,
			data:{
				action: 'adifier_bid_history',
				advert_id: $this.data('advertid'),
				bidpage: $this.data('bidpage'),
				full: true
			},
			dataType: 'JSON',
			method: 'POST',
			success: function( response ){
				$('.bidding-history-results').append( response.message );
		    	if( response.next_page ){
		    		$this.html( response.btn_text );
		    		$this.data( 'bidpage', response.next_page );
		    	}
		    	else{
		    		$this.remove();
		    	}
			}
		})
	});	 

	$(document).on('click', '.promotion-description-toggle', function(){
		$('.pr-'+$(this).data('target')).slideToggle();
	});

	$(document).on('af-phone-verify-code', function( e, response ){
		if( response.codeform ){
			$('.phone-verification-wrap').html( response.codeform );
		}
	});
});