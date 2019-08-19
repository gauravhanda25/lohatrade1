<?php
/*
* Get number of favorite adverts
*/
if( !function_exists('adifier_count_favorited') ){
function adifier_count_favorited(){
	global $wpdb;
	$count = 0;
	$favorited = get_user_meta( get_current_user_id(), 'favorites_ads', true);
	if( !empty( $favorited ) ){
		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE ID IN (".esc_sql( join( ',', $favorited ) ).") AND post_type = 'advert'");
	}

	return $count;
}
}

/*
* Get id and title of user ad
*/
if( !function_exists('adifier_id_title_ad_chart') ){
function adifier_id_title_ad_chart(){
	global $wpdb;
	$items = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_author = %d AND post_type = 'advert' AND post_status = 'publish'", get_current_user_id()));

	$data = array();
	if( !empty( $items ) ){
		foreach( $items as $item  ){
			$data[$item->ID] = $item->post_title;
		}
	}

	return $data;
}
}

/*
* Retrive chart data by post ID
*/
if( !function_exists('adifier_chart_data_by_id') ){
function adifier_chart_data_by_id(){
	global $wpdb;
	$labels = array();
	$data = array();
	$views_data = $wpdb->get_var($wpdb->prepare("SELECT views_data FROM {$wpdb->prefix}adifier_advert_data WHERE post_id = %d", $_POST['advert_id']));
	if( !empty( $views_data ) ){
		$views_data = json_decode( $views_data );
		foreach( $views_data as $timestamp => $count ){
			if( sizeof( $labels ) < 30 ){
				$labels[] = date_i18n( get_option( 'date_format' ), $timestamp );
				$data[] = absint( $count );
			}
			else{
				break;
			}
		}
	}

	$response = array(
		'empty' 	=> empty( $data ),
		'labels' 	=> $labels,
		'data'		=> $data,
		'max'		=> !empty( $data ) ? max(array_values( $data )) : 0
	);
	echo json_encode( $response );
	die();
}
add_action('wp_ajax_chart_data', 'adifier_chart_data_by_id');
add_action('wp_ajax_nopriv_chart_data', 'adifier_chart_data_by_id');
}

?>