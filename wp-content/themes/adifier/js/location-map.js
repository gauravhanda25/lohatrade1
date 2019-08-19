jQuery(document).ready(function($){
	"use strict";

    var $location = $('.location-map');
    if( $location.length > 0 ){

        var markerUrl = $location.data('icon') ? $location.data('icon') : adifier_data.marker_icon;
        var iconwidth = '';
        var iconheight = '';
        if( $location.data('icon') && $location.data('iconwidth') && $location.data('iconheight') ){
            iconwidth = $location.data('iconwidth');
            iconheight = $location.data('iconheight');
        }
        else if( adifier_data.marker_icon && adifier_data.marker_icon_width && adifier_data.marker_icon_height ){
            iconwidth = adifier_data.marker_icon_width;
            iconheight = adifier_data.marker_icon_height;
        }

        if( adifier_map_data.map_source == 'google' ){
            var location = new google.maps.LatLng( $location.data('lat'), $location.data('long') );
            var map = new google.maps.Map($location[0], {
                zoom: 15,
                center: location,
                styles: adifier_data.map_style ? JSON.parse( adifier_data.map_style ) : ''
            });

            var icon = markerUrl;
            if( icon !== '' ){
                icon = {
                    url: markerUrl,
                    size: iconwidth ? new google.maps.Size( iconwidth / 2, iconheight / 2 ) : '',
                    scaledSize: iconwidth ? new google.maps.Size( iconwidth / 2, iconheight / 2 ) : ''
                };
            }

            var marker = new google.maps.Marker({
                position: location,
                map: map,
                icon: icon,
                title: ''
            });
        }
        else{

            if( markerUrl == '' ){
                markerUrl = adifier_mapbox_data.default_marker;
                iconwidth = 55;
                iconheight = 55;
            }

            mapboxgl.accessToken = adifier_mapbox_data.api;
            var location = new mapboxgl.LngLat( $location.data('long'), $location.data('lat') );
            var map = new mapboxgl.Map({
                container: $location[0],
                zoom: 15,
                center: location,
                style: adifier_data.map_style ? adifier_data.map_style : 'mapbox://styles/mapbox/light-v9'
            });     

            var el = '';
            if( markerUrl !== '' ){
                el = document.createElement('div');
                el.className = 'mapboxgl-marker';
                el.style.backgroundSize = 'contain';
                el.style.backgroundImage = 'url('+markerUrl+')';
                el.style.width =  iconwidth + 'px';
                el.style.height = iconheight + 'px';
            }

            var marker = new mapboxgl.Marker(el).setLngLat(location).addTo(map);
        }
    }
})