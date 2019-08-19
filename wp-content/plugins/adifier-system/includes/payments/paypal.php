<?php
if( !class_exists('Adifier_PayPal') ){
class Adifier_PayPal{

	/*
	Add paypal options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayPal', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayPal payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_paypal',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PayPal', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment PayPal', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'paypal_client_id',
                    'type'      => 'text',
                    'title'     => esc_html__('PayPal Client ID Token', 'adifier') ,
                    'desc'      => esc_html__('Input client ID token of your PayPal application', 'adifier'),
                ),
                array(
                    'id'        => 'paypal_secret',
                    'type'      => 'text',
                    'title'     => esc_html__('PayPal Secret Token', 'adifier') ,
                    'desc'      => esc_html__('Input secret token of your PayPal application', 'adifier'),
                ),
            )
        );

        return $sections;
	}

	/*
	Check payment method
	*/
	static public function start_payment(){
		if( self::is_enabled() ){
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_PayPal::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_PayPal::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_paypal', 'Adifier_PayPal::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PayPal::select_dropdown' );

			add_action( 'wp_ajax_paypal_create_payment', 'Adifier_PayPal::create_payment' );
			add_action( 'wp_ajax_paypal_execute_payment', 'Adifier_PayPal::execute_payment' );	
		}
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['paypal'] = esc_html__( 'PayPal', 'adifier' );
		return $dropdown;
	}

	/*
	Check if we can actually use paypal
	*/
	static public function is_enabled(){
		$enable_paypal = adifier_get_option( 'enable_paypal' );
		$paypal_client_id = adifier_get_option( 'paypal_client_id' );
		$paypal_secret = adifier_get_option( 'paypal_secret' );
		if( $enable_paypal == 'yes' && !empty( $paypal_client_id ) && !empty( $paypal_secret ) ){
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
		wp_enqueue_script('adifier-paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js', false, false, true);
		wp_enqueue_script('adifier-paypal', get_theme_file_uri( '/js/payments/paypal.js' ), array('jquery', 'adifier-purchase'), false, true);

		wp_enqueue_style( 'adifier-paypal', get_theme_file_uri( '/css/payments/paypal.css' ) );
	}

	/*
	Add paypal t the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<div id="paypal-button"></div>
			<div class="paypal-covering"></div>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$data = self::http( 'payments/sale/'.$order_transaction_id.'/refund' );
		if( !empty( $data->state ) && $data->state == 'completed' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	If REST Access Token is expired or not available get one
	*/
	static public function get_paypal_token(){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
		$adifier_paypal_token = get_option( 'adifier_paypal_token_'.$payment_enviroment );
		$adifier_paypal_token_expire = get_option( 'adifier_paypal_token_expire_'.$payment_enviroment );
		$timestamp = current_time( 'timestamp' );

		if( empty( $adifier_paypal_token_expire ) || $timestamp > $adifier_paypal_token_expire ){
			$auth = adifier_b64_encode( adifier_get_option( 'paypal_client_id' ) . ':' . adifier_get_option( 'paypal_secret' ) );
			$response = wp_remote_post( self::paypal_url().'oauth2/token', array(
				'headers' 		=> array(
					'Authorization' => 'Basic '.$auth,
				),
				'body' 			=> array(
					'grant_type'	=> 'client_credentials'
				),
			));	


			if ( is_wp_error( $response ) ) {
			} 
			else{			
			   	$data = json_decode( $response['body']);
				if( !empty( $data->expires_in ) ){
					update_option( 'adifier_paypal_token_expire', $timestamp + $data->expires_in );
					update_option( 'adifier_paypal_token', $data->access_token );
					$adifier_paypal_token = $data->access_token;
				}		   	
			}
		}

		return $adifier_paypal_token;
	}


	/*
	Create price
	*/
	static public function check_price( $price, $abbr ){
		$no_decimals = array( 'HUF', 'JPY', 'TWD' );
		if( in_array( $abbr, $no_decimals ) ){
			$price = number_format( $price, 0 );
		}

		return (string)$price;

	}

	/*
 	First we create payment which will user approve and send ID to JS script
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$currency_abbr = adifier_get_option( 'currency_abbr' );
			$data = self::http( 'payments/payment', array(
				'intent'			=> 'sale',
				'redirect_urls'		=> array(
					'return_url'		=> home_url('/'),
					'cancel_url'		=> home_url('/').'cancel',
				),
				'payer'				=> array(
					'payment_method' 	=> 'paypal'
				),
				'transactions'		=> array(
					array(
						'amount' => array(
							'total' 	=> self::check_price( $order['price'], $currency_abbr ),
							'currency'	=> $currency_abbr
						),
						'custom' => $order_id
					),
				)
			));

			if( !empty( $data->id ) ){
				echo json_encode( array( 'paymentID' => $data->id ) );
			}

			die();
		}
	}

	/*
	Once user have confirmed payment we can execute it
	*/
	static public function execute_payment(){
		$payment_id = $_POST['paymentID'];
		$data = self::http( 'payments/payment/'.$payment_id.'/execute', array(
			'payer_id' => $_POST['payerID']
		));

		if( !empty( $data->state ) && $data->state == 'approved' ){
			$response = Adifier_Order::create_order(array(
				'order_payment_type' 	=> 'paypal',
				'order_transaction_id' 	=> $data->transactions[0]->related_resources[0]->sale->id,
				'order_id'				=> $data->transactions[0]->custom,
				'order_paid'			=> 'yes'
			));
		}
		else{
			$response = array( 'error' => '<div class="alert-error">'.esc_html__( 'We were unable to process your payment at the moment', 'adifier' ).'</div>' );
		}

		echo json_encode( $response );
		die();
	}

	/*
	Build URL
	*/
	static public function paypal_url(){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
		return "https://api".( $payment_enviroment == 'test' ? '.sandbox' : '' ).".paypal.com/v1/";
	}

	/*
	HTTP calling
	*/
	static public function http( $checkpoint, $data = array() ){
	    $response = wp_remote_post( self::paypal_url().$checkpoint, array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Bearer '.self::get_paypal_token(),
	            'Content-Type'	=> 'application/json'
	        ),
	        'body' => !empty( $data ) ? json_encode( $data ) : '{}',
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
		   	return json_decode( $response['body']);		   	
		}
	}


}
add_filter( 'init', 'Adifier_PayPal::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PayPal::register_in_options' );
}

?>