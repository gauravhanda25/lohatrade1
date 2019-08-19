<?php
if( !class_exists('Adifier_Flow') ) {
class Adifier_Flow{
	/*
	Add flow options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Flow', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Flow payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_flow',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Flow', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Flow', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'flow_api',
                    'type'      => 'text',
                    'title'     => esc_html__('API Key', 'adifier') ,
                    'desc'      => esc_html__('Input your flow api key', 'adifier'),
                ),
                array(
                    'id'        => 'flow_secret',
                    'type'      => 'text',
                    'title'     => esc_html__('Secret Key', 'adifier') ,
                    'desc'      => esc_html__('Input your flow secret key', 'adifier'),
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
            if( !empty( $_GET['adifier_verify_payment'] ) && $_GET['adifier_verify_payment'] == 'flow' ){
                self::verify_payment();
            }

            if( !empty( $_GET['adifier_verify_refund'] ) && $_GET['adifier_verify_refund'] == 'flow' ){
                self::verify_refund();
            }

			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_Flow::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Flow::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_flow', 'Adifier_Flow::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_Flow::select_dropdown' );

			add_action('wp_ajax_flow_create_payment', 'Adifier_Flow::create_payment');
			add_action('wp_ajax_flow_execute_payment', 'Adifier_Flow::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['flow'] = esc_html__( 'Flow', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if flow is configured and enabled
	*/
	static public function is_enabled(){
		$enable_flow = adifier_get_option( 'enable_flow' );
		$flow_api = adifier_get_option( 'flow_api' );
		$flow_secret = adifier_get_option( 'flow_secret' );
		if( $enable_flow == 'yes' && !empty( $flow_api ) && !empty( $flow_secret ) ){
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
		wp_enqueue_script('adifier-flow', get_theme_file_uri( '/js/payments/flow.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-flow', get_theme_file_uri( '/css/payments/flow.css' ) );
	}

	/*
	Add flow to the list of the available payments in the frontend
	*/
	static public function render(){
		$pk_client_id = adifier_get_option( 'pk_client_id' );
		?>	
		<li>
			<a href="javascript:void(0);" id="flow-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/flow.png' ) ); ?>" alt="flow" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$user_info = get_userdata( $order->post_author );
		$_order_json_details = Adifier_Order::get_order_details( $order->ID );
		$params = array(
			'apiKey'				=> adifier_get_option( 'flow_api' ),
			'refundCommerceOrder'	=> current_time( 'timestamp' ),
			'commerceTrxId'			=> $order_transaction_id,
			'receiverEmail'			=> $user_info->user_email,
			'amount'				=> $_order_json_details['price'],
			'urlCallBack'			=> esc_url( add_query_arg( array( 'adifier_verify_refund' => 'flow' ), home_url('/') ) ),
		);

		$params['s'] = self::generate_signature( $params );

	    $data = self::http( 'refund/create', $params );

		if( !empty( $data->status ) && $data->status == 'created' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Verify refund
	*/
	static public function verify_refund(){
		echo 'OK';
	}

	/*
	Generate signature
	*/

	static public function generate_signature( $params ){
		$flow_secret = adifier_get_option( 'flow_secret' );

		ksort( $params );
		foreach( $params as $key => $value ){
			$list[] = $key.'='.$value;
		}


		return hash_hmac('sha256', implode( '&', $list ), $flow_secret);
	}


	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$current_user = wp_get_current_user();
			$params = array(
				'apiKey' 			=> adifier_get_option( 'flow_api' ),
				'commerceOrder'		=> $order_id,
				'subject'			=> $order_id,
				'currency'			=> adifier_get_option( 'currency_abbr' ),
				'amount'			=> $order['price'],
				'email'				=> $current_user->user_email,
				'urlConfirmation'	=> esc_url( add_query_arg( array( 'adifier_verify_payment' => 'flow' ), home_url('/index.php') ) ),
				'urlReturn'			=> $_POST['redirectUrl'].'#flow-return'
			);

			$params['s'] = self::generate_signature( $params );

			$data = self::http( 'payment/create', $params );

			if( !empty( $data->token ) ){
	            $result = Adifier_Order::create_order(array(
	                'order_payment_type'    => 'flow',
	                'order_transaction_id'  => $order_id,
	                'order_id'              => $order_id,
	                'order_paid'            => 'no'
	            ));				
				$response['paymentUrl'] = $data->url.'?token='.$data->token;
			}
			else{
				$response['error'] = esc_html__( 'Can not generate URL', 'adifier' );
			}
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

    /*
    Verify payment of ideal
    */
    static public function verify_payment(){
    	
    	$params = array(
    		'token' 	=> $_POST['token'],
    		'apiKey'	=> adifier_get_option( 'flow_api' )
    	);

    	$params['s'] = self::generate_signature( $params );

        $data = self::http( 'payment/getStatus', $params, 'GET');

        if( !empty( $data->status ) ){
        	$order_id = Adifier_Order::get_order_by_transaction_id( $data->commerceOrder );
            if( $data->status == 2 ){
                Adifier_Order::apply_after_verification( $order_id );
            }
            else if( in_array( $data->status, array( 3, 4 ) ) ){
                wp_delete_post( $order_id, true );
            }
        }
    }	

	/*
	To http post for flow
	*/
	static public function http( $checkpoint, $data, $method = 'POST' ){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
	    $response = wp_remote_post( 'https://'.( $payment_enviroment == 'live' ? 'www' : 'sandbox' ).'.flow.cl/api/'.$checkpoint, array(
	        'method' => $method,
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
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
add_filter( 'init', 'Adifier_Flow::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Flow::register_in_options' );
}
?>