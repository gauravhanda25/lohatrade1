jQuery(document).ready(function($){
	"use strict";
	/*
	* handle sizes of divs
	*/
	var $searchForm = $('.search-map-form');
	var $mapResults = $('.search-map-results');
	var $searchMap = $('.search-map');
	if( $(window).width() > 736 ){
		$('.search-map-results-content, .search-form').scrollbar();
	}
	function checkHeight(){
		var height = $(window).height() - $('header').outerHeight() - ( $('#wpadminbar').length > 0 ? $('#wpadminbar').height() : 0 );
		$mapResults.height( height );
		$searchMap.height( height );
		$searchForm.height( height );
	}

	if( $(window).width() > 736 ){
		checkHeight();
		$(window).resize(function(){
			checkHeight();
		});
	}

	/* MAP PART */
	function showMarkers(){
		var data = [];
		$('.search-map-la-long').each(function(){

			var $this = $(this);
			var $parent = $this.parents('.advert-item');
	        var $contentString = $('<div></div>');

	        $contentString.append( $parent.find('.advert-media').clone() );
	        $contentString.append( $parent.find('.adv-title').clone() );
	        $contentString.find('h5').wrap('<div class="flex-right"></div>');
	        $contentString.find('.flex-right').append( $parent.find('.bottom-advert-meta').clone() );
	        $contentString.find('a').addClass('text-overflow').attr('target', '_blank');

			data.push({
				latitude	: $this.data('latitude'),
				longitude	: $this.data('longitude'),
				id 			: $this.data('id'),
				icon		: $this.data('icon'),
				iconwidth	: $this.data('iconwidth'),
				iconheight	: $this.data('iconheight'),
				content 	: $contentString.html(),
				parent 		: $parent
			});

		});

		if( data ){
			$(document).trigger('adifier-search-map-start', [data]);
		}
	}

	$(document).trigger('adifier-search-map-events');

	function clearMap(){
		$(document).trigger('adifier-search-map-clear');
	}

	$(document).trigger('adifier-search-map-launch', [$searchMap]);
	showMarkers();
	$(document).on('adifier-new-search', function(){
		clearMap();
		showMarkers();
	});
});