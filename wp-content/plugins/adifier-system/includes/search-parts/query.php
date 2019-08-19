<?php
global $wp;
$currency_location	= adifier_get_option( 'currency_location' );
$currency_symbol 	= adifier_get_option( 'currency_symbol' );
$radius_max 		= adifier_get_option( 'radius_max' );
$per_page 			= adifier_get_option( 'adverts_per_page' );

$keyword 			= !empty( $_REQUEST['keyword'] ) 			? $_REQUEST['keyword'] 			: '';
$category 			= !empty( $_REQUEST['category'] ) 			? $_REQUEST['category'] 		: '';
$location_id 		= !empty( $_REQUEST['location_id'] ) 		? $_REQUEST['location_id'] 		: '';
$cf_fields			= !empty( $_REQUEST['cf_fields'] ) 			? $_REQUEST['cf_fields'] 		: array();
$latitude 			= !empty( $_REQUEST['latitude'] ) 			? $_REQUEST['latitude'] 		: '';
$longitude 			= !empty( $_REQUEST['longitude'] ) 			? $_REQUEST['longitude'] 		: '';
$location 			= !empty( $_REQUEST['location'] ) 			? $_REQUEST['location'] 		: '';
$radius 			= isset( $_REQUEST['radius'] ) 				? $_REQUEST['radius'] 			: $radius_max;
$price 				= !empty( $_REQUEST['price'] ) 				? $_REQUEST['price'] 			: '';
$currency 			= !empty( $_REQUEST['currency'] ) 			? $_REQUEST['currency'] 		: '';
$type 				= !empty( $_REQUEST['type'] ) 				? $_REQUEST['type'] 			: '';
$cond 				= !empty( $_REQUEST['cond'] ) 				? $_REQUEST['cond'] 			: '';
$image_only 		= !empty( $_REQUEST['image-only'] ) 		? true 							: false;
$urgent_only 		= !empty( $_REQUEST['urgent-only'] ) 		? true 							: false;
$page 				= !empty( $_REQUEST['af_page'] ) 			? $_REQUEST['af_page'] 			: 1;
$orderby 			= !empty( $_REQUEST['af_orderby'] ) 		? $_REQUEST['af_orderby'] 		: '';
$layout 			= !empty( $_REQUEST['layout'] ) 			? $_REQUEST['layout'] 			: adifier_get_option( 'default_search_listing' );

$args = array(
	'post_status'		=> 'publish',
	'paged'				=> $page,
	'include_top_ads'	=> true,
	'orderby'			=> 'date',
	'order'				=> 'DESC',
	'tax_query'			=> array()
);		

if( $result_listing == 2 ){
	$per_page = 8;
	$args['posts_per_page'] = 8;
}

if( !empty( $orderby ) ){
	$orderby_val = explode( '-', $orderby );
	$args['orderby'] = $orderby_val[0];
	$args['order'] = $orderby_val[1];
}

if( !empty( $keyword ) ){
	$args['s'] = $keyword;
}

if( !empty( $category ) ){
	$args['tax_query'][] = array(
		'taxonomy'	=> 'advert-category',
		'terms'		=> $category
	);
	if( !empty( $cf_fields ) ){
		$args['tax_query_between'] = array();
		$fields = Adifier_Custom_Fields_Search::get_fields_by_category_id( $category );
		foreach( $fields as $field ){
			if( !empty( $cf_fields[$field->cf_slug] ) ){
				$values = is_array( $cf_fields[$field->cf_slug] ) ? array_filter( $cf_fields[$field->cf_slug] ) : $cf_fields[$field->cf_slug];
				if( !empty( $values ) ){
					$cf_filter = array(
						'taxonomy'	=> $field->cf_slug
					);
					if( in_array( $field->cf_type, array( 1, 2, 6, 7, 9, 10 ) ) ){
						$cf_filter['terms'] = $values;
						$args['tax_query'][] = $cf_filter;
					}
					else if( $field->cf_type == 3 ){
						if( !empty( $values['min'] ) && !empty( $values['max'] ) ){
							$cf_filter['terms']	 = array( strtotime( $values['min'] ), strtotime( $values['max'] ) );
							$args['tax_query_between'][] = $cf_filter;
						}
					}
					else if( $field->cf_type == 4 ){
						$cf_filter['terms']	 = explode( ',', $values );
						$args['tax_query_between'][] = $cf_filter;
					}
					else if( $field->cf_type == 5 ){
						$cf_filter = array(
							'relation' => 'AND'
						);
						foreach( $values as $value ){
							$cf_filter[] = array(
								'taxonomy'	=> $field->cf_slug,
								'terms'		=> $value
							);
						}
						
						$args['tax_query'][] = $cf_filter;
					}
					else if( $field->cf_type == 8 ){
						if( isset( $values['min'] ) || isset( $values['max'] ) ){
							$cf_filter['terms']	 = array( isset( $values['min'] ) ? $values['min'] : '', isset( $values['max'] ) ? $values['max'] : '' );
							$args['tax_query_between'][] = $cf_filter;
						}
					}					
				}
			}
		}
	}					
}

if( !empty( $location_id ) ){
	$args['tax_query'][] = array(
		'taxonomy'	=> 'advert-location',
		'terms'		=> $location_id
	);
}

$map_source = adifier_get_option( 'map_source' );
if( $map_source == 'mapbox' ){
	if( !empty( $location ) ){
		$response = wp_remote_get( 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$location.'.json?limit=1&access_token='.adifier_get_option( 'google_api_key' ) );
		if ( is_wp_error( $response ) ) {
		} 
		else{
		   	$data = json_decode( $response['body'] );
		   	if( !empty( $data->features ) ){
		   		$longitude = $data->features[0]->geometry->coordinates[0];
		   		$latitude = $data->features[0]->geometry->coordinates[1];
		   	}
		}		
	}
}

if( !empty( $location ) && !empty( $longitude ) && !empty( $latitude ) ){
	$args['location'] = array(
		'latitude' 	=> $latitude,
		'longitude'	=> $longitude,
		'radius'	=> $radius
	);
}

if( !empty( $type ) ){
	$args['type'] = $type;
}

if( !empty( $cond ) ){
	$args['cond'] = $cond;
}

if( !empty( $price ) ){
	$price = !is_array( $price ) ? explode( ',', $price ) : $price;
	$args['price'] = array(
		!empty( $price[0] ) ? Adifier_Advert_Query::normalize_search_price( adifier_mysql_format_price( $price[0], $currency ), $currency ) : 0,
		!empty( $price[1] ) ? Adifier_Advert_Query::normalize_search_price( adifier_mysql_format_price( $price[1], $currency ), $currency ) : ''
	);
}

if( !empty( $image_only ) ){
	$args['meta_query'] = array( 
		array(
			'key' => '_thumbnail_id'
		) 
	);
}

if( !empty( $urgent_only ) ){
	$args['urgent'] = true;
}

if( !is_tax() ){
	$adverts = new Adifier_Advert_Query( $args );
}

$pagination = paginate_links( array(
	'prev_next' 	=> true,
	'format'		=> '?af_page=%#%',
	'end_size' 		=> 2,
	'mid_size' 		=> 2,
	'total' 		=> $adverts->max_num_pages,
	'current' 		=> $page,
	'prev_next' 	=> false,
));
?>