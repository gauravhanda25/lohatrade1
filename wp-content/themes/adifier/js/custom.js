jQuery(document).ready(function($){
	"use strict";

	var isRTL = $('body').hasClass('rtl') ? true : false;
	var isLoggedIn = $('body').hasClass('logged-in') ? true : false;

	$('form').prepend('<input type="hidden" value="1" name="aff-cpt">');

	$('.postform, .widget_archive select, .widget_text select').wrap('<div class="styled-select"></div>');

	/* TO TOP */
	$(window).on('scroll', function(){		
		if( $(window).scrollTop() > 200 ){
			$('.to_top').fadeIn(100);
		}
		else{
			$('.to_top').fadeOut(100);
		}
	});	

	$('.nav-paste').html( $('.nav-copy').html() );

	$(document).on('click', '.to_top', function(e){
		e.preventDefault();
		$("html, body").stop().animate(
			{
				scrollTop: 0
			}, 
			{
				duration: 1200
			}
		);		
	});

	if( adifier_data.enable_sticky == 'yes' && ( $('.author-header').length == 0 || ( $('.author-header').length > 0 && $(window).width() < 1025 ) ) ){
		$('header.sticky-header').each(function(){
			var $elem = $(this);

			if( $('.header-2.top-header').length > 0 && $(window).width() <= 1024 ){
				$elem = $('.header-2.top-header');
			}

			if( $('.header-4').length > 0 && $(window).width() <= 1024 ){
				$elem = $('.header-4.upper-header');
			}

			var $clone = $elem.clone();
			if( $clone.hasClass('header-2') && $('.dark-logo-wrap').html() !== '' && $(window).width() > 1024 ){
				$clone.find('.container > .flex-wrap').prepend( $('.dark-logo-wrap').html() );
			}

			if( $clone.hasClass('header-4') && $(window).width() > 1024 ){
				$clone.find('.flex-right').append( $('.special-nav').clone() );
			}

			$('body').append( $clone.addClass('sticky-nav') );
			var $adminBar = $('#wpadminbar');
			var hasAdminBar = $adminBar.length > 0 ? true : false;

			var scrollTop = 0;
			if( ( $elem.hasClass('header-2') || $elem.hasClass('header-4') ) && !$elem.hasClass('header-3') && !$elem.hasClass('header-5') ){
				scrollTop = $elem.offset().top;
			}

			$(window).scroll(function(){
				if( ( !hasAdminBar && $(window).scrollTop() > scrollTop ) || ( hasAdminBar && $adminBar.css('position') == 'fixed' && $(window).scrollTop() > scrollTop ) || ( hasAdminBar && $adminBar.css('position') == 'absolute' && $(window).scrollTop() > $elem.offset().top )  ){
					var top;
					( $('#wpadminbar').length > 0 && $('#wpadminbar').css('position') == 'fixed' ) ? top = $('#wpadminbar').height() : top = 0;
					$clone.css('top', top+'px');
				}
				else{
					$clone.css('top', '-500px');	
				}
			});			
		});
	}
	else{
		var wpadminbar = $('#wpadminbar');
		if( wpadminbar.length > 0 ){
			$('.author-sidebar').css( 'top', wpadminbar.height() );
		}
	}
	
	function smallScreenDropdown(e){
		if( $(window).width() < 1024 && e.target.nodeName == 'I' ){
			//alert('fds');
			e.preventDefault();
			var $this = $(this);
			if( !$this.parent().hasClass('open') ){
				$this.parent().addClass('open').find(' > .dropdown-menu').show();
				$this.parents('.dropdown').addClass('open').find(' > .dropdown-menu').show();
			}
			else{
				$this.parent().removeClass('open').find(' > .dropdown-menu').hide();
				$this.parent().find('.dropdown').removeClass('open').find(' > .dropdown-menu').hide();
			}					
		}
	}
	
	function handle_navigation(){
		if ($(window).width() > 1024) {
			$('.navigation li.dropdown, .navigation li.dropdown-submenu').hover(function () {
				$(this).addClass('open').find(' > .dropdown-menu').stop(true, true).hide().slideDown(50);
			}, function () {
				$(this).removeClass('open').find(' > .dropdown-menu').stop(true, true).show().slideUp(50);
	
			});
		}
		else{
			$('.dropdown-toggle').removeAttr('data-toggle');
			$(document).off( 'click', 'li.dropdown a', smallScreenDropdown);
			$(document).on( 'click', 'li.dropdown a', smallScreenDropdown);
		}
	}
	handle_navigation();
	
	$(window).resize(function(){
		setTimeout(function(){
			handle_navigation();
		}, 200);
	});

	if( $('.header-3').length > 0 ){
		if( $('#wpadminbar').length > 0 ){
			$('.header-3:not(.sticky-nav)').css( 'top', $('#wpadminbar').height() );
		}
	}

	/* SUBMIT FORMS */
	$(document).on('click', '.submit-ajax-form, .submit-form', function(e){
		e.preventDefault();
		$(this).parents('form').submit();
	});

	$('.change-submit').on('change', function(e){
		$(this).parents('form').submit();
	});

	$('.ajax-form, .key-submit-form').keypress(function(e){
    	if(e.which == 13 && e.target.nodeName !== 'TEXTAREA' ) {
    		$(this).submit();
    	}
    });

	$('.header-search input[name="keyword"]').keypress(function(e){
    	if(e.which == 13 ) {
    		$(this).parents('form').submit();
    	}
    });

	var ajaxing = false;
	$(document).on( 'submit', '.ajax-form', function(e){
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
			
			$submitButton.find('.icon-response').remove();

			/* we mus disable empty file inputs since iOS is messing it up */
			var $inputs = $('input[type="file"]:not([disabled])', $this);
			$inputs.each(function(_, input) {
				if (input.files.length > 0){
					return;	
				}

				$(input).prop('disabled', true);
			});

			formData = new FormData($(this)[0]);
			$inputs.prop('disabled', false);

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
					if( $this.data('append') ){
						$result.append( response.message );
					}
					else{
						$result.html( response.message );
					}

					if( response.url ){
						window.location = response.url;
					}
					
					if( response.reload ){
						window.location.reload();
					}					

					if( !isIcon ){
						$submitButton.find('i').remove();
					}
					else{
						$submitButton.html( oldIcon )
					}

					if( response.icon_response ){
						$submitButton.append( response.icon_response );
					}

					if( $submitButton.data('callbacktrigger') ){
						$(document).trigger( $submitButton.data('callbacktrigger'), [response] );
					}
									
					ajaxing = false;
				},				
			});
		}
	});
	
	$(document).on('click', '.submit-redirect', function(){
		$('#login form').append('<input type="hidden" class="redirect" name="redirect" value="submit" />');
	});

	$('#login').on('hidden.bs.modal', function () {
	    $(this).find('.redirect').remove();
	});

	/* contact script */
	var $map = $('.contact-map');
	if( $map.length > 0 ){
		var markers = JSON.parse( $map.html().trim() );
		$map.html('');
		$map.removeClass('hidden');

		if( adifier_map_data.map_source == 'google' ){
			var bounds = new google.maps.LatLngBounds();
			var mapOptions = { mapTypeId: google.maps.MapTypeId.ROADMAP };
			var map =  new google.maps.Map($map[0], mapOptions);
			if( markers.length > 0 ){
				for( var i=0; i<markers.length; i++ ){
					var temp = markers[i].split(',');
					var location = new google.maps.LatLng( temp[0], temp[1] );
					bounds.extend( location );

					var icon = adifier_data.marker_icon;
					if( icon !== '' ){
					    icon = {
					    	url: adifier_data.marker_icon,
					    	size: adifier_data.marker_icon_width ? new google.maps.Size( adifier_data.marker_icon_width / 2, adifier_data.marker_icon_height / 2 ) : '',
					    	scaledSize: adifier_data.marker_icon_width ? new google.maps.Size( adifier_data.marker_icon_width / 2, adifier_data.marker_icon_height / 2 ) : ''
					    };
					}

					var marker = new google.maps.Marker({
					    position: location,
					    map: map,
					    icon: icon
					});				
				}

				map.fitBounds( bounds );

				var listener = google.maps.event.addListener(map, "idle", function() { 
					if( adifier_data.markers_max_zoom != '' ){
				  		map.setZoom(parseInt( adifier_data.markers_max_zoom ));
				  		google.maps.event.removeListener(listener); 
				  	}
				});			
				
			}
		}
		else if( adifier_map_data.map_source == 'mapbox' ){
			mapboxgl.accessToken = adifier_mapbox_data.api;
			var bounds = new mapboxgl.LngLatBounds();
            var map = new mapboxgl.Map({
                container: $map[0],
                style: adifier_data.map_style ? adifier_data.map_style : 'mapbox://styles/mapbox/light-v9'
            }); 
            if( markers.length > 0 ){
            	$.each(markers, function(index, marker){
					var temp = marker.split(',');
					var location = new mapboxgl.LngLat( temp[1], temp[0] );
					bounds.extend( location );

					var el = '';
					if( adifier_data.marker_icon !== '' ){
		                el = document.createElement('div');
		                el.className = 'mapboxgl-marker';
		                el.style.backgroundSize = 'contain';
		                el.style.backgroundImage = 'url('+adifier_data.marker_icon+')';
		                el.style.width =  adifier_data.marker_icon_width / 2 + 'px';
		                el.style.height = adifier_data.marker_icon_height / 2 + 'px';
					}

					var marker = new mapboxgl.Marker(el).setLngLat(location).addTo(map);
            	});

            	map.fitBounds(bounds, {padding: 30, maxDuration: 1000});
            }
		}
	}   

	/* START AUTOCOMPLETE ON HEADER SEARCH */
	function headerCategoryColor( $select ){
		if( $select.val() ){
			$select.css('color', '#484848');
		}
		else{
			$select.css('color', '#ccc');	
		}
	}
	var $headerLocation = $('.header-location');
	if( $headerLocation.length > 0 ){
		var $location = $headerLocation.find('.location');
		var $latitude = $headerLocation.find('.latitude');
		var $longitude = $headerLocation.find('.longitude');
		if( $location.length > 0 ){
			if( adifier_map_data.map_source == 'google' ){
				var autocomplete = new google.maps.places.Autocomplete( $location.get(0) );
				if( adifier_data.country_restriction ){
					autocomplete.setComponentRestrictions({'country': adifier_data.country_restriction.split(',')});
				}

				google.maps.event.addListener(autocomplete, 'place_changed', function() {
					var place = autocomplete.getPlace();
					$latitude.val( place.geometry.location.lat() );
					$longitude.val( place.geometry.location.lng() );
				});
			}
		}
	}

	var $headerSearch = $('.header-search,.labeled-main-search');
	if( $headerSearch.length > 0 ){
		$headerSearch.find('select').each(function(){
			var $this = $(this);
			$this.on('change', function(){
				headerCategoryColor( $(this) );
			});
			headerCategoryColor( $this );
		});
	}

    /* REVEAL PHONE */
    $('.reveal-phone').on('click', function(e){
    	var $this = $(this);
    	if( !$this.hasClass( 'revealed' ) ){
	        e.preventDefault();
	        var $em = $this.find('em');
	        $em.text( $em.text().replace('XXX', $this.data('last')) );
	        $this.attr('href', 'tel:'+$em.text().replace(/[`~!@#$%^&*()_| \-=?;:'",.<>\{\}\[\]\\\/]/gi, ''))
	        $this.addClass('revealed')
	    }
    });	

    /* KC PATCH FOR VIDEO OVERLAY */
    $('.kc-video-bg').each(function(){
    	var $this = $(this);
    	if( $this.css('background-image') ){
    		$this.append('<div class="kc-video-overlay" style="background: '+$this.css('background-image')+'"></div');
    	}
    });

    /* TOGGLE AUTHOR SIDEBAR */
	try {
	    if( $(window).width() <= 1024 ){
	    	$('.navigation-wrap > ul').scrollbar();
	    }
	    $('.author-sidebar > div').scrollbar();
	}
	catch(error) {
		
	}
    $('.small-sidebar-open').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        if( !$this.hasClass('sso') ){
	        $this.addClass('sso');
	        var target = $this.data('target');
	        var $target = $(target);
	        $target.parents('header').addClass('header-top');
	        $target.addClass('open');
	        setTimeout(function(){
	        	$('body').append('<a href="javascript:void(0);" class="small-sidebar-close" data-target="'+target+'" style="'+( isRTL ? 'right' : 'left' )+': '+($target.outerWidth() - 15)+'px; top:'+($(window).height() / 2)+'px; z-index:999999999999999;"><i class="aficon-times-circle"></i></a>');
	        }, 200);
	    }
    });  

    $(document).on('click', '.small-sidebar-close', function(e){
        e.preventDefault();
        var $this = $(this);
        var $target = $( $this.data('target') );
        $target.removeClass('open');
        $('.sso').removeClass('sso');
        $target.parents('header').removeClass('header-top');
        $this.remove();
    });

    /*PRINT ADVERT*/
    $('.print-advert').on('click', function(e){
    	e.preventDefault();
    	$('.reveal-phone').trigger('click');
    	window.print();
    });

    /* ROW SLIDER */
    $('.row-slider').each(function(){
    	var $this = $(this);
    	$this.find(" > img").css('opacity', 1);
		$this.find(" > img:gt(0)").hide();

		setInterval(function() { 
		  $this.find(' > img:first')
		    .fadeOut(1000)
		    .next()
		    .fadeIn(1000)
		    .end()
		    .appendTo( $this );
		},  4000);
    });

    /* MOVE COMMENTS TO THE BOTTOM OF THE SCREEN ON SMALLER SCREENS */
    if( $(window).width() <= 768 ){
	    $('main .container').append( $('.small-screen-last').html() );
	    $('.small-screen-last').html('');
	}

	/* GDPR subscribe show checkbox */
	$('.subscription-footer input[type="text"]').focus(function(){
		$('.subscription-footer .form-group').show();
	});
	$('.subscription-footer input[type="text"]').blur(function(){
		$('.subscription-footer .form-group').hide();
	});

	/* QUICK SEARCH */
	var quickSearchTimeout = false;
	$('.quick-search-form input').on('keyup', function(){
		var $this = $(this);
		var $result = $this.parents('form').find('.ajax-form-result');
		var $qsStatus = $('.quick-search-status');
		clearTimeout( quickSearchTimeout );
		if( $this.val().length >= 4 ){
			quickSearchTimeout = setTimeout(function(){
				$qsStatus.html('<i class="aficon-spin aficon-circle-notch"></i>');
				$.ajax({
					url: adifier_data.ajaxurl,
					method: 'POST',
					data: {
						action: 'adifier_ajax_quick_search',
						s: $this.val()
					},
					dataType: 'JSON',
					success: function( res ){
						$result.html( res.message );
						$result.show();
					},
					complete: function(){
						$qsStatus.html('');
					}
				})
			}, 500);
		}
	});

	/* TOGGLE TAXONOMY CHILDREN */
	$(document).on('click', '.af-dropdown-toggle', function(){
		var $this = $(this);
		var $target = $($this.data('target'));
		$this.toggleClass('open');
		$target.slideToggle();
	});

    /* ADD TO FAVORITES */
    $(document).on('click', '.process-favorites', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.find('i').attr('class', 'aficon-stopwatch');
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
                    $this.find('i').attr( 'class', response.success );
                }
                else{
                    alert( response.error );
                }
            }
        })
    });

	/*
	* Confirm action
	*/
	$(document).on('click', '.delete-acc', function(e){
		if( confirm( $(this).data('confirm') ) ){
			window.location.href = $(this).data('url');
		}
	});

	/*
	* Go to directions
	*/
	if( adifier_data.use_google_direction == 'yes' ){
		var $directions = $('.af-get-directions');
		if( $directions.length > 0 ){
			var $map = $directions.prev();
			var lat = $map.data('lat');
			var long = $map.data('long');		
			if (navigator.geolocation) {
			    navigator.geolocation.getCurrentPosition(function(position){
			    	$directions.attr('href', 'https://maps.google.com/maps?saddr='+position.coords.latitude+','+position.coords.longitude+'&daddr='+$map.data('lat')+','+$map.data('long'));
			    }, function(err){
	 				$directions.attr('href', 'https://maps.google.com/maps?saddr=&daddr='+$map.data('lat')+','+$map.data('long'));
			    });
			} else {
				$directions.attr('href', 'https://maps.google.com/maps?saddr=&daddr='+$map.data('lat')+','+$map.data('long'));
			}		
		}
	}

	/*
	Open header categories
	*/
	$(document).on('click', '.header-cats-trigger', function(e){
		e.preventDefault();
		$('.header-cats').toggleClass('open');
	});

	/*
	* inline comment validation
	*/
	$('#commentform').on('submit', function(e){
		var $this = $(this);
		var flag = false;	
		var source = '.comment-required-fields';
		
		$this.find('.required').each(function(){
			var $this = $(this);
			if( $this.val() == '' && $this.attr('name') !== 'url' ){
				flag = true;
			}
			else if( $this.attr('name') == 'email' && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test( $this.val() ) ){
				flag = true;
				source = '.comment-required-email';
			}
		});

		if( flag ){
			e.preventDefault();
			$('.comment-validation-remove').remove();
			$this.find('.form-submit').before( $this.find(source).clone().removeClass('hidden').addClass('comment-validation-remove') );
		}
	});

	/*
	* Scroll to
	*/
	$(document).on('click', '.scroll-to', function(e){
		e.preventDefault();
		$("html, body").stop().animate(
			{
				scrollTop: $($(this).data('target')).offset().top - $('.sticky-nav').outerHeight() - 50
			}, 
			{
				duration: 1200
			}
		);		
	})

	/*
	* Open modals
	*/
	$(document).on('click', '.af-modal', function(e){
		e.preventDefault();
		$($(this).attr('href')).modal('show');
	});	

	if( window.location.hash == '#register' ){
		$('#register').modal('show');
	}
	else if( window.location.hash == '#login' ){
		$('#login').modal('show');
	}
	else if( window.location.hash == '#recover' ){
		$('#recover').modal('show');
	}


	/*
	* Call unread messages
	*/
	function updateUnreadMessages(){
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'GET',
			data: {
				action: 'adifier_get_unread_message_num'
			},
			success: function(response){
				$('.unread-badge').remove();
				if( response !== '' ){
					$('.messages-unread-count').append( response );
				}					
			},
			complete: function(){
				setTimeout(updateUnreadMessages, 5000);
			}
		});
	}
	if( $('.messages-right').length == 0 ){
		updateUnreadMessages();
	}

	$(document).on('click', '.af-modal', function(e){
		e.preventDefault();
		$($(this).attr('href')).modal('show');
	});		

	/*
	* Toggle passwords
	*/
	$(document).on('click', '.toggle-password', function(){
		$('.reveal-password').each(function(){
			var $this = $(this);
			if( $this.attr('type') == 'text' ){
				$this.attr( 'type', 'password' );
			}
			else{
				$this.attr( 'type', 'text' );
			}
		});
	});
});