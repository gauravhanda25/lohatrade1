jQuery(document).ready(function($){
	"use strict";

	var searchWithMap = $('.search-with-map').length > 0 ? true : false;
	/* start autocomplete for location filter */
	if( $('.location').length > 0 && adifier_map_data.map_source == 'google' ){
		var autocomplete = new google.maps.places.Autocomplete( $('.location').get(0) );
		if( adifier_data.country_restriction ){
			autocomplete.setComponentRestrictions({'country': adifier_data.country_restriction.split(',')});
		}
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			$('.latitude').val( place.geometry.location.lat() );
			$('.longitude').val( place.geometry.location.lng() );
		});
	}

	$('.location').on('change', function(){
		if( $(this).val() ){
			$('.radius-slider').removeClass('hidden');
		}
		else{
			$('.radius-slider').addClass('hidden');
		}
	});

	/* toggle filters */
	$('.toggle-filters').on('click', function(e){
		e.preventDefault();
		$('.search-form').slideToggle();
	});

	/* start slider filters */
	$('.filter-slider').each(function(){
		startSlider( $(this) );		
	});

	function number_format (number, decimals, dec_point, thousands_sep) {
	    // Strip all characters but numerical ones.
	    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	    var n = !isFinite(+number) ? 0 : +number,
	        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
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

	function startSlider( $object ){
		var isRange = $object.data('range') ? true : false;
		var $parent = $object.parents('.slider-wrap');
		var $valueHolder = $parent.find('input');
		var $textValueHolder = $parent.find('.slider-value');
		var prefix = $object.data('prefix') ? $object.data('prefix') : '';
		var sufix = $object.data('sufix') ? $object.data('sufix') : '';
		var thousands = $object.data('thousands') ? $object.data('thousands') : false;
		var decimal = $object.data('decimal') ? $object.data('decimal') : false;
		var decimals = $object.data('decimals') ? $object.data('decimals') : 2;
		var $currency = $('#currency');
		if( $valueHolder.attr('name') == 'price' ){
			if( $currency.length > 0 ){
				prefix = adifier_currency_specs[$currency.val()]['form'] == 'front' ? adifier_currency_specs[$currency.val()]['sign'] : '';
				sufix = adifier_currency_specs[$currency.val()]['form'] == 'back' ? adifier_currency_specs[$currency.val()]['sign'] : '';				
				thousands = adifier_currency_specs[$currency.val()]['thousands_separator'];
				decimal = adifier_currency_specs[$currency.val()]['decimal_separator'];
				decimals = adifier_currency_specs[$currency.val()]['show_decimals'] == 'yes' ? 2 : 0;
			}
		}
		var args = {
			min: $object.data('min'),
			max: $object.data('max'),
			disabled: $object.data('disabled') ? true : false,
			slide: function(e, ui){
				if( isRange ){
					if( ui.values[0] == args.min && ui.values[1] == args.max ){
						$valueHolder.val('');
					}
					else{
						$valueHolder.val( ui.values[0]+","+ui.values[1] );
					}
					$textValueHolder.html( prefix+( decimal ? number_format( ui.values[0], decimals, decimal, thousands ) : ui.values[0] )+sufix+" - "+prefix+( decimal ? number_format( ui.values[1], decimals, decimal, thousands ) : ui.values[1] )+sufix );
				}
				else{
					if( ui.value == args.max ){
						$valueHolder.val('');
					}
					else{
						$valueHolder.val( ui.value );
					}
					$textValueHolder.html( prefix+ui.value+sufix );
				}
			}
		};
		if( isRange ){
			args.range = true;
			args.values= $valueHolder.val() ? $valueHolder.val().split(',') : [$object.data('min'),$object.data('max')];
			$textValueHolder.html( prefix+( decimal ? number_format( args.values[0], decimals, decimal, thousands ) : args.values[0] )+sufix+' - '+prefix+( decimal ? number_format( args.values[1], decimals, decimal, thousands ) : args.values[1] )+sufix );
		}
		else{
			args.range = 'min';
			args.value = $valueHolder.val() ? $valueHolder.val() : $object.data('default');
			$textValueHolder.html( prefix+args.value+sufix );
		}

		$object.slider( args );

		$(document).on('aficon-reset',function(){
			try {
				$object.slider('destroy');
				startSlider( $object );
			}
			catch(err){

			}
		});		
	}

	$(document).on('change', '#currency', function(){
		var $object = $('.price-filter-slider');
		$object.slider('destroy');
		startSlider( $object );		
	});

	/* start JS for custom fields */
	var $cfWrapper = $('.category-custom-fields');
	function startACF(){
		$( ".select2-multiple").select2();
		$( ".select2-single").select2();
		$( ".cf-datepicker").datetimepicker({ 
			dateFormat: 'mm/dd/yy',
			showTimepicker: false
		});
		$('.filter-slider:not(.ui-slider)').each(function(){
			startSlider( $(this) );
		});
	}

	/* on nested select change load next level */
	$cfWrapper.on('change', '.nested-select', function(){
		var $this = $(this);
		var $parent = $this.parents('.cf-field');
		var depth = $this.data('depth');
		var maxdepth = $this.data('maxdepth');
		var fieldid = $this.data('fieldid');
		var val = $this.val();

		for( var i=depth+1; i<=maxdepth; i++ ){
			$parent.find('.cf-nested.depth_'+i).remove();
		}
		if( depth < maxdepth && val ){
			$parent.append('<div class="text-center cf-loader"><i class="aficon-circle-notch aficon-spin"></i></div>');
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				data:{
					action: 'adifier_get_filter_subfield',
					field_id: fieldid,
					value: val,
					depth: depth + 1
				},
				success: function( response ){
					$parent.find('.cf-loader').remove();
					$parent.append( response );
					$('.select2-single:not(.select2-hidden-accessible)').select2();
				}
			});
		}
	});	

	/* if there are custom fields load scripts for them */
	var $cfFields = $('.cf-field');
	if( $cfFields.length > 0 ){
		startACF();
	}

	/* on category selection load custom fields */
	$(document).on('change', '.category-filter input', function(){
 		var $this = $(this);
		if( $this.prop('checked') ){
			var val = $this.val();
			$cfWrapper.html('<div class="text-center cf-loader"><i class="aficon-circle-notch aficon-spin"></i></div>');
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				data: {
					action: 'adifier_get_cf_filter',
					category_id: val
				},
				success: function(response){
					$cfWrapper.html( response );
					startACF();
				}
			});
		}
		else{
			$cfWrapper.html('');
		}
	});

	/* on orderby change */
	$(document).on('change', '.orderby', function(){
		submitForm();
	});

	/* pagination click */
	$(document).on('click', '.pagination a', function(e){
		e.preventDefault();
		submitForm( $(this).text() );
	});

	/* layout click */
	$(document).on('click', '.layout-view a', function(e){
		e.preventDefault();
		$('.layout-view a').removeClass( 'active' );
		$(this).addClass('active');
		submitForm();
	});	

	/* apply filter and fetch aresults */
	$('.filter-adverts').on('click',function(e){
		e.preventDefault();
		submitForm( 1 );
	});

	$('.search-form input[name="keyword"]').on('keypress',function(e){
    	if(e.which == 13 ) {
    		submitForm( 1 );
    	}		
	});	

	/* open sub levels of category */
	$(document).on('click', '.taxonomy-filter .styled-radio a', function(e){
		e.preventDefault();
		var $this = $(this);
		var $parent = $this.parent().parent();
		if( $this.hasClass('opened') ){
			$parent.find('ul').addClass('hidden');
			$parent.find('a').removeClass('opened');
			$this.removeClass('opened');
		}
		else{
			$this.addClass('opened');
			$parent.find('> ul').removeClass('hidden');
		}
	});

	$('.taxonomy-filter input').on('change', function(){
		var $this = $(this);
		$this.parent().find('a:not(.opened)').click();
	});

	var $hasChecked = $('.taxonomy-filter input:checked');
	if( $hasChecked.length > 0 ){
		$hasChecked.each(function(){
			$(this).parent().find('> a').click();
		});
	}

	/* submit form & populate new results */
	
	function submitForm( page ){
		var $form = $('.search-form'); 
		var action = $form.attr('action');
		var arrayData = $form.serializeArray();
		var data = '';
		if( arrayData ){
		    $.each( arrayData, function( i, field ) {
		    	if( field.value ){
		      		data += '&'+field.name+'='+field.value;
		      	}
		    });
		}
		var $holder = $('.ajax-search');
		var $orderby = $('.orderby');
		var $pagination = $('.pagination span');
		var $layout = $('.layout-view a.active');
		$holder.addClass('loading');
		if( $orderby.length > 0 ){
			data += '&af_orderby='+$orderby.val();
		}
		if( $pagination.length > 0 ){
			page = page ? page : $pagination.text();
			data += '&af_page='+page.toString().replace(/,/g, "");
		}
		if( $layout.length > 0 ){
			data += '&layout='+$layout.data('style');
		}

		if( data !== '' ){
			data = data.substr(1);
		}		
		$.ajax({
			url: action,
			method: 'POST',
			data: data,
			success: function(response){
				$('.ajax-search').html( $(response).find('.ajax-search').html() );
				$(document).trigger('adifier-new-search');
				history.pushState({
				    id: 'search'
				}, document.title, '?'+data);				
			},
			complete: function(){
				$holder.removeClass('loading');
				var scroll_to = $holder.offset().top;
				var $admin = $('#wpadminbar');
				var $sticky = $('.sticky-nav');
				if( $sticky.length > 0 ) {
					scroll_to -= $sticky.height();
				}
				if( $admin.length > 0 && $admin.css( 'position' ) == 'fixed' ){
					scroll_to -= $admin.height();
				}

				if( !searchWithMap ){
					$('html, body').animate({
					    scrollTop: scroll_to - 60
					}, 500);
				}
				else{
					if( $(window).width() <= 736 ){
						$('html, body').animate({
						    scrollTop: scroll_to
						}, 500);
					}
					else{
						$('.search-map-results-content').animate({
						    scrollTop: 0
						}, 500);
					}
				}
			}
		});
	}

	/* reset search form */
	$('.reset-search').on('click', function(e){
		e.preventDefault();
		$('.search-form select:not(#currency),input[type="text"],input[type="hidden"]').val('');
		$('input[type="checkbox"],input[type="radio"]').prop('checked', false);
		$('input[type="radio"][value=""]').prop('checked', true);
		$('.search-form select,input').trigger('change');
		$(document).trigger('aficon-reset');
		$('.taxonomy-filter .styled-radio a.opened').click();
		submitForm(1);
	});

	/* toggle more less */
	$('.toggle-more-less').on( 'click', function(e){
		e.preventDefault();
		var $this = $(this);
		if( !$this.hasClass('opened') ){
			$this.find('span').text( $this.data('less') );
			$this.addClass('opened');
			$this.parent().parent().find('> li.term-hidden').addClass('visible');
		}
		else{
			$this.parent().parent().find('> li.term-hidden').removeClass('visible');	
			$this.removeClass('opened');
			$this.find('span').text( $this.data('more') );
		}
	});
});