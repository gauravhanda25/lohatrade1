<?php
if( !class_exists('Adifier_Order') ){
class Adifier_Order{

	/*
	* First assign hooks for order
	*/
	static public function initiate(){
		add_action( 'adifier_amb_action', 'Adifier_Order::refund_box' );
		add_action( 'save_post', 'Adifier_Order::order_save', 10, 3 );
		add_action( 'before_delete_post', 'Adifier_Order::order_delete', 10, 1 );
		add_action( 'wp_ajax_package_payment_amount', 'Adifier_Order::order_amount');
		add_action( 'admin_notices', 'Adifier_Order::notice');
		add_filter( 'manage_ad-order_posts_columns', 'Adifier_Order::admin_columns' );
		add_action( 'manage_ad-order_posts_custom_column' , 'Adifier_Order::admin_columns_values', 10, 2 );
		add_action( 'admin_enqueue_scripts', 'Adifier_Order::adifier_number_manuel_order', 2 );
		
	}

	static public function adifier_number_manuel_order() {
	    global $wpdb;
	    $count = $wpdb->get_var("SELECT COUNT(meta_id) FROM {$wpdb->postmeta} WHERE meta_key = 'order_paid' AND meta_value = 'no'");
	    wp_localize_script( 'adifier-admin', 'adifier_admin_order', array( 'order-count' => $count ));
	}


	/*
	* Add custom columns to orders custom post type
	*/
	static public function admin_columns( $columns ) {
		$columns = array_slice($columns, 0, 3, true) + array("paid" => esc_html__( 'Paid', 'adifier' ) ) + array_slice($columns, 3, count($columns) - 1, true) ;    
	    return $columns;
	}

	/*
	* print values for column
	*/
	static public function admin_columns_values( $column, $post_id ){
	    switch ( $column ) {

	        case 'paid' :
	        	$paid = get_post_meta( $post_id, 'order_paid', true );
	        	switch( $paid ){
	        		case 'no' 	: esc_html_e( 'No', 'adifier' ); break;
	        		case 'yes' 	: esc_html_e( 'Yes', 'adifier' ); break;
	        	}
	        	break;
	    }
	}

	/*
	* Add refund box to order post type
	*/
	static public function refund_box(){
		adifier_amb( 'order-refund', esc_html__( 'Refund', 'adifier' ), 'Adifier_Order::refund_box_callback', 'ad-order', 'side', 'high'  );
		adifier_amb( 'order-products', esc_html__( 'Products', 'adifier' ), 'Adifier_Order::products_box_callback', 'ad-order' );
		adifier_amb( 'order-buyer', esc_html__( 'Buyer Details', 'adifier' ), 'Adifier_Order::buyer_box_callback', 'ad-order', 'side', 'high' );
	}

	/*
	* Print refund box on order custom post type
	*/
	static public function refund_box_callback( $post ){
		$order_payment_type = get_post_meta( $post->ID, 'order_payment_type', true );
		$manual_refunds = apply_filters( 'adifier_manual_refund_list', array() );
		if( in_array( $order_payment_type, $manual_refunds ) ){
			?>
			<p><?php esc_html_e( 'Manual money return is required. Action below will only remove products from the ad/user', 'adifier' ) ?></p>
			<?php
		}
		?>
		<p>
			<input type="checkbox" name="adifier_refund_order" id="adifier_refund_order">
			<label for="adifier_refund_order"><?php esc_html_e( "Refund This Order?", 'adifier' ); ?></label>
		</p>
		<?php
	}

	static public function get_order_details( $order_id ){
		$_order_json_details = get_post_meta( $order_id, '_order_json_details', true );
		return $_order_json_details;
	}

	/*
	* Print list of the purchased products
	*/
	static public function products_box_callback( $post ){
		$order_details = self::get_order_details( $post->ID );
		self::order_detail_info( $order_details );
	}

	/*
	* Print details of the order
	*/
	static public function order_detail_info( $order_details ){
		if( !empty( $order_details['products'] ) ){
			$tax = !empty( $order_details['tax'] ) ? 1 + $order_details['tax'] / 100 : '';			
			?>
			<div class="invoice-details <?php echo !empty( $tax ) ? 'has-tax' : '' ?>">
				<div class="flex-wrap details-title">
					<div><?php esc_html_e( 'Description', 'adifier' ) ?></div>
					<div><?php esc_html_e( 'Quantity', 'adifier' ) ?></div>
					<?php if( !empty( $tax ) ){
						?>
						<div><?php esc_html_e( 'Price', 'adifier' ) ?></div>
						<div><?php echo $order_details['tax_name'] ?></div>
						<?php
					}?>
					<div><?php esc_html_e( 'Amount', 'adifier' ) ?></div>
				</div>
				<?php
				foreach( $order_details['products'] as $product ){
					?>
					<div class="flex-wrap product-item">
						<div>
							<?php
							if( $product['id'] == 'packages' ) {
								esc_html_e( 'Package: ', 'adifier' );
							}
							else if( $product['id'] == 'subscriptions' ){
								esc_html_e( 'Subscription: ', 'adifier' );
							}
							else if( $product['id'] == 'hybrids' ){
								esc_html_e( 'Product: ', 'adifier' );
							}							
							else{
								echo get_the_title( $order_details['advert_id'] ).' - ';
							}
							echo esc_html( $product['name'] );
							?>
						</div>
						<div>1</div>
						<?php if( !empty( $tax ) ){
							?>
							<div class="unit-price">
								<?php echo adifier_price_format( $product['price'] / $tax ); ?>
							</div>
							<div class="tax-perc">
								<?php echo adifier_price_format( $product['price'] - $product['price'] / $tax ); ?>
							</div>
							<?php
						}
						?>
						<div class="price">
							<?php 
							echo adifier_price_format( $product['price'] );
							?>
						</div>
					</div>
					<?php
				}
				if( !empty( $tax ) ){
					?>
					<div class="flex-wrap total-line product-item">
						<div><?php echo esc_html__( 'Total ', 'adifier' ).$order_details['tax_name'] ?></div>
						<div></div>
						<div class="price">
							<?php echo adifier_price_format( $order_details['price'] - $order_details['price'] / $tax ); ?>
						</div>
					</div>				
					<?php
				}
				?>
				<div class="flex-wrap total-line product-item">
					<div><?php echo esc_html__( 'Total Amount', 'adifier' ) ?></div>
					<div></div>
					<div class="price">
						<?php echo adifier_price_format( $order_details['price'] ); ?>
					</div>
				</div>					
			</div>
			<?php
		}
	}

	/*
	* Print buyer details
	*/
	static public function buyer_box_callback( $post ){
		$buyer_location 	= get_user_meta( $post->post_author, 'location', true );
		$buyer_name			= get_the_author_meta( 'display_name', $post->post_author );
		if( empty( $buyer_location ) ){
			$buyer_location = get_post_meta( $post->ID, 'buyer_location', true );
			$buyer_name 	= get_post_meta( $post->ID, 'buyer_name', true );
		}

		echo !empty( $buyer_name )  				? '<p class="no-margin">'.$buyer_name.'</p>' : '';
		echo !empty( $buyer_location['street'] ) 	? '<p class="no-margin">'.$buyer_location['street'].'</p>' : '';
		echo !empty( $buyer_location['city'] ) 		? '<p class="no-margin">'.$buyer_location['city'].'</p>' : '';
		echo !empty( $buyer_location['state'] ) 	? '<p class="no-margin">'.$buyer_location['state'].'</p>' : '';
		echo !empty( $buyer_location['country'] ) 	? '<p class="no-margin">'.$buyer_location['country'].'</p>' : '';
	}

	/*
	* On order save check if it is refunded and if so remove products from account/advert
	*/
	static public function order_save( $post_id, $post, $update ){
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || 'trash' === $post->post_status ){
			return;
		}

		$post_type = get_post_type($post_id);
	    if ( "ad-order" != $post_type || !isset( $_POST['order_paid'] ) ){
	    	return;
	    }

		if( !isset( $_POST['adifier_refund_order'] ) ){

			$order_paid = $_POST['order_paid'];
			$order_paid = array_shift( $order_paid );

		    $old_paid = get_post_meta( $post_id, 'order_paid', true );

		    if( ( empty( $old_paid ) || $old_paid == 'no' ) && $order_paid == 'yes' ){
		    	delete_post_meta( $post_id, 'order_refunded' );
		    	$order_details = self::get_order_details( $post_id );
		    	self::mail_order( $order_details, $post_id );
		    	self::apply_products( $order_details );
		    }
		    else if( ( !empty( $old_paid ) && $old_paid == 'yes' ) && $order_paid == 'no' ){
		    	self::undo_products( self::get_order_details( $post_id ) );
		    }
		}
		else{
			$is_refunded = get_post_meta( $post_id, 'order_refunded', true );
			if( empty( $is_refunded ) ){			
				$order_payment_type = $_POST['order_payment_type'];
				$order_payment_type = array_shift( $order_payment_type );

				$order_transaction_id = $_POST['order_transaction_id'];
				$order_transaction_id = array_shift( $order_transaction_id );

				do_action( 'adifier_refund_'.$order_payment_type, $post, $order_transaction_id );
			}
		}
	}

	/*
	* On delete remove onhold metas for posts
	*/
	static public function order_delete( $post_id ){
		global $post_type;   
		if ( $post_type != 'ad-order' ){
			return;
		}

		$order_details = self::get_order_details( $post_id );
		if( !empty( $order_details['advert_id'] ) ){
			delete_post_meta( $order_details['advert_id'], 'adifier_products_onhold' );
		}
	}
	/*
	* Mark order as refunded and mark it in postmeta
	*/
	static public function mark_as_refunded( $post ){
		self::undo_products( self::get_order_details( $post->ID ) );
		update_post_meta( $post->ID, 'order_refunded', 1 );;
		wp_update_post(array(
			'ID'			=> $post->ID,
			'post_title'	=> $post->post_title.' '.esc_html__( 'Refunded', 'adifier' )
		));
	}

	/*
	* Apply account packages
	*/
	static private function _apply_package( $user_id, $adverts ){
		$current_adverts = self::get_user_package_adverts( $user_id );
		self::_save_package_value( $user_id, $current_adverts + $adverts );
	}

	/*
	* Reverse account packages
	*/
	static private function _reverse_package( $user_id, $adverts ){
		$current_adverts = self::get_user_package_adverts( $user_id );
		$reduced = $current_adverts - $adverts;
		$reduced = $reduced < 0 ? 0 : $reduced;
		self::_save_package_value( $user_id, $reduced );
	}

	/*
	* Save adverts meta
	*/
	static private function _save_package_value( $user_id, $adverts ){
		update_user_meta( $user_id, 'af_adverts', $adverts );
	}

	/*
	* Apply account subscription
	*/
	static private function _apply_subscription( $user_id, $time ){
		$subscribe = self::get_user_package_subscription( $user_id );
		$subscribe = $subscribe == 0 ? current_time( 'timestamp' ) : $subscribe;
		self::_save_subscription_value( $user_id, $subscribe + $time );
	}	

	/*
	* Reverse account subscription
	*/
	static private function _reverse_subscription( $user_id, $reverse_time ){
		$subscribe = self::get_user_package_subscription( $user_id );
		$reduced = $subscribe - $reverse_time;
		$reduced = $reduced < current_time('timestamp') ? 0 : $reduced;
		self::_save_subscription_value( $user_id, $reduced );
	}		

	/*
	* Save subscription valuve
	*/
	static function _save_subscription_value( $user_id, $time ){
		update_user_meta( $user_id, 'af_subscribe', $time );
	}

	/*
	* Apply order after verification
	*/
	static public function apply_after_verification( $order_id ){
		$order_verified = get_post_meta( $order_id, 'order_verified', true );
		if( $order_verified !== 'yes' ){
			update_post_meta( $order_id, 'order_verified', 'yes' );
			update_post_meta( $order_id, 'order_paid', 'yes' );
			$order_details = self::get_order_details( $order_id );
			self::mail_order( $order_details, $order_id );
			self::apply_products( $order_details );
		}
	}

	/*
	* Once the order is refunded remove applied promotions and account payments
	*/
	static public function undo_products( $order ){
		$promotions = adifier_available_promotions();
		foreach( $order['products'] as $product ){
			if( !empty( $promotions[$product['id']] ) ){
				if( !empty( $promotions[$product['id']]['handler'] ) ){
					call_user_func( $promotions[$product['id']]['handler'], $order, $product, 'remove' );
				}
				else{				
					if( $promotions[$product['id']]['value_holder'] == 'meta_value' ){
						delete_post_meta( $order['advert_id'], $product['id'] );	
					}
					else{
						adifier_save_advert_meta( $order['advert_id'], str_replace( 'promo_', '', $product['id'] ), 0 );	
					}
				}
			}
			else if( $product['id'] == 'packages' ){
				self::_reverse_package( $order['userId'], $product['adverts'] );
			}
			else if( $product['id'] == 'subscriptions' ){
				self::_reverse_subscription( $order['userId'], $product['days']*86400 );
			}
			else if( $product['id'] == 'hybrids' ){
				self::_reverse_package( $order['userId'], $product['adverts'] );
				self::_reverse_subscription( $order['userId'], self::_get_hybrid_time( $product['days'] ) );
			}
		}
	}

	static public function get_user_package_adverts( $user_id ){
		return (int)get_user_meta( $user_id, 'af_adverts', true );
	}

	static public function get_user_package_subscription( $user_id ){
		return (int)get_user_meta( $user_id, 'af_subscribe', true );
	}

	static private function _get_hybrid_time( $target ){
		$temp = explode( '+', $target );
		if( !empty( $temp[1] ) ){
			return $temp[1]*3600;
		}
		else{
			return $target*86400;
		}
	}

	/*
	* On payment apply promotions and account payments
	*/
	static public function apply_products( $order ){
		$response = array();
		$promotions = adifier_available_promotions();
		foreach( $order['products'] as $product ){
			if( !empty( $promotions[$product['id']] ) ){
				/* if promotion has some functions to handle it */
				if( !empty( $promotions[$product['id']]['handler'] ) ){
					call_user_func( $promotions[$product['id']]['handler'], $order, $product, 'apply' );
				}
				else{
					$time = current_time('timestamp')+( $product['days'] * 86400 );
					if( $promotions[$product['id']]['value_holder'] == 'meta_value' ){
						update_post_meta( $order['advert_id'], $product['id'], $time );
					}
					else{
						adifier_save_advert_meta( $order['advert_id'], str_replace( 'promo_', '', $product['id'] ), $time );	
					}
				}
				$response['promotion'][$product['id']] = sprintf( esc_html__( '(Active until: %s)', 'adifier' ), date_i18n( get_option( 'date_format' ), $time ) );
			}
			else if( $product['id'] == 'packages' ){
				self::_apply_package( $order['userId'], $product['adverts'] );
			}
			else if( $product['id'] == 'subscriptions' ){
				self::_apply_subscription( $order['userId'], $product['days'] * 86400 );
			}
			else if( $product['id'] == 'hybrids' ){
				self::_apply_package( $order['userId'], $product['adverts'] );
				self::_apply_subscription( $order['userId'], self::_get_hybrid_time( $product['days'] ) );
			}
		}
		if( !empty( $order['advert_id'] ) ){
			delete_post_meta( $order['advert_id'], 'adifier_products_onhold' );
		}

		return $response;
	}

	/*
	* Send mail with invoice
	*/
	static public function mail_order( $order, $order_id ){
		$order_html = '';
		$tax = !empty( $order['tax'] ) ? 1 + $order['tax'] / 100 : '';
		ob_start();
		foreach( $order['products'] as $product ){
			?>
	        <tr>
	            <td width="<?php echo !empty( $tax ) ? '40%' : '70%' ?>" <?php echo !empty( $tax ) ? '' : 'colspan="3"' ?> style="padding:10px 20px; border-bottom: 1px solid #f8f8f8">
					<?php
					if( $product['id'] == 'packages' ) {
						esc_html_e( 'Package: ', 'adifier' );
					}
					else if( $product['id'] == 'subscriptions' ){
						esc_html_e( 'Subscription: ', 'adifier' );
					}
					else if( $product['id'] == 'hybrids' ){
						esc_html_e( 'Product: ', 'adifier' );
					}					
					else{
						esc_html_e( 'Promotion: ', 'adifier' );
					}
					echo esc_html( $product['name'] );
					?>
				</td>
				<td width="15%" style="padding:10px 20px; border-bottom: 1px solid #f8f8f8" align="right">
					1
				</td>
				<?php if( !empty( $tax ) ){
					?>
					<td width="15%" style="padding:10px 20px; border-bottom: 1px solid #f8f8f8" align="right">
						<?php echo adifier_price_format( $product['price'] / $tax ); ?>
					</td>					
					<td width="15%" style="padding:10px 20px; border-bottom: 1px solid #f8f8f8" align="right">
						<?php echo adifier_price_format( $product['price'] - $product['price'] / $tax ); ?>
					</td>
					<?php
				}
				?>
				<td width="15%" style="padding:10px 20px; border-bottom: 1px solid #f8f8f8" align="right">
					<?php echo adifier_price_format( $product['price'] ); ?>
				</td>
			</tr>
			<?php
		}
		$order_html = ob_get_contents();
		ob_end_clean();

		ob_start();
		include( get_theme_file_path( 'includes/emails/invoice.php' ) );
		$message_email = ob_get_contents();
		ob_end_clean();

		$order_number = get_post_meta( $order_id, 'order_number', true );

		$user_id = get_post_field( 'post_author', $order_id );
		$user = get_userdata( $user_id );
		adifier_send_mail( $user->user_email, esc_html__( 'Invoice - ', 'adifier' ).$order_number, $message_email );		
	}

	/*
	* When payments which needs to be cleared are used then run this function to inform user that he already ordered promotion so he does not pay twice
	* also create meta for that advert which will be deleted once the payment is cleared
	*/
	static public function apply_products_onhold( $order ){
		$response = array();
		$promotions = adifier_available_promotions();
		$onhold = (array)get_post_meta( get_the_ID(), 'adifier_products_onhold', true );
		foreach( $order['products'] as $product ){
			if( !empty( $promotions[$product['id']] ) ){
				$onhold[] = $product['id'];
				$response['promotion'][$product['id']] = esc_html__( '(Waiting For Payment)', 'adifier' );
			}
		}
		if( !empty( $onhold ) ){
			update_post_meta( $order['advert_id'], 'adifier_products_onhold', $onhold );
		}

		return $response;
	}

	static public function check_onhold( $promotion ){
		$onhold = get_post_meta( get_the_ID(), 'adifier_products_onhold', true );
		if( !empty( $onhold ) && in_array( $promotion, $onhold ) ){
			return esc_html__( '(Waiting For Payment)', 'adifier' );
		}
		else{
			return '';
		}
	}

	/*
	* When payment method is selected create transient which will then be used for creating order
	*/
	static public function create_transient( $order ){
		$invoice_num_start = adifier_get_option( 'invoice_num_start' );
		if( !empty( $invoice_num_start ) ){
			$invoice_num_prefix = adifier_get_option( 'invoice_num_prefix' );
			$invoice_num_sufix = adifier_get_option( 'invoice_num_sufix' );

			$order_id = $invoice_num_prefix.$invoice_num_start.$invoice_num_sufix;
			$invoice_num_start++;
			Redux::setOption('adifier_options','invoice_num_start', $invoice_num_start);
		}
		else{
			$order_id = current_time( 'timestamp' );
		}
		//$order_id = 'order_'.$order_name;
		set_transient( $order_id, $order, 345600 );

		return $order_id;
	}

	/*
	* Create actuall order
	*/
	static public function create_order( $args ){
		$order = get_transient( $args['order_id'] );
		//$order_number = str_replace( 'order_', '', $args['order_id'] );
		$order_number = $args['order_id'];
		$response = array();

		$post_id = wp_insert_post(array(
			'post_type'		=> 'ad-order',
			'post_status'	=> 'publish',
			'post_title'	=> esc_html__( 'Order', 'adifier' ).' #'.$order_number,
			'post_author'	=> $order['userId']
		));

		if( !empty( $post_id ) ){
			delete_transient( $args['order_id'] );
			update_post_meta( $post_id, 'order_payment_type', $args['order_payment_type'] );
			if( !empty( $args['order_transaction_id'] ) ){
				update_post_meta( $post_id, 'order_transaction_id', $args['order_transaction_id'] );
			}
			update_post_meta( $post_id, 'order_paid', $args['order_paid'] );
			update_post_meta( $post_id, 'order_number', $order_number );
			update_post_meta( $post_id, '_order_json_details', $order );

			if( $args['order_paid'] == 'yes' ){
				self::mail_order( $order, $post_id );
				$response = self::apply_products( $order );
			}
			else if( !empty( $order['advert_id'] ) ){
				$response = self::apply_products_onhold( $order );
			}

			return array_merge( $response, array( 'success' => '<div class="alert-success">'.esc_html__( 'Your order is completed', 'adifier' ).'</div>', 'order_id' => $post_id ) );
		}
		else{
			return array( 'error' => '<div class="alert-success">'.esc_html__( 'We could not create your order, contact administration', 'adifier' ).'</div>' );
		}
	}

	/*
	* On payment select create order with its details
	*/
	static public function get_order(){
		$order = json_decode( stripcslashes( $_POST['order'] ), true );
		if( $order['type'] == 'acc_pay' ){
			$account_payment = adifier_get_option( 'account_payment' );
			$packs = adifier_get_packs( $account_payment );
			if( !empty( $packs[$order['list']] ) ){
				$packs[$order['list']]['id'] = $account_payment;
				$products = array( $packs[$order['list']] );
			}			
		}
		else{
			$products = array();
			foreach( $order['list'] as $promotion_id => $pack_id ){
				$promotion = adifier_get_promotion( $promotion_id );
				if( !empty( $promotion ) ){
					unset( $promotion['desc'] );
					$promotion['days'] = $promotion['packs'][$pack_id]['days'];
					$promotion['price'] = $promotion['packs'][$pack_id]['price'];
					unset( $promotion['packs'] );
					$days = $promotion['days'];
					$promotion['name'] = $promotion['name'].( !empty( $promotion['days'] ) ? ' ('.esc_html__( 'For', 'adifier' ).' '.sprintf( _n( '%s day', '%s days', $days, 'adifier' ), $promotion['days'] ).')' : '' );
					$promotion['id'] = $promotion_id;
					$products[] = $promotion;
				}
			}
		}

		unset( $order['list'] );
		$order['products'] = $products;
		$order['price'] = self::get_order_price( $products );
		$order['tax'] = adifier_get_option( 'tax' );
		$order['tax_name'] = adifier_get_option( 'tax_name' ).' ('.$order['tax'].'%)';

		return $order;
	}

	/*
	* Grab order amount
	*/
	static public function get_order_price( $products ){
		if( !is_user_logged_in() ){
			return false;
		}	
		
		$price = 0;

		if( !empty( $products ) ){
			foreach( $products as $product ){
				$price += $product['price'];
			}
		}

		return number_format( $price, 2, '.', '' );
	}

	/*
	* Add notice
	*/
	static public function notice(){
		$notice = get_transient( 'adifier_order_notice' );
		if( !empty( $notice ) ){
			foreach( $notice['errors'] as $error ){
				?>
				<div class="notice notice-<?php echo esc_attr( $notice['notice'] ) ?> is-dismissible">
				    <p><?php echo $error ?></p>
				</div>
				<?php
			}
		}
		delete_transient( 'adifier_order_notice' );
	}

	/*
	* Get order by meta order ID
	*/
	static public function get_order_by_transaction_id( $transaction_id ){
		global $wpdb;
		$res = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value = %s", $transaction_id));

		return !empty( $res ) ? $res : false;
	}
}
add_action( 'init', 'Adifier_Order::initiate' );
}
?>