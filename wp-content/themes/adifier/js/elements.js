jQuery(document).ready(function($){
	"use strict";

	var isRTL = $('body').hasClass('rtl') ? true : false;

	function startAdvertsSlider(){
	    $('.adverts-slider').each(function(){
	    	var $this = $(this);
	    	if( !$this.hasClass('owl-loaded') ){
		    	$this.owlCarousel({
			    	responsive: {
			    		0: {
			    			items: 1,
			    			loop: $this.data('autoplay') != '' ? true : false,
			    		},
			    		667: {
			    			items: 2,
			    			loop: ( $this.find('.af-item-wrap').length >= 2 && $this.data('autoplay') != '' ) ? true : false,
			    		},
			    		990:{
			    			items: $this.data('visibleitems'),
			    			loop: ( $this.find('.af-item-wrap').length >= $this.data('visibleitems') && $this.data('autoplay') != '' ) ? true : false,
			    		}
			    	},
			    	rtl: isRTL,
			        nav: true,
			        autoplay: $this.data('autoplay') != '' ? true : false,
			        autoplayTimeout: $this.data('autoplay'),
			        autoplayHoverPause: $this.data('autoplay') != '' ? true : false,
			        dots: false,
			        navText: ['<i class="aficon-long-arrow-alt-left-1"></i>','<i class="aficon-long-arrow-alt-right-1"></i>'],
			        navElement: "div",
			        margin: 25,
			        stagePadding: 10,
				    onInitialized: function(){

				    }			        
		    	});
		    }
		});
	}

	startAdvertsSlider();
	$(document).on( 'kc_advert-js-trigger', function(){
		startAdvertsSlider();
	});	

	function startCategoriesSlider(){
	    $('.categories-slider').each(function(){
	    	var $this = $(this);
	    	if( !$this.hasClass('owl-loaded') ){
		    	$this.owlCarousel({
			    	responsive: {
			    		0: {
			    			items: 1,
			    			loop: $this.data('autoplay') != '' ? true : false,
			    		},
			    		667: {
			    			items: 2,
			    			loop: ( $this.find('.af-item-wrap').length >= 2 && $this.data('autoplay') != '' ) ? true : false,
			    		},
			    		768: {
			    			items: 3
			    		},
			    		990:{
			    			items: $this.data('visibleitems'),
			    			loop: ( $this.find('.af-item-wrap').length >= $this.data('visibleitems') && $this.data('autoplay') != '' ) ? true : false,
			    		}
			    	},
			    	rtl: isRTL,
			        nav: true,
			        autoplay: $this.data('autoplay') != '' ? true : false,
			        autoplayTimeout: $this.data('autoplay'),
			        autoplayHoverPause: $this.data('autoplay') != '' ? true : false,
			        dots: false,
			        navText: ['<i class="aficon-angle-left"></i>','<i class="aficon-angle-right"></i>'],
			        navElement: "div",
			        margin: typeof $this.data('margin') == 'undefined' ? 10 : $this.data('margin'),
			        stagePadding: typeof $this.data('stagepadding') == 'undefined' ? 9 : $this.data('stagepadding'),
		    	});
		    }
		});
	}

	startCategoriesSlider();
	$(document).on( 'kc_categories-js-trigger', function(){
		startCategoriesSlider();
	});	

	function startSearchMap(){
		$('.search-map').each(function(){
			var $this = $(this);
			if( !$this.hasClass('map-loaded') ){
				var content = $this.find('script').html();
				var data = content ? JSON.parse( content ) : '';

				$(document).trigger('adifier-search-map-launch', [$this]);
				if( data ){
					$(document).trigger('adifier-search-map-start', [data]);
				}
				$this.addClass('map-loaded');
			}
		});
	}

	startSearchMap();
	$(document).on( 'kc_promotion_map-js-trigger', function(){
		startSearchMap();
	});	

	$('.kc_cat_search').on('click', function(){
		var $this = $(this);
		var data = 'category='+$this.data('cat');
		document.cookie = 'af-search-data='+data+'; path=/;'; 
		window.location = $this.data('href');
	});

	$('.kc_loc_search').on('click', function(){
		var $this = $(this);
		var data = 'location_id='+$this.data('loc');
		document.cookie = 'af-search-data='+data+'; path=/;'; 
		window.location = $this.data('href');
	});

	function startAdvertsBigSlider(){
		$('.adverts-big-slider').each(function(){
			var $this = $(this);
			if( !$this.hasClass('owl-loaded') ){
				$this.owlCarousel({
					items: 1,
					rtl: isRTL,
				    nav: true,
				    dots: false,
				    navText: ['<i class="aficon-long-arrow-alt-left-1"></i>','<i class="aficon-long-arrow-alt-right-1"></i>'],
				    navElement: "div",
				});
			}
		});
	}

	startAdvertsBigSlider();
	$(document).on( 'kc_advert-js-trigger', function(){
		startAdvertsBigSlider();
	});	

	$(document).on('focus', '.element-qs input', function(){
		var $form = $(this).parents('form');
		if( $form.find( '.adv-title' ).length > 0 ){
			$form.find( '.ajax-form-result' ).show();
		}
	});

	$(document).on('click', function(e){
		if( $(e.target).parents('.element-qs').length == 0 ){
			$( '.element-qs .ajax-form-result' ).hide();
		}
	});

	/* SLIDER TEXT */
	/*
	helper function for interactive slider
	*/
	function activateSlideItem( $this ){
		var $item = $this.find('.owl-item.active .af-interactive-item');
		$item.find('img').animate({
			opacity: 1,
			right: 0
		}, 300, 'linear') 

		$item.find('h2').delay(300).animate({
			opacity: 1,
			top: 0
		}, 300, 'linear');

		$item.find('p').delay(600).animate({
			opacity: 1,
			left: 0
		}, 300, 'linear');

		$item.find('a').delay(900).animate({
			opacity: 1,
			bottom: 0
		}, 300, 'linear');
	}
	/*
	Helper function for fadeout
	*/
	var timer_IFST = null;
	function interactiveFadeTimeout( $this ){
		timer_IFST = setTimeout(function(){
			var $item = $this.find('.owl-item.active .af-interactive-item');
			$item.animate({
				opacity: 0
			}, 250, function(){
				setTimeout(function(){
					$item.css('opacity', 1);
				}, 1000);
			});
		}, 4700);
	}
	function startTextSlider(){
		$('.af-text-slider').each(function(){
			var $this = $(this);
			if( !$this.hasClass('owl-loaded') ){
				var owl = $this.owlCarousel({
					items: 1,
					rtl: isRTL,
					autoplay: true,
					autoplayTimeout: $this.data('speed') ? $this.data('speed') : 5000,
					autoplayHoverPause: $this.hasClass( 'af-interactive-slider' ) ? false : true,
					mouseDrag: $this.hasClass( 'af-interactive-slider' ) ? false : true,
					touchDrag: $this.hasClass( 'af-interactive-slider' ) ? false : true,
					pullDrag: $this.hasClass( 'af-interactive-slider' ) ? false : true,
					animateOut: $this.hasClass( 'af-interactive-slider' ) ? 'fadeOut' : '',
					loop: true,
				    nav: false,
				    dots: true,
				    onInitialized: function(){
				    	$this.find('.owl-dot').css('background', $this.data('color'));
				    	if( $this.hasClass( 'af-interactive-slider' ) ){
				    		activateSlideItem( $this );
				    	}
				    	interactiveFadeTimeout( $this );
				    }
				});
				if( $this.hasClass( 'af-interactive-slider' ) ){
					owl.on('changed.owl.carousel', function(){
						owl.trigger('stop.owl.autoplay');
						owl.trigger('play.owl.autoplay');
						clearTimeout(timer_IFST);					
						interactiveFadeTimeout( $this );
					});
					owl.on('translated.owl.carousel', function(e) {
						$this.find('.owl-item:not(.active) .af-interactive-item img').css({
							opacity: 0,
							right: -100
						});			
						$this.find('.owl-item:not(.active) .af-interactive-item h2').css({
							opacity: 0,
							top: -200
						});
						$this.find('.owl-item:not(.active) .af-interactive-item p').css({
							opacity: 0,
							left: -200
						});
						$this.find('.owl-item:not(.active) .af-interactive-item a').css({
							opacity: 0,
							bottom: -100
						});

						activateSlideItem( $this );
					});
				}
			}
		});
	}

	startTextSlider();
	$(document).on( 'kc_advert-js-trigger', function(){
		startTextSlider();
	});	

	/* SLIDER BG TEXT */
	function startBGTextSlider(){
		$('.af-slider-bg-text').each(function(){
			var $this = $(this);
			if( !$this.hasClass('owl-loaded') ){
				$this.owlCarousel({
					items: 1,
					rtl: isRTL,
					autoplay: true,
					autoplayTimeout: $this.data('speed') != '' ? $this.data('speed') : 3000,
					autoplayHoverPause: true,
					loop: true,
				    nav: true,
				    dots: false,
				    navText: ['<i class="aficon-caret-left"></i>','<i class="aficon-caret-right"></i>'],
				    navElement: "div",
				});
			}
		});
	}

	startBGTextSlider();
	$(document).on( 'kc_advert-js-trigger', function(){
		startBGTextSlider();
	});	

	/*
	* How It Works
	*/

	$('.hiw-item-style').each(function(){
		var $this = $(this);
		var $html = $this.html();
		$('head').append('<style>'+$html+'</style>');
		$this.remove();
	});

	/*
	* Start typed texts
	*/
	$('.af-typed-text').each(function(){
		var $this = $(this);
		var $elem = $this.find('span');
		var strings = $this.text();
		strings = strings.replace(/&/g, '&amp;');
		var options = {
		  strings: strings.split('|'),
		  typeSpeed: $this.data('speed'),
		  backSpeed: $this.data('back'),
		  loop: true,
		  contentType: 'html',
		  smartBackspace: $this.data('smart') == 'yes' ? true : false
		};

		$this.removeClass('hidden');
		$elem.html('');

		new Typed($elem.get()[0], options);
	});
});