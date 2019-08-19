<?php
if( !class_exists('Adifier_PayHere') ) {
class Adifier_PayHere{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayHere', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayHere payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_payhere',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PayHere', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via PayHere', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'payhere_merchant_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your PayHere merchant ID', 'adifier')
                ),
                array(
                    'id' 		=> 'payhere_merchant_secret',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant Secret', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your PayHere merchant secret key', 'adifier')
                )
            )
        );

        return $sections;
    }

	/*
	Check payment method
	*/
	static public function start_payment(){
		if( self::is_enabled() ){
            if( !empty( $_REQUEST['verify_payment'] ) && $_REQUEST['verify_payment'] == 'payhere' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_PayHere::enqueue_scripts' );
				add_action( 'wp_footer', 'Adifier_PayHere::add_modal' );
				add_action( 'adifier_payment_methods', 'Adifier_PayHere::render' );
			}

			add_action( 'wp_ajax_show_account_modal', 'Adifier_PayHere::show_account_modal' );
			add_action( 'wp_ajax_verify_account_modal', 'Adifier_PayHere::verify_account_modal' );

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_payhere', 'Adifier_PayHere::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PayHere::select_dropdown' );

			add_action( 'adifier_manual_refund_list', 'Adifier_PayHere::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'payhere';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['payhere'] = esc_html__( 'PayHere', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_payhere = adifier_get_option( 'enable_payhere' );
		$payhere_merchant_id = adifier_get_option( 'payhere_merchant_id' );
		$payhere_merchant_secret = adifier_get_option( 'payhere_merchant_secret' );
		if( $enable_payhere == 'yes' && !empty( $payhere_merchant_id ) && !empty( $payhere_merchant_secret ) ){
			return true;
		}
		else{
			return false;
		}
	}

	/*
	Add required scripts and styles
	*/
	static public function enqueue_scripts(){		
		wp_enqueue_script('adifier-payhere', get_theme_file_uri( '/js/payments/payhere.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-payhere', get_theme_file_uri( '/css/payments/payhere.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="payhere-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/payhere.png' ) ); ?>" alt="payhere" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		Adifier_Order::mark_as_refunded( $order );
	}

	/*
	Add modal
	*/
	static public function add_modal(){
		?>
		<div class="modal in" id="payhere" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="payhere-loader"><i class="aficon-spin aficon-circle-notch"></i></div>
					<form action="" id="payhere-form" method="post">

						<div class="modal-header">
							<h5 class="modal-title"><?php esc_html_e( 'Verify Your Data', 'adifier' ) ?></h5>
							<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
						</div>

						<div class="modal-body"></div>

						<div class="modal-footer">
							<div class="flex-left">
								<div class="ajax-form-result"></div>
							</div>
							<div class="flex-right">
								<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
								<input type="hidden" name="action" value="payhere_verify_data">
								<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Verify', 'adifier' ) ?> </a>
							</div>
						</div>
					</form>
				</div>

			</div>
		</div>		
		<?php
	}

	/*
	Verify modal data
	*/
	static public function verify_account_modal( $user_data = array() ){
		if( !empty( $_POST['data'] ) ){
			$flag = false;
			foreach( $_POST['data'] as $key => $value ){
				if( empty( $value ) ){
					$flag = true;
				}
				else{
					$user_data[$key] = $value;
				}
			}

			if( !$flag ){
				self::create_payment( $user_data );
			}
			else{
				$response['error'] = esc_html__( 'All fields are mandatory', 'adifier' );
			}
		}
		else{
			$response['error'] = esc_html__( 'All fields are mandatory', 'adifier' );
		}

		echo json_encode( $response );
		die();
	}

	/*
	Create data modal
	*/
	static public function show_account_modal(){
		$adifier_payhere_user_data = get_user_meta( get_current_user_id(), 'adifier_payhere_user_data', true );
		$user_data = get_userdata( get_current_user_id() );
		if( empty( $adifier_payhere_user_data ) ){
			$user_location = get_user_meta( get_current_user_id(), 'location', true );
			$adifier_payhere_user_data = array(
				'first_name' 	=> $user_data->first_name,
				'last_name' 	=> $user_data->last_name,
				'phone'			=> get_user_meta( get_current_user_id(), 'phone', true )
			);
			if( !empty( $user_location ) ){
				$adifier_payhere_user_data = array_merge( $adifier_payhere_user_data, array(
					'address' 	=> $user_location['street'],
					'city'		=> $user_location['city'],
					'country'	=> $user_location['country']
				));
			}
		}
		$adifier_payhere_user_data['email'] = $user_data->user_email;
		?>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="first_name" class="bold"><?php esc_html_e( 'First Name *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo esc_attr( $adifier_payhere_user_data['first_name'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>									
			</div>
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="last_name" class="bold"><?php esc_html_e( 'Last Name *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo esc_attr( $adifier_payhere_user_data['last_name'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="phone" class="bold"><?php esc_html_e( 'Phone *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="phone" name="phone" value="<?php echo esc_attr( $adifier_payhere_user_data['phone'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>									
			</div>
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="address" class="bold"><?php esc_html_e( 'Address *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="address" name="address" value="<?php echo esc_attr( $adifier_payhere_user_data['address'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="city" class="bold"><?php esc_html_e( 'City *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="city" name="city" value="<?php echo esc_attr( $adifier_payhere_user_data['city'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>									
			</div>
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label for="country" class="bold"><?php esc_html_e( 'Country *', 'adifier' ) ?></label>
					<input type="text" class="form-control" id="country" name="country" value="<?php echo esc_attr( $adifier_payhere_user_data['country'] ) ?>" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
				</div>
			</div>
		</div>	
		<input type="hidden" name="email" value="<?php echo esc_attr( $adifier_payhere_user_data['email'] ) ?>">
		<?php
		die();
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment( $user_data = array() ){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$payhere_merchant_id = adifier_get_option( 'payhere_merchant_id' );
			update_user_meta( get_current_user_id(), 'adifier_payhere_user_data', $user_data );

			$form = '';
			ob_start();
			$fields = array(
				'merchant_id'				=> esc_attr( $payhere_merchant_id ),
				'order_id'					=> esc_attr( $order_id ),
				'amount'					=> esc_attr( $order['price'] ),
				'items'						=> esc_attr( $order_id ),
				'currency'      			=> adifier_get_option( 'currency_abbr' ),
				'return_url'				=> $_POST['redirectUrl'].'#payhere-return',
				'cancel_url'				=> $_POST['redirectUrl'],
				'notify_url'				=> add_query_arg( 'verify_payment', 'payhere', site_url('/') ),

			);

			$fields = array_merge( $fields, $user_data );

			?>
			<form method="post" action="<?php echo esc_url( self::get_url() ) ?>pay/checkout" class="payhere-form">
				<?php

				foreach( $fields as $key => $value ){
					echo '<input name="'.$key.'" value="'.$value.'" type="hidden">';
				}
				?>
			</form>	
			<?php
			$form = ob_get_contents();
			ob_end_clean();

			$response = array(
				'form' => $form
			);		
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();
	}

    /*
    Verify payment of payhere
    */
    static public function verify_payment(){
    	$payhere_merchant_secret = adifier_get_option( 'payhere_merchant_secret' );
    	$local_md5sig = strtoupper( md5( $_POST['merchant_id'].$_POST['order_id'].$_POST['payhere_amount'].$_POST['payhere_currency'].$_POST['status_code'].strtoupper( md5( $payhere_merchant_secret )) ) );

    	if ( $local_md5sig === $_POST['md5sig'] && $_POST['status_code'] == 2 ){
			Adifier_Order::create_order(array(
				'order_payment_type' 	=> 'payhere',
				'order_transaction_id' 	=> $_POST['payment_id'],
				'order_id'				=> $_POST['order_id'],
				'order_paid'			=> 'yes'
			));
    	}
    }

    /*
	Get URL:
    */
    static public function get_url(){
    	$payment_enviroment = adifier_get_option( 'payment_enviroment' );
    	return 'https://'.( $payment_enviroment == 'test' ? 'sandbox' : 'www' ).'.payhere.lk/';
    }

}
add_filter( 'init', 'Adifier_PayHere::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PayHere::register_in_options' );
}
?>