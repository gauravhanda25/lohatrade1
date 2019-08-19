<?php
/*
* Add link to conversations
*/
if( !function_exists('adifier_admin_messages_menu') ){
function adifier_admin_messages_menu(){
    adifier_menu_page( esc_html__('Conversations','adifier'), esc_html__('Conversations','adifier'), 'edit_posts', 'admin_conversations', 'adifier_admin_conversations');
}
add_action('admin_menu', 'adifier_admin_messages_menu');
}


/*
* Display messages
*/
if( !function_exists('adifier_admin_conversations') ){
function adifier_admin_conversations(){
	global $wpdb;
	$con_id = !empty( $_GET['con_id'] ) ? $_GET['con_id'] : '';

	/* if array with con_ids is submited then it means that we need to delte conversations and their messages */
	$con_ids = !empty( $_POST['con_ids'] ) ? $_POST['con_ids'] : array();
	if( !empty( $con_ids ) ){
		$wpdb->query( "DELETE ac, acm FROM {$wpdb->prefix}adifier_conversations AS ac LEFT JOIN {$wpdb->prefix}adifier_conversation_messages AS acm ON ac.con_id = acm.con_id WHERE ac.con_id IN (".esc_sql( join( ',', $con_ids ) ).")" );
		$message = '<div class="updated notice is-dismissible"><p>'.esc_html__( 'Conversations removed', 'adifier' ).'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}

	/* if array with message_ids is submitted then we need to delete messages from conversation */
	$message_ids = !empty( $_POST['message_ids'] ) ? $_POST['message_ids'] : array();
	if( !empty( $message_ids ) ){
		$wpdb->query( "DELETE FROM {$wpdb->prefix}adifier_conversation_messages WHERE message_id IN ( ".esc_sql( join( ',', $message_ids ) )." )" );
		$message = '<div class="updated notice is-dismissible"><p>'.esc_html__( 'Messages removed', 'adifier' ).'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}

	if( empty( $con_id ) ){
		$paged = !empty( $_GET['paged'] ) ? $_GET['paged'] : 1;
		$per_page = 30;
		$offset = ( $paged - 1 ) * $per_page;
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}adifier_conversations";
		if( !empty( $_POST['s'] ) ){
			$query .= " WHERE advert_title LIKE '%".esc_sql($_POST['s'])."%'";
		}
		$query .= $wpdb->prepare( " LIMIT %d OFFSET %d", $per_page, $offset );

		$conversations = $wpdb->get_results( $query );
		$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );
		
		$pagination = paginate_links( array(
		    'base' => add_query_arg( 'paged', '%#%' ),
		    'format' => '',
		    'prev_text' => '&laquo;',
		    'next_text' => '&raquo;',
		    'total' => ceil( $total / $per_page),
		    'current' => $paged
		));

		include( get_theme_file_path('includes/admin/conversations.php') );
	}
	else{
		$messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}adifier_conversation_messages WHERE con_id = %d ORDER BY created DESC", $con_id ) );
		include( get_theme_file_path('includes/admin/messages.php') );
	}
}
}
?>