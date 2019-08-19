<?php
if( !class_exists('Adifier_Paysafe') ) {
class Adifier_Paysafe{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Paysafe', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Paysafe payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_paysafe',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Paysafe', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Paysafe', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'paysafe_account_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Account ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paysafe account ID', 'adifier'),
                ),
                array(
                    'id' 		=> 'paysafe_username',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('API Key Username', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paysafe username', 'adifier'),
                ),
                array(
                    'id' 		=> 'paysafe_password',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('API KeyPassword', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paysafe password', 'adifier'),
                ),
                array(
                    'id' 		=> 'paysafe_sut_username',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Single-Use Token Username', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paysafe single-use token username', 'adifier'),
                ),
                array(
                    'id' 		=> 'paysafe_sut_password',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Single-Use Token Password', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Paysafe single-use token password', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Paysafe::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Paysafe::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_paysafe', 'Adifier_Paysafe::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_Paysafe::select_dropdown' );

			add_action( 'wp_ajax_paysafe_create_payment', 'Adifier_Paysafe::create_payment' );
			add_action( 'wp_ajax_paysafe_execute_payment', 'Adifier_Paysafe::execute_payment' );
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['paysafe'] = esc_html__( 'Paysafe', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_paysafe = adifier_get_option( 'enable_paysafe' );
		$paysafe_account_id = adifier_get_option( 'paysafe_account_id' );
		$paysafe_username = adifier_get_option( 'paysafe_username' );
		$paysafe_password = adifier_get_option( 'paysafe_password' );
		$paysafe_sut_username = adifier_get_option( 'paysafe_sut_username' );
		$paysafe_sut_password = adifier_get_option( 'paysafe_sut_password' );
		if( $enable_paysafe == 'yes' && !empty( $paysafe_account_id ) && !empty( $paysafe_username ) && !empty( $paysafe_password ) && !empty( $paysafe_sut_password ) ){
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
		wp_enqueue_script('adifier-paysafe-script', 'https://hosted.paysafe.com/checkout/1.4.0/paysafe.checkout.min.js', false, false, true);
		wp_enqueue_script('adifier-paysafe', get_theme_file_uri( '/js/payments/paysafe.js' ), array('jquery', 'adifier-purchase', 'adifier-paysafe-script'), false, true);
		wp_localize_script( 'adifier-paysafe', 'adifier_paysafe', array(
			'zip' => esc_html__( 'Enter your ZIP code', 'adifier' )
		));
		wp_enqueue_style( 'adifier-paysafe', get_theme_file_uri( '/css/payments/paysafe.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="paysafe-button">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/paysafe.png' ) ); ?>" alt="paysafe" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$adifier_paysafe_method = get_post_meta( $order->ID, 'adifier_paysafe_method', true );
		if( in_array( $adifier_paysafe_method, array( 'Cards', 'DirectDebit' ) ) ){
			$data = self::http('cardpayments/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/settlements/'.$order_transaction_id.'/refunds', array(
				'merchantRefNum' => time()
			));
		}
		else if( $adifier_paysafe_method == 'Interac' ){
			$data = self::http('/alternatepayments/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/payments/'.$order_transaction_id.'/refunds', array(
				'merchantRefNum' => time()
			));
		}

		if( !empty( $data->error ) && $data->error->code == '3406' ){
			$data = self::http('cardpayments/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/settlements/'.$order_transaction_id.'', array(
				'status' => 'CANCELLED'
			), 'PUT');
		}

		if( ( !empty( $data->status ) && in_array( $data->status, array( 'COMPLETED', 'PENDING', 'CANCELLED' ) ) ) ){
			Adifier_Order::mark_as_refunded( $order );
		}
		else{
			set_transient( 
				'adifier_order_notice', 
				array( 
					'errors' => array( $data->error->message ), 
					'notice' => 'error' 
				) 
			);			
		}
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){

			$response = array(
				'base64key' => adifier_b64_encode( adifier_get_option( 'paysafe_sut_username' ).':'.adifier_get_option( 'paysafe_sut_password' ) ),
				'order'		=> array(			
					'amount' 		=> $order['price'] * 100,
					'currency'		=> adifier_get_option( 'currency_abbr' ),
					'environment'	=> strtoupper( adifier_get_option( 'payment_enviroment' ) ),
					'companyName'	=> get_bloginfo( 'name' ),
					'order_id'		=> $order_id,
				)
			);
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

	/*
	Let's execute the payment depending on type Cards DirectDebit Interac
	*/
	static public function execute_payment(){
		$token = $_POST['token'];
		$order_id = $_POST['order_id'];
		$method = $_POST['paymentMethod'];
		$order = get_transient( $order_id );

		if( !empty( $token ) ){
			if( !empty( $order ) ){

				if( $method == 'Cards' ){
					$data = self::http( 'cardpayments/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/auths', array(
						'merchantRefNum' 	=> $order_id,
						'amount'			=> $order['price'] * 100,
						'settleWithAuth'	=> true,
						'card'				=> array(
							'paymentToken'	=> $token
						),
						'billingDetails'	=> array(
							'zip'	=> $_POST['zip']
						)
					));
				}
				else if( $method == 'DirectDebit' ){
					$data = self::http( 'directdebit/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/purchases ', array(
						'merchantRefNum' 	=> $order_id,
						'amount'			=> $order['price'] * 100,
						'ach'				=> array(
							'paymentToken'		=> $token,
							'payMethod'			=> 'WEB',
							'paymentDescriptor'	=> 'Transaction'
						),
						'billingDetails'	=> array(
							'zip'	=> $_POST['zip']
						)
					));
				}
				else if( $method == 'Interac' ){
					$data = self::http( 'alternatepayments/v1/accounts/'.adifier_get_option( 'paysafe_account_id' ).'/payments ', array(
						'merchantRefNum' 	=> $order_id,
						'amount'			=> $order['price'] * 100,
						'settleWithAuth'	=> false,
						'returnLinks'		=> array(
							'rel'	=> 'default',
							'href'	=> home_url( '/' )
						),
						'card'				=> array(
							'paymentToken'	=> $token
						),
						'billingDetails'	=> array(
							'zip'	=> $_POST['zip']
						),						
						'paymentType'		=> 'INTERAC',
						'paymentToken'		=> $token
					));
				}

			    if( !empty( $data->status ) && $data->status === 'COMPLETED' ){
					$response = Adifier_Order::create_order(array(
						'order_payment_type' 	=> 'paysafe',
						'order_transaction_id' 	=> $data->id,
						'order_id'				=> $order_id,
						'order_paid'			=> 'yes'
					));
					update_post_meta( $response['order_id'], 'adifier_paysafe_method', $method );
			    }
			    else{
			    	$response = array( 'error' => esc_html__( 'We were unable to process your payment at the moment', 'adifier' ) );
			    }
			}
			else{
				$response = array( 'error' => esc_html__( 'Your order has expired', 'adifier' ) );
			}
		}
		else{
			$response = array( 'error' => esc_html__( 'Invalid payment token, try again', 'adifier' ) );
		}
	    
		echo json_encode( $response );
		die();    
	}

	/*
	Get URL
	*/
	static public function get_url(){
		return 'https://api.'.( adifier_get_option( 'payment_enviroment' ) == 'test' ? 'test.' : '' ).'paysafe.com/';
	}

	/*
	HTTP calling
	*/
	static public function http( $checkpoint, $data = array(), $request = 'POST' ){
	    $response = wp_remote_post( self::get_url().$checkpoint, array(
	        'method' => $request,
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Basic '.adifier_b64_encode( adifier_get_option('paysafe_username').':'.adifier_get_option('paysafe_password') ),
	            'Content-Type'	=> 'application/json'
	        ),
	        'body' => !empty( $data ) ? json_encode( $data ) : '',
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
		   	return json_decode( $response['body']);		   	
		}
	}

}
add_filter( 'init', 'Adifier_Paysafe::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Paysafe::register_in_options' );
}
?>