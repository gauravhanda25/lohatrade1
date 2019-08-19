<?php
if( !class_exists('Adifier_PayUmoney') ) {
class Adifier_PayUmoney{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayU Money', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayUmoney payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_payumoney',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PayUmoney', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via PayUmoney', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'pum_key',
                    'type'      => 'text',
                    'title'     => esc_html__('Key', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUmoney key', 'adifier'),
                ),
                array(
                    'id'        => 'pum_salt',
                    'type'      => 'text',
                    'title'     => esc_html__('Salt', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUmoney salt', 'adifier'),
                ),
                array(
                    'id'        => 'pum_auth_header',
                    'type'      => 'text',
                    'title'     => esc_html__('Authorization Header', 'adifier') ,
                    'desc'      => esc_html__('Input your Auth/Test header', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_PayUmoney::enqueue_scripts' );
				if( function_exists('adifier_script_tag_handler') ){
					adifier_script_tag_handler( 'Adifier_PayUmoney::script_bolt_id' );
				}
				add_action( 'adifier_payment_methods', 'Adifier_PayUmoney::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_payumoney', 'Adifier_PayUmoney::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PayUmoney::select_dropdown' );

			add_action('wp_ajax_payumoney_create_payment', 'Adifier_PayUmoney::create_payment');
			add_action('wp_ajax_payumoney_execute_payment', 'Adifier_PayUmoney::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['payumoney'] = esc_html__( 'PayUmoney', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_payumoney = adifier_get_option( 'enable_payumoney' );
		$pum_key = adifier_get_option( 'pum_key' );
		$pum_salt = adifier_get_option( 'pum_salt' );
		if( $enable_payumoney == 'yes' && !empty( $pum_key ) && !empty( $pum_salt ) ){
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
		wp_enqueue_script('adifier-payumoney-checkout', 'https://'.( $payment_enviroment == 'live' ? '' : 'sbox' ).'checkout-static.citruspay.com/bolt/run/bolt.min.js', false, false, true);
		wp_enqueue_script('adifier-payumoney', get_theme_file_uri( '/js/payments/payumoney.js' ), array('jquery', 'adifier-purchase', 'adifier-payumoney-checkout'), false, true);

		wp_enqueue_style( 'adifier-payumoney', get_theme_file_uri( '/css/payments/payumoney.css' ) );
	}

	/*
	Add ID bolt to script tag
	*/
	static public function script_bolt_id( $tag, $handle, $src ){
	    if ( $handle == 'adifier-payumoney-checkout' ){
	    	$tag = str_replace('></', ' id="bolt" ></', $tag);
	    }
	    return $tag;
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="payumoney-button">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/payumoney.png' ) ); ?>" alt="payumoney" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$order_details = Adifier_Order::get_order_details( $order->ID );

		$data = self::http( 'treasury/merchant/refundPayment', array(
			'paymentId'		=> $order_transaction_id,
			'refundAmount'	=> $order_details['price']
		));
		if( !empty( $data->status ) && $data->status == '0' ){
			Adifier_Order::mark_as_refunded( $order );
		}
		else if( !empty( $data->status ) && $data->status == '-1' ){
			set_transient( 
				'adifier_order_notice', 
				array( 
					'errors' => array( esc_html__( 'Check if the transaction is settled', 'adifier' ).' - '.$data->message ), 
					'notice' => 'error' 
				) 
			);
		}
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$current_user = wp_get_current_user();
		$first_name = $current_user->first_name;
		$phone = get_user_meta( get_current_user_id(), 'phone', true );

		if( !empty( $first_name ) ){
			if( !empty( $phone ) ){
				$order = Adifier_Order::get_order();
				$order_id = Adifier_Order::create_transient( $order );

				if( !empty( $order['price'] ) ){
					$response = array(
						'key'			=> adifier_get_option( 'pum_key' ),
						'txnid'			=> substr(hash('sha256', mt_rand() . microtime()), 0, 20),
						'hash'			=> '',
						'amount' 		=> $order['price'],
						'firstname'		=> $first_name,
						'email'			=> $current_user->user_email,
						'phone'			=> $phone,
						'productinfo'	=> get_bloginfo( 'name' ),
						'surl'			=> $_POST['url'],
						'furl'			=> $_POST['url'],
						'udf1'			=> $order_id,
					);

					$hashVarsSeq = explode('|', "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10");
					$hash_string = '';  
					foreach($hashVarsSeq as $hash_var) {
					    $hash_string .= isset($response[$hash_var]) ? $response[$hash_var] : '';
					    $hash_string .= '|';
					}
					$hash_string .= adifier_get_option( 'pum_salt' );

					$response['hash'] = strtolower(hash('sha512', $hash_string));
				}
				else{
					$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
				}
			}
			else{
				$response['error'] = esc_html__( 'You need to provide your phone number', 'adifier' );		
			}
		}
		else{
			$response['error'] = esc_html__( 'You need to provide your first name', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

	/*
	First we create payment which will user approve and send ID to JS script
	*/
	static public function execute_payment(){
		$paymentData = $_POST['paymentData'];

		$hashVarsSeq = explode('|', "status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key");
		$hash_string = adifier_get_option( 'pum_salt' );  
		foreach($hashVarsSeq as $hash_var) {
			$hash_string .= '|';
		    $hash_string .= isset($paymentData[$hash_var]) ? $paymentData[$hash_var] : '';
		}

		$hash = strtolower(hash('sha512', $hash_string));

		if( $hash == $paymentData['hash'] ){
			$response = Adifier_Order::create_order(array(
				'order_payment_type' 	=> 'payumoney',
				'order_transaction_id' 	=> $paymentData['txnid'],
				'order_id'				=> $paymentData['udf1'],
				'order_paid'			=> 'yes'
			));
		}
		else{
			$response['error'] = '<div class="alert-error">'.esc_html__( 'Hashes do not match', 'adifier' ).'</div>';
		}

		echo json_encode( $response );
		die();
	}

	static public function http( $checkpoint, $data = array() ){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
	    $response = wp_remote_post( 'https://'.( $payment_enviroment == 'live' ? 'www' : 'test' ).'.payumoney.com/'.$checkpoint, array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Bearer '.adifier_get_option( 'pum_auth_header' ),
	            'Content-Type'	=> 'application/json'
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
add_filter( 'init', 'Adifier_PayUmoney::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PayUmoney::register_in_options' );
}
?>