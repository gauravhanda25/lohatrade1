/*jshint devel:true */
/*global google */

function AdifierMapInit( fieldEl ) {

	var searchInput = jQuery('.map-search', fieldEl ).get(0);
	var mapCanvas   = jQuery('.map-holder', fieldEl ).get(0);
	var latitude    = jQuery('input[name="lat"]', fieldEl );
	var longitude   = jQuery('input[name="long"]', fieldEl );
	var country   	= jQuery('input[name="country"]', fieldEl );
	var state   	= jQuery('input[name="state"]', fieldEl );
	var city   		= jQuery('input[name="city"]', fieldEl );
	var street   	= jQuery('input[name="street"]', fieldEl );


	if( adifier_map_data.map_source == 'google' ){

		var componentForm = {
			route: 'long_name',
			street_number: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'long_name',
			country: 'long_name',
		};		

		var mapOptions = {
			center:    new google.maps.LatLng( 0,0 ),
			zoom:      3,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		if( typeof adifier_data !== 'undefined' ){
			mapOptions.styles = adifier_data.map_style ? JSON.parse( adifier_data.map_style ) : '';
		}

		var map      = new google.maps.Map( mapCanvas, mapOptions );

		// Marker
		var markerOptions = {
			map: map,
			draggable: true
		};

		var marker = new google.maps.Marker( markerOptions );
		marker.setPosition( mapOptions.center );

		function setPosition( latLng, zoom ) {

			marker.setPosition( latLng );
			map.setCenter( latLng );

			if ( zoom ) {
				map.setZoom( zoom );
			}

			latitude.val( latLng.lat() );
			longitude.val( latLng.lng() );
		}

		// Set stored Coordinates
		if ( latitude.val() && longitude.val() ) {
			latLng = new google.maps.LatLng( latitude.val(), longitude.val() );
			setPosition( latLng, 17 )
		}

		google.maps.event.addListener( marker, 'dragend', function() {
			setPosition( marker.getPosition() );
		});

		// Search
		var autocomplete = new google.maps.places.Autocomplete(searchInput);
		if( typeof adifier_data !== 'undefined' ){
			if( adifier_data.country_restriction ){
				autocomplete.setComponentRestrictions({'country': adifier_data.country_restriction.split(',')});
			}
		}	
		autocomplete.bindTo('bounds', map);

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			}

			setPosition( place.geometry.location, 17 );

			var street_val = '';
			street.val('');
			for (var i = 0; i < place.address_components.length; i++) {
				var addressType = place.address_components[i].types[0];
				if (componentForm[addressType]) {
					var val = place.address_components[i][componentForm[addressType]];
					if( addressType == 'route' ){
						street_val = street.val();
						if( street_val ){
							val = adifier_data.address_order == 'front' ? val+' '+street_val : street_val+' '+val;
						}
						street.val( val );
					}
					if( addressType == 'street_number' ){
						street_val = street.val();
						if( street_val ){
							val = adifier_data.address_order == 'front' ? street_val+' '+val : val+' '+street_val;
						}
						street.val( val );
					}					
					else if( addressType == 'locality' ){
						city.val( val );
					}
					else if( addressType == 'administrative_area_level_1' ){
						state.val( val );	
					}
					else if( addressType == 'country' ){
						country.val( val );	
					}
				}
			}		

		});

		jQuery(searchInput).keypress(function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
			}
		});
	}
	else if( adifier_map_data.map_source == 'mapbox' ){
		mapboxgl.accessToken = adifier_mapbox_data.api;
		var map = new mapboxgl.Map({
			container: mapCanvas,
			style: typeof adifier_data !== 'undefined' && adifier_data.map_style ? adifier_data.map_style : 'mapbox://styles/mapbox/light-v9'
		});

		var geocoder = new MapboxGeocoder({
			accessToken: mapboxgl.accessToken,
			mapboxgl: mapboxgl,
			placeholder: adifier_mapbox_data.placeholder,
			marker: false
		});

		var marker = new mapboxgl.Marker({
			draggable: true
		}).setLngLat([0, 0]).addTo(map);		

		function setPosition( lngLat, zoom ) {
			map.setCenter( lngLat )
			marker.setLngLat( lngLat );

			if ( zoom ) {
				map.setZoom( zoom );
			}			

			latitude.val( lngLat.lat );
			longitude.val( lngLat.lng );
		}


		// Set stored Coordinates
		if ( latitude.val() && longitude.val() ) {
			var latLng = {
				lat: latitude.val(),
				lng: longitude.val()
			};
			setPosition( latLng, 17 );
		}

		geocoder.on('result', function( response ){
			if( response.result.place_type ){
				var types = [ 'address', 'place', 'region', 'country' ];
				var data = {
					address: '',
					place: '',
					region: '',
					country: ''
				};
				var start = types.indexOf( response.result.place_type[0] );
				if( start == -1 && response.result.place_type[0] == 'poi' ){
					start = 0;
					data[types[start]] = response.result.properties.address;
				}
				else{
					data[types[start]] = response.result.text;
				}
				for (var i = start+1; i < types.length; i++) {
					for ( var j=0; j<response.result.context.length; j++ ){
						if( response.result.context[j].id.indexOf( types[i] ) > -1 ){
							data[types[i]] = response.result.context[j].text;
						}
					}
				}

				street.val( data.address );
				city.val( data.place );
				state.val( data.region );
				country.val( data.country );

				setPosition( {lng: response.result.geometry.coordinates[0], lat: response.result.geometry.coordinates[1] } );
			}
		});


		marker.on('dragend', function(){
			setPosition( marker.getLngLat() );
		});
		 
		document.getElementById('map-search').appendChild(geocoder.onAdd(map));
	}

}

(function(jQuery) {

	var jQueryadifierMap = jQuery('.adifier-map');
	if( jQueryadifierMap.length > 0 && jQuery('.reveal-after').length == 0 ){
		jQuery(document).ready(function(){
			AdifierMapInit( jQueryadifierMap );
		});
	}			

}(jQuery));