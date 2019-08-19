<?php
/*
* Fetch auction IDs where user participates
*/
if( !function_exists('adifier_get_auctions_ids') ){
function adifier_get_auctions_ids(){
	return get_user_meta( get_current_user_id(), 'adifier_auctions', true );
}
}

/*
* Get minimum bidding price for the auction
*/
if( !function_exists('adifier_min_bid_price') ){
function adifier_min_bid_price( $advert_id = 0 ){
	$advert_id = empty( $advert_id ) ? get_the_ID() : $advert_id;
	$bidding_step = adifier_get_option( 'bidding_step' );
	return adifier_get_advert_meta( $advert_id, 'price', true ) + $bidding_step;
}
}


/*
* Save user bid
*/
if( !function_exists('adifier_place_bid') ){
function adifier_place_bid(){
	global $wpdb;
	if( is_user_logged_in() ){
		$user_id = get_current_user_id();
		$bid = !empty( $_POST['bid'] ) ? $_POST['bid'] : '';
		$advert_id = !empty( $_POST['advert_id'] ) ? $_POST['advert_id'] : '';

		$currency = adifier_get_advert_meta( $advert_id, 'currency', true );
		if( !empty( $bid ) && adifier_validate_price_format( $bid, $currency ) ){
			if( !adifier_is_expired() ){
				$min_bid = adifier_min_bid_price( $advert_id );
				$bid = adifier_mysql_format_price( $bid, $currency );
				if( $bid >= $min_bid ){
					$latest_bid_user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}adifier_bids WHERE post_id = %d ORDER BY created DESC LIMIT 1", $advert_id));
					if( !empty( $latest_bid_user_id ) && $latest_bid_user_id != $user_id ){
						$user = get_user_by( 'ID', $latest_bid_user_id );
						$name = adifier_author_name( $user );
						$title = get_the_title( $advert_id );
						$link = get_the_permalink( $advert_id );
						ob_start();
						include( get_theme_file_path( 'includes/emails/outbid.php' ) );
						$message = ob_get_contents();
						ob_end_clean();
						adifier_send_mail( $user->user_email, __( 'Auction Bid - You\'ve Been Outbid', 'adifier' ), $message );						

					}
					$bid_id = $wpdb->insert(
						$wpdb->prefix.'adifier_bids',
						array(
							'post_id'	=> $advert_id,
							'user_id'	=> $user_id,
							'bid'		=> $bid,
							'ip'		=> adifier_get_IP(),
							'created'	=> current_time( 'timestamp' )
						),
						array(
							'%d',
							'%d',
							'%f',
							'%s',
							'%s'
						)
					);

					if( $bid_id ){
						$bids = adifier_get_advert_meta( $advert_id, 'bids', true );
						$bids++;
						adifier_save_advert_meta( $advert_id, 'bids', $bids );
						adifier_save_advert_meta( $advert_id, 'price', $bid );
						adifier_clear_advert_meta( $advert_id );

						$auctions = (array)get_user_meta( $user_id, 'adifier_auctions', true );
						if( !in_array( $advert_id, $auctions ) ){
							$auctions[] = $advert_id;
							update_user_meta( $user_id, 'adifier_auctions', $auctions );
						}

						$response['message'] = '<div class="alert-success">'.esc_html__( 'Bid is placed', 'adifier' ).'</div>';
					}
					else{
						$response['message'] = '<div class="alert-error">'.esc_html__( 'Could not place the bid', 'adifier' ).'</div>';
					}
				}
				else{
					$response['message'] = '<div class="alert-error">'.sprintf( esc_html__( 'Bid must be at least %s', 'adifier' ), adifier_price_format( $min_bid, $currency )).'</div>';
				}
			}
			else{
				$response['message'] = '<div class="alert-error">'.esc_html__( 'Auction has ended', 'adifier' ).'</div>';
			}
			$response['price'] = adifier_get_advert_price( $advert_id );
			$response['min_bid'] = $bid >= $min_bid ? adifier_min_bid_price( $advert_id ) : $min_bid;
			$response['min_bid_text'] = sprintf( esc_html__( 'Min bid: %s', 'adifier' ), strip_tags( adifier_price_format( $response['min_bid'], $currency ) ) );
		}
		else if( !adifier_validate_price_format( $bid, $currency ) ){
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Bid format is invalid', 'adifier' ).adifier_acceptable_price_formats( $currency ).'</div>';
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Input bid value', 'adifier' ).'</div>';
		}
	}

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_adifier_place_bid', 'adifier_place_bid' );
}

