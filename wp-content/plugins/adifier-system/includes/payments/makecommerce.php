<?php
if( !class_exists('Adifier_MakeCommerce') ) {
class Adifier_MakeCommerce{
	/*
	Add makecommerce options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Make Commerce', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Make Commerce payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_makecommerce',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Make Commerce', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Make Commerce', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'mc_shop_id',
                    'type'      => 'text',
                    'title'     => esc_html__('Shop ID', 'adifier') ,
                    'desc'      => esc_html__('Input your makecommerce Shop ID', 'adifier'),
                ),
                array(
                    'id'        => 'mc_api_secret',
                    'type'      => 'text',
                    'title'     => esc_html__('API Secret Key', 'adifier') ,
                    'desc'      => esc_html__('Input your api secret key', 'adifier'),
                ),
                array(
                    'id'        => 'mc_api_public',
                    'type'      => 'text',
                    'title'     => esc_html__('API Publishable Key', 'adifier') ,
                    'desc'      => esc_html__('Input your api publishable key', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_MakeCommerce::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_MakeCommerce::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_makecommerce', 'Adifier_MakeCommerce::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_MakeCommerce::select_dropdown' );

			add_action('wp_ajax_makecommerce_create_payment', 'Adifier_MakeCommerce::create_payment');
			add_action('wp_ajax_makecommerce_execute_payment', 'Adifier_MakeCommerce::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['makecommerce'] = esc_html__( 'Make Commerce', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if makecommerce is configured and enabled
	*/
	static public function is_enabled(){
		$enable_makecommerce = adifier_get_option( 'enable_makecommerce' );
		$mc_shop_id = adifier_get_option( 'mc_shop_id' );
		$mc_api_secret = adifier_get_option( 'mc_api_secret' );
		$mc_api_public = adifier_get_option( 'mc_api_public' );
		if( $enable_makecommerce == 'yes' && !empty( $mc_shop_id ) && !empty( $mc_api_secret ) && !empty( $mc_api_public ) ){
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
		wp_enqueue_script('makecommerce-checkout', 'https://payment'.( adifier_get_option( 'payment_enviroment' ) == 'test' ? '-test' : '' ).'.maksekeskus.ee/checkout/dist/checkout.min.js', false, false, true);
		wp_enqueue_script('adifier-makecommerce', get_theme_file_uri( '/js/payments/makecommerce.js' ), array('jquery', 'makecommerce-checkout', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-makecommerce', get_theme_file_uri( '/css/payments/makecommerce.css' ) );
	}

	/*
	Add makecommerce to the list of the available payments in the frontend
	*/
	static public function render(){
		$pk_client_id = adifier_get_option( 'pk_client_id' );
		?>	
		<li>
			<a href="javascript:void(0);" id="makecommerce-button">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/makecommerce.png' ) ); ?>" alt="makecommerce" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$_order_json_details = Adifier_Order::get_order_details( $order->ID );
	    $data = self::http( '/transactions/'.$order_transaction_id.'/refunds', array(
	    	'amount'	=> $_order_json_details['price'],
	    	'comment'	=> esc_html__( 'Refund', 'adifier' ),
	    ));

		if( !empty( $data->status ) && $data->status == 'SETTLED' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$data = array(
				'key'			=> adifier_get_option( 'mc_api_public' ),
				'amount' 		=> $order['price'],
				'currency'		=> adifier_get_option( 'currency_abbr' ),
				'completed'		=> 'makecommerce_complete',
				'cancelled'		=> 'makecommerce_cancel',
			);

			$user = wp_get_current_user();
			$transaction = self::http( 'transactions', array(
				'transaction' => array(
					'amount' 	=> $data['amount'],
					'currency'	=> $data['currency'],
					'reference'	=> $order_id
				),
			    'customer' => array(
			        'email' => $user->user_email,
			        'ip' 	=> $_SERVER['REMOTE_ADDR'],
			    )			
			));

			if( !empty( $transaction->id ) ){
				$data['transaction'] = $transaction->id;
				$response = $data;
			}
			else{
				$response['error'] = esc_html__( 'Can not create transaction at this point', 'adifier' );
			}
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

	/*
	First we create payment which will user approve and send ID to JS script
	*/
	static public function execute_payment(){
		$data = $_POST['data'];
		if( !empty( $data ) ){
			if( $data['transaction']['status'] == 'COMPLETED' ){
				$response = Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'makecommerce',
					'order_transaction_id' 	=> $data['transaction']['id'],
					'order_id'				=> $data['transaction']['reference'],
					'order_paid'			=> 'yes'
				));
			}
			else{
				$response = array( 'error' => esc_html__( 'Transaction is not paid', 'adifier' ) );
			}
		}
		else{
			$response = array( 'error' => esc_html__( 'Transaction is not paid', 'adifier' ) );
		}		
	    
		echo json_encode( $response );
		die();    
	}

	/*
	To http post for makecommerce
	*/
	static public function http( $checkpoint, $data ){
	    $response = wp_remote_request( 'https://api'.( adifier_get_option( 'payment_enviroment' ) == 'test' ? '-test' : '' ).'.maksekeskus.ee/v1/'.$checkpoint, array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Basic '.base64_encode( adifier_get_option( 'mc_shop_id' ).':'.adifier_get_option( 'mc_api_secret' ) ),
	            'Content-type' => 'application/json'
	        ),
	        'body' => json_encode( $data ),
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{			
		   	return json_decode( $response['body']);		   	
		}
	}

}
add_filter( 'init', 'Adifier_MakeCommerce::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_MakeCommerce::register_in_options' );
}
?>