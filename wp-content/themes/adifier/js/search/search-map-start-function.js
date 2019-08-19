jQuery(document).ready(function($){
	"use strict";

	if( adifier_map_data.map_source == 'google' ){

		var bounds = new google.maps.LatLngBounds();
		var mapOptions = {
			center:    new google.maps.LatLng( 0,0 ),
			zoom:      3,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: adifier_data.map_style ? JSON.parse( adifier_data.map_style ) : ''
		};
		var infoBox = new InfoBox({
			enableEventPropagation: true,
			maxWidth: 350,
			infoBoxClearance: new google.maps.Size(50, 50),
			alignBottom: true,
			pixelOffset: new google.maps.Size(-47, -75)
		});	
		var markers = [];
		var addedIDs = [];
		var markerCluster;
		var map;

		$(document).on('adifier-search-map-launch', function(e, $mapHolder){
			map = new google.maps.Map( $mapHolder.get(0), mapOptions );
		});

		$(document).on( 'adifier-search-map-start', function(e, data){
			$.each(data, function(index, item) {
				var latlong = new google.maps.LatLng(item.latitude, item.longitude);
				var latLongKey = item.latitude+'-'+item.latitude;
				if( addedIDs.indexOf(item.id) == -1 ){
		        	addedIDs.push( item.id );
			        bounds.extend( latlong );
		            
			        var icon = item.icon;

		            if( icon !== '' ){
			            icon = {
			            	url: item.icon,
			            	size: ( item.iconwidth && item.iconheight ) ? new google.maps.Size(item.iconwidth/2, item.iconheight/2) : '',
			            	scaledSize: ( item.iconwidth && item.iconheight ) ? new google.maps.Size(item.iconwidth/2, item.iconheight/2) : ''
			            };
		        	}


			        var marker = new google.maps.Marker({
			            position: latlong,
			            map: map,
			            icon: icon,
			            content: item.content
			        });

			        markers.push(marker);

					marker.addListener('click', function() {
						infoBox.close();
						infoBox.setContent( marker.content );
						infoBox.setOptions({
						    pixelOffset: new google.maps.Size(-47, -75)
						});
						infoBox.open(map, marker);
					});

					if( item.parent ){
				        item.parent.on('mouseenter', function(){
				        	marker.setAnimation(google.maps.Animation.BOUNCE);
				        });

				        item.parent.on('mouseleave', function(){
				        	marker.setAnimation(null);
				        });

				        google.maps.event.addListener(marker, 'mouseover', function() {
				        	item.parent.addClass('marker-hovered');
				        });

				        google.maps.event.addListener(marker, 'mouseout', function() {
				        	item.parent.removeClass('marker-hovered');
				        });
					}
				}
			});

			markerCluster = new MarkerClusterer(map, markers, {
	            styles: [
	                {
	                    height: 55,
	                    url: adifier_data.url + "cluster.png",
	                    width: 55,
	                    textColor: '#ffffff'
	                }
	            ],
		        calculator: function (markers, numStyles) {
		            return {text: markers.length, index: 1};
		        }
			});

	        google.maps.event.addListener(markerCluster, 'click', function (cluster) {
	        	infoBox.close();
	            var markers = cluster.getMarkers();
	            var pos;
	            var samePosition = true;
	            for (var i = 0; i < markers.length; i++) {
	                if (!pos) {
	                    pos = markers[i].position;
	                }
	                else if (!pos.equals(markers[i].position)) {
	                    samePosition = false;
	                }
	            }
	            if (samePosition) {
			        var content = '<ul class="list-unstyled info-box-markers-list">';
			        var markers = cluster.getMarkers();
			        var addedMarkers = [];
					$.each(markers, function(index, marker) {
					    content += '<li>'+marker.content+'</li>';
					});
			        content += '</ul>';

			        infoBox.setContent(content);

					infoBox.setOptions({
					    pixelOffset: new google.maps.Size(-45, -50)
					});

			        infoBox.open(map, markers[markers.length - 1]);
			        setTimeout(function(){
			        	$('.info-box-markers-list').scrollbar();
			        }, 50);
			        
	                markerCluster.setZoomOnClick(false);
	            }
	        });		

			map.fitBounds(bounds);
			
		});

		$(document).on( 'adifier-search-map-clear', function(){
			$.each(markers, function(index, marker) {
				marker.setMap( null );	
			});
			infoBox.close();
			markerCluster.clearMarkers();
			markers = [];
			addedIDs = [];
			bounds = new google.maps.LatLngBounds();
		});

		$(document).on( 'adifier-search-map-events', function(){
		    $(document).on("mouseenter", '.infoBox', function(){
		        map.setOptions({
		        	draggable:false,
		        	scrollwheel: false
		        }); 
		    })
		    $(document).on("mouseleave", '.infoBox', function(){
		        map.setOptions({
		        	draggable:true,
		        	scrollwheel: true
		        }); 
		    });
		});
	}
	else{
		var markers = {};
		var pureMarkerList = [];
		var addedIDs = [];
		var bounds = new mapboxgl.LngLatBounds();
		var map;

		mapboxgl.accessToken = adifier_mapbox_data.api;

		$(document).on('adifier-search-map-launch', function(e, $mapHolder){
			map = new mapboxgl.Map({
				container: $mapHolder.get(0),
				style: adifier_data.map_style ? adifier_data.map_style : 'mapbox://styles/mapbox/light-v9'
			});
		});		

		$(document).on( 'adifier-search-map-start', function(e, data){
			$.each(data, function(index, item) {
				var lngLat = new mapboxgl.LngLat(item.longitude, item.latitude);
				var key = 'mrk-'+item.longitude+item.latitude;
				bounds.extend( lngLat );  
				if( addedIDs.indexOf(item.id) == -1 ){
		        	addedIDs.push( item.id );

		        	var el = '';
		        	if( item.icon !== '' ){
		            	el = document.createElement('div');
		            	el.className = 'mapboxgl-marker';
		            	el.style.backgroundSize = 'contain';
						el.style.backgroundImage = 'url('+( item.icon !== '' ? item.icon : adifier_mapbox_data.default_marker )+')';
						el.style.width =  ( item.icon !== '' ? item.iconwidth/2 : 55 ) + 'px';
						el.style.height = ( item.icon !== '' ? item.iconheight/2 : 55 ) + 'px';
		        	}

					var marker = new mapboxgl.Marker(el).setLngLat(lngLat).addTo(map);

					if( !markers[key] ){
						markers[key] = {
							markers: [],
							content: []
						};
					}
			        markers[key].markers.push(marker);
			        markers[key].content.push(item.content);	

			        pureMarkerList.push(marker);
				}
			});

			$.each( markers, function(index, data){
				if( data.markers.length > 1 ){
					$.each( data.markers, function(index, marker){
						var popup = new mapboxgl.Popup({ offset: 25 }).setHTML( '<div class="infoBox"><ul class="list-unstyled info-box-markers-list"><li>'+data.content.join( '</li><li>' )+'</li></ul></div>' );
						marker.setPopup( popup );		

						popup.on('open', function(){
							$('.info-box-markers-list').scrollbar();
						});
					});
				}
				else{
					var popup = new mapboxgl.Popup({ offset: 25 }).setHTML( '<div class="infoBox">'+data.content[0]+'</div>' );
					data.markers[0].setPopup( popup );
				}
			});

			map.fitBounds(bounds, {padding: 30, maxDuration: 1000});

		});	

		$(document).on( 'adifier-search-map-clear', function(){
			$.each(pureMarkerList, function(index, marker) {
				marker.remove();	
			});
			pureMarkerList = [];
			markers = {};
			addedIDs = [];
			bounds = new mapboxgl.LngLatBounds();
		});
	}

});