/*
* Print bidding history
*/
if( !function_exists( 'adifier_print_bidding_history' ) ){
function adifier_print_bidding_history( $advert_id = '', $page = 1, $full = false, $ip = false, $per_page = 20){
	global $wpdb;
	$response['message'] = '';

	$offset = ( $page - 1 ) * $per_page;
	$results = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}adifier_bids AS ab LEFT JOIN {$wpdb->users} AS u ON ab.user_id = u.ID WHERE post_id = %d ORDER BY created DESC LIMIT %d OFFSET %d", $advert_id, $per_page, $offset));
	$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

	if( !empty( $results ) ){
		ob_start();
		$currency = adifier_get_advert_meta( $advert_id, 'currency', true );
		foreach( $results as $result ) {
			
			?>
			<div class="flex-wrap flex-center">
				<div>
					<?php 
						if( $full ){
							?>
							<a href="<?php echo Adifier_Conversations::has_conversation_started( $result->user_id, get_post_field( 'post_author', $advert_id ), $advert_id ) ?>" class="contact-buyer" data-buyer_id="<?php echo esc_attr( $result->ID ) ?>">
								<i class="aficon-envelope"></i>
							</a>
							<?php								
							echo esc_html( $result->display_name );
						}
						else{
							echo substr( $result->user_login, 0, 1 ).'*'.substr( $result->user_login, -1 );
						}
					?>
				</div>
				<?php if( $ip ): ?>
					<div class="bid-ip">
						<?php echo esc_html( $result->ip ) ?>
					</div>
				<?php endif; ?>					
				<div>
					<?php echo adifier_price_format( $result->bid, $currency ) ?>
				</div>
				<div>
					<?php echo date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ), $result->created ) ?>
				</div>
			</div>
			<?php

		}
		$response['message'] = ob_get_contents();
		ob_end_clean();		
	}		

	$max_pages = ceil( $total / $per_page );
	if( $max_pages > $page ){
		$response['next_page'] = $page+1;
		$response['btn_text'] = esc_html__( 'Load More', 'adifier' );
	}	

	return $response;
}
}

/*
* Get bidding history
*/
if( !function_exists('adifier_bid_history') ){
function adifier_bid_history(){
	if( !is_user_logged_in() ){
		return false;
	}
	global $wpdb;
	$advert_id = !empty( $_POST['advert_id'] ) ? $_POST['advert_id'] : '';
	$page = !empty( $_POST['bidpage'] ) ? $_POST['bidpage'] : 1;
	$full = !empty( $_POST['full'] ) ? true : false;
	$ip = !empty( $_POST['ip'] ) ? true : false;

	if( !empty( $advert_id ) ){
		echo json_encode( adifier_print_bidding_history( $advert_id, $page, $full, $ip ) );
	}
	
	die();
}
add_action( 'wp_ajax_adifier_bid_history', 'adifier_bid_history' );
}


/*
* Get ended auctions which are not proccessed and send mails to seler and buyer with information and next directions
*/
if( !function_exists('adifier_process_auctions') ){
function adifier_process_auctions(){
	global $wpdb;
	$adifier_auction_processing_ids = get_transient( 'adifier_auction_processing_ids' );
		$query = $wpdb->prepare("SELECT p.ID, post_author, post_title, currency, price, start_price FROM {$wpdb->posts} as p LEFT JOIN {$wpdb->prefix}adifier_advert_data AS aad ON p.ID = aad.post_id WHERE aad.expire < %d AND aad.expire <> 0 AND type = 2 AND exp_info <> 1", current_time('timestamp'));
	if( !empty( $adifier_auction_processing_ids ) ){
		$query .= " AND p.ID NOT IN (".esc_sql( join(',', $adifier_auction_processing_ids) ).")";
	}
	$query .= " ORDER BY expire ASC LIMIT 30";
	$auctions = $wpdb->get_results( $query );
	if( !empty( $auctions ) ){
		if( empty( $adifier_auction_processing_ids ) ){
			$ids = wp_list_pluck( $auctions, 'ID' );
			set_transient( 'adifier_auction_processing_ids', $ids, 60 );
		}
		
		foreach( $auctions as $auction ){
			$flag = false;
			$seller = get_user_by( 'ID', $auction->post_author );
			$reserved_price = get_post_meta( $auction->ID, 'advert_reserved_price', true );
			if( empty( $auction->price ) || ( !empty( $reserved_price ) && $auction->price < $reserved_price ) ){
				$flag = true;
			}
			if( !$flag ){
				$buyer_bid = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}adifier_bids WHERE post_id = %d AND bid = %s LIMIT 1", $auction->ID, $auction->price ) );
				$buyer = get_user_by( 'ID', $buyer_bid->user_id );
				$buyer_name = adifier_author_name( $buyer );

				ob_start();
				include( get_theme_file_path( 'includes/emails/auction-seller.php' ) );
				$message = ob_get_contents();
				ob_end_clean();
				adifier_send_mail( $seller->user_email, esc_html__( 'Auction Ended - Contact Buyer', 'adifier' ), $message );
				
				ob_start();
				include( get_theme_file_path( 'includes/emails/auction-buyer.php' ) );
				$message = ob_get_contents();
				ob_end_clean();
				adifier_send_mail( $buyer->user_email, esc_html__( 'Auction Ended - You Won', 'adifier' ), $message );
			}
			else{
				if( !empty( $reserved_price ) ){
					$reserved_price = adifier_price_format( $reserved_price, $auction->currency );
				}
			
				ob_start();
				include( get_theme_file_path( 'includes/emails/auction-failed.php' ) );
				$message = ob_get_contents();
				ob_end_clean();
				adifier_send_mail( $seller->user_email, esc_html__( 'Auction Ended - Failed', 'adifier' ), $message );
			}

			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}adifier_advert_data SET exp_info = 1 WHERE post_id = %d", $auction->ID));
		}
	}
}
add_action( 'wp_head', 'adifier_process_auctions' );
}
?>