<?php
if( !class_exists('Adifier_AuthorizeNet') ) {
class Adifier_AuthorizeNet{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Authorize.Net', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Authorize.Net payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_authorize',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Authorize.Net', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Authorize.Net', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'authorize_api_login_id',
                    'type'      => 'text',
                    'title'     => esc_html__('API Login ID', 'adifier') ,
                    'desc'      => esc_html__('Input your API Login ID', 'adifier'),
                ),
                array(
                    'id'        => 'authorize_client_key',
                    'type'      => 'text',
                    'title'     => esc_html__('Client Key', 'adifier') ,
                    'desc'      => esc_html__('Input your client key', 'adifier'),
                ),
                array(
                    'id'        => 'authorize_transaction_key',
                    'type'      => 'text',
                    'title'     => esc_html__('Transaction Key', 'adifier') ,
                    'desc'      => esc_html__('Input your transation key', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_AuthorizeNet::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_AuthorizeNet::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_authorize', 'Adifier_AuthorizeNet::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_AuthorizeNet::select_dropdown' );

			add_action('wp_ajax_authorize_execute_payment', 'Adifier_AuthorizeNet::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['authorize'] = esc_html__( 'Authorize.Net', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_authorize = adifier_get_option( 'enable_authorize' );
		$authorize_api_login_id = adifier_get_option( 'authorize_api_login_id' );
		$authorize_client_key = adifier_get_option( 'authorize_client_key' );
		$authorize_transaction_key = adifier_get_option( 'authorize_transaction_key' );
		if( $enable_authorize == 'yes' && !empty( $authorize_api_login_id ) && !empty( $authorize_client_key ) && !empty( $authorize_transaction_key ) ){
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
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
		wp_enqueue_script('adifier-authorize-checkout', 'https://js'.( $payment_enviroment == 'live' ? '' : 'test' ).'.authorize.net/v3/AcceptUI.js', false, false, true);
		wp_enqueue_script('adifier-authorize', get_theme_file_uri( '/js/payments/authorize.js' ), array('jquery', 'adifier-purchase', 'adifier-authorize-checkout'), false, true);

		wp_enqueue_style( 'adifier-authorize', get_theme_file_uri( '/css/payments/authorize.css' ) );
	}

	/*
	Add authorize to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<form id="paymentForm" method="POST" action="">
			    <input type="hidden" name="dataValue" id="dataValue" />
			    <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
			    <button type="button"
			    	id="authorize-button" 
			        class="AcceptUI" 
			        data-billingAddressOptions='{"show":false, "required":false}' 
			        data-apiLoginID="<?php echo esc_attr( adifier_get_option( 'authorize_api_login_id' ) ) ?>" 
			        data-clientKey="<?php echo esc_attr( adifier_get_option( 'authorize_client_key' ) ) ?>" 
			        data-acceptUIFormBtnTxt="<?php esc_attr_e( 'Pay Now', 'adifier' ) ?>" 
			        data-acceptUIFormHeaderTxt="<?php esc_attr_e( 'Card Information', 'adifier' ) ?>" 
			        data-responseHandler="authorizeNetResponse"> 
			        <img src="<?php echo esc_url( get_theme_file_uri( '/images/authorize.png' ) ); ?>" alt="authorize" width="148" height="42"> 
			    </button>
			</form>			
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$transactionInfo = explode('|', $order_transaction_id);
		$order_details = Adifier_Order::get_order_details( $order->ID );

	    $data = self::http(array(
			'transactionType'	=> 'refundTransaction',
			'amount'			=> $order_details['price'],
            'payment'			=> array(
            	'creditCard'		=> array(
            		'cardNumber'		=> $transactionInfo[1],
            		'expirationDate'	=> 'XXXX'
            	)
            ),
			'refTransId'	=>  $transactionInfo[0]
	    ));

		if( !empty( $data->transactionResponse->responseCode ) && $data->transactionResponse->responseCode == '1' ){
			Adifier_Order::mark_as_refunded( $order );
		}
		else if( !empty( $data->transactionResponse->errors ) ){
			$transientErrors = array();
			foreach( $data->transactionResponse->errors as $error ){
				$transientErrors[] = $error->errorText;
			}
			set_transient( 
				'adifier_order_notice', 
				array( 
					'errors' => $transientErrors, 
					'notice' => 'error' 
				) 
			);
		}
	}


	/*
	First we create payment which will user approve and send ID to JS script
	*/
	static public function execute_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );
		$buyerData = $_POST['buyerData'];

		if( !empty( $buyerData ) ){
			if( !empty( $order ) ){
			    $data = self::http(array(
					'transactionType'	=> 'authCaptureTransaction',
					'amount'			=> (string)$order['price'],
		            'payment'			=> array(
		            	'opaqueData'		=> array(
		            		'dataDescriptor'	=> $buyerData['dataDescriptor'],
		            		'dataValue'			=> $buyerData['dataValue']
		            	)
		            )
			    ));

			    if( $data->transactionResponse->responseCode == '1' ){
					$response = Adifier_Order::create_order(array(
						'order_payment_type' 	=> 'authorize',
						'order_transaction_id' 	=> $data->transactionResponse->transId.'|'.substr( $data->transactionResponse->accountNumber, -4 ),
						'order_id'				=> $order_id,
						'order_paid'			=> 'yes'
					));
			    }
			    else{
			    	$response = array( 'error' => '<div class="alert-error">'.esc_html__( 'We were unable to process your payment at the moment', 'adifier' ).'</div>' );
			    }
			}
			else{
				$response = array( 'error' => '<div class="alert-error">'.esc_html__( 'Your order has expired', 'adifier' ).'</div>' );
			}
		}
		else{
			$response = array( 'error' => '<div class="alert-error">'.esc_html__( 'Invalid payment token, try again', 'adifier' ).'</div>' );
		}
	    
		echo json_encode( $response );
		die();    
	}

	/*
	To http post for stripe
	*/
	static public function http( $data ){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );

	    $response = wp_remote_post( 'https://api'.( $payment_enviroment == 'live' ? '' : 'test' ).'.authorize.net/xml/v1/request.api', array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Content-Type' => 'application/json'
	        ),
	        'body' => json_encode( array(
	        	'createTransactionRequest' => array(
	        		'merchantAuthentication' => array(
						'name' 				=> adifier_get_option('authorize_api_login_id'),
						'transactionKey'	=> adifier_get_option('authorize_transaction_key')
	        		),
	        		'refId'	=> 'ref_'.time(),
	        		'transactionRequest' => $data,
	        	)
	        )),
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
			$response['body'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response['body']);
		   	return json_decode( $response['body'] );
		}
	}

}
add_filter( 'init', 'Adifier_AuthorizeNet::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_AuthorizeNet::register_in_options' );
}
?>