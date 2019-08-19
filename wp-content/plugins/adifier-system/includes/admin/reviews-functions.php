<?php
/*
* Add admin menu item for reviews
*/
if( !function_exists('adifier_admin_reviews_menu') ){
function adifier_admin_reviews_menu(){
    adifier_menu_page( esc_html__('Reviews','adifier'), esc_html__('Reviews','adifier'), 'edit_posts', 'admin_reviews', 'adifier_admin_reviews');
}
add_action('admin_menu', 'adifier_admin_reviews_menu');
}


/*
* List reviews
*/
if( !function_exists('adifier_admin_reviews') ){
function adifier_admin_reviews(){
	global $wpdb;

	$review_ids = !empty( $_POST['review_ids'] ) ? $_POST['review_ids'] : array();
	if( !empty( $review_ids ) ){
		$reviewed_ids = $wpdb->get_col("SELECT reviewed_id FROM {$wpdb->prefix}adifier_reviews WHERE review_id IN (".esc_sql( join( ',', $review_ids ) ).")");
		$wpdb->query( "DELETE FROM {$wpdb->prefix}adifier_reviews WHERE review_id IN (".esc_sql( join( ',', $review_ids ) ).") OR parent IN (".esc_sql( join( ',', $review_ids ) ).")" );
		if( !empty( $reviewed_ids ) ){
			foreach( $reviewed_ids as $reviewed_id ){
				adifier_calculate_user_review( $reviewed_id );
			}
		}
		$message = '<div class="updated notice is-dismissible"><p>'.esc_html__( 'Reviews removed', 'adifier' ).'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}

	$paged = !empty( $_GET['paged'] ) ? $_GET['paged'] : 1;
	$per_page = 30;
	$offset = ( $paged - 1 ) * $per_page;

	$reviews = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS ar.*, ar2.review AS response FROM {$wpdb->prefix}adifier_reviews AS ar LEFT JOIN {$wpdb->prefix}adifier_reviews AS ar2 ON ar.review_id = ar2.parent WHERE ar.parent = 0 ORDER BY ar.created DESC LIMIT %d OFFSET %d", $per_page, $offset ) );
	$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

	$pagination = paginate_links( array(
	    'base' => add_query_arg( 'paged', '%#%' ),
	    'format' => '',
	    'prev_text' => '&laquo;',
	    'next_text' => '&raquo;',
	    'total' => ceil( $total / $per_page),
	    'current' => $paged
	));

	include( get_theme_file_path('includes/admin/reviews.php') );
}
}
?>