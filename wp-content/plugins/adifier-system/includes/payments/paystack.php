<?php
if( !class_exists('Adifier_Paystack') ) {
class Adifier_Paystack{
	/*
	Add paystack options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Paystack', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Paystack payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_paystack',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Paystack', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Paystack', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'paystack_secret_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Paystack Secret Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paystack secret key. Make sure taht you input test key here if you want to test gateway first', 'adifier')
                ),
                array(
                    'id' 		=> 'paystack_public_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Paystack Public Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paystack public key. Make sure taht you input test key here if you want to test gateway first', 'adifier')
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Paystack::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Paystack::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_paystack', 'Adifier_Paystack::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_Paystack::select_dropdown' );

			add_action('wp_ajax_paystack_create_payment', 'Adifier_Paystack::create_payment');
			add_action('wp_ajax_paystack_execute_payment', 'Adifier_Paystack::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['paystack'] = esc_html__( 'Paystack', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if paystack is configured and enabled
	*/
	static public function is_enabled(){
		$enable_paystack = adifier_get_option( 'enable_paystack' );
		$paystack_secret_key = adifier_get_option( 'paystack_secret_key' );
		$paystack_public_key = adifier_get_option( 'paystack_public_key' );
		if( $enable_paystack == 'yes' && !empty( $paystack_secret_key ) && !empty( $paystack_public_key ) ){
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
		wp_enqueue_script('adifier-paystack-checkout', 'https://js.paystack.co/v1/inline.js', false, false, true);
		wp_enqueue_script('adifier-paystack', get_theme_file_uri( '/js/payments/paystack.js' ), array('jquery', 'adifier-purchase'), false, true);

		wp_enqueue_style( 'adifier-paystack', get_theme_file_uri( '/css/payments/paystack.css' ) );
	}

	/*
	Add paystack to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="paystack-button">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/paystack.png' ) ); ?>" alt="paystack" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
	    $data = self::http( 'refund', 'POST', array(
		    'transaction' => $order_transaction_id,
		));

	    if( !empty( $data->data ) && !empty( $data->data->transaction ) && in_array( $data->data->transaction->status, array( 'reversed', 'processed' ) ) ){
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

			$user = wp_get_current_user();
			$response = array(
				'email'			=> $user->user_email,
				'key'			=> adifier_get_option( 'paystack_public_key' ),
				'amount' 		=> $order['price'] * 100,
				'order_id'		=> $order_id,
				'currency'		=> adifier_get_option( 'currency_abbr' ),
			);
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
		$reference = $_POST['reference'];
		$order_id = $_POST['order_id'];
		$order = get_transient( $order_id );

		if( !empty( $reference ) ){
			if( !empty( $order ) ){
			    $data = self::http( 'transaction/verify/'.$reference, 'GET');

			    if( !empty( $data ) ){
			    	if( !empty( $data->data ) ){
			    		if( !empty( $data->data->status ) && $data->data->status == 'success' ){
							$response = Adifier_Order::create_order(array(
								'order_payment_type' 	=> 'paystack',
								'order_transaction_id' 	=> $data->data->id,
								'order_id'				=> $order_id,
								'order_paid'			=> 'yes'
							));
						}
					    else{
					    	$response = array( 'error' => esc_html__( 'Transaction was not successful: Last gateway response was: ', 'adifier' ).$data->data->gateway_response );
					    }						
					}
					else{
						$response = array( 'error' => $data->message );
					}					
			    }
				else{
					$response = array( 'error' => esc_html__( 'Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable', 'adifier' ) );
				}			    
			}
			else{
				$response = array( 'error' => esc_html__( 'Your order has expired', 'adifier' ) );
			}
		}
		else{
			$response = array( 'error' => esc_html__( 'Reference is invalid', 'adifier' ) );
		}		
	    
		echo json_encode( $response );
		die();    
	}

	/*
	To http post for stripe
	*/
	static public function http( $checkpoint, $method = 'POST', $data = array() ){
	    $response = wp_remote_post( 'https://api.paystack.co/'.$checkpoint, array(
	        'method' => $method,
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Bearer '.adifier_get_option( 'paystack_secret_key' )
	        ),
	        'body' => $data,
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{			
		   	return json_decode( $response['body']);		   	
		}
	}

}
add_filter( 'init', 'Adifier_Paystack::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Paystack::register_in_options' );
}
?>