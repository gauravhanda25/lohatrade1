<?php
if( !class_exists('Adifier_payueu') ) {
class Adifier_payueu{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayU EU', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayUEU payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_payueu',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable payueu', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via payueu', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'payueu_posid',
                    'type'      => 'text',
                    'title'     => esc_html__('POS ID', 'adifier') ,
                    'desc'      => esc_html__('Input your payueu POS ID', 'adifier'),
                ),
                array(
                    'id'        => 'payueu_second_key',
                    'type'      => 'text',
                    'title'     => esc_html__('Second Key', 'adifier') ,
                    'desc'      => esc_html__('Input your payueu second key', 'adifier'),
                ),
                array(
                    'id'        => 'payueu_oauth_client_id',
                    'type'      => 'text',
                    'title'     => esc_html__('OAuth Client ID', 'adifier') ,
                    'desc'      => esc_html__('Input your payueu OAuth client ID', 'adifier'),
                ),
                array(
                    'id'        => 'payueu_oauth_client_secret',
                    'type'      => 'text',
                    'title'     => esc_html__('OAuth Client Secret', 'adifier') ,
                    'desc'      => esc_html__('Input your payueu OAuth client secret', 'adifier'),
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
            if( !empty( $_REQUEST['adifier_verify_payment'] ) && $_REQUEST['adifier_verify_payment'] == 'payueu' ){
                self::verify_payment();
            }
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_payueu::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_payueu::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_payueu', 'Adifier_payueu::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_payueu::select_dropdown' );

			add_action('wp_ajax_payueu_create_payment', 'Adifier_payueu::create_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['payueu'] = esc_html__( 'PayUEU', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_payueu = adifier_get_option( 'enable_payueu' );
		$payueu_posid = adifier_get_option( 'payueu_posid' );
		$payueu_second_key = adifier_get_option( 'payueu_second_key' );
		$payueu_oauth_client_id = adifier_get_option( 'payueu_oauth_client_id' );
		$payueu_oauth_client_secret = adifier_get_option( 'payueu_oauth_client_secret' );
		if( $enable_payueu == 'yes' && !empty( $payueu_posid ) && !empty( $payueu_second_key ) && !empty( $payueu_oauth_client_id ) && !empty( $payueu_oauth_client_secret ) ){
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
		wp_enqueue_script('adifier-payueu', get_theme_file_uri( '/js/payments/payueu.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-payueu', get_theme_file_uri( '/css/payments/payueu.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="payueu-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/payueu.png' ) ); ?>" alt="payueu" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$data = self::http( 'api/v2_1/orders/'.$order_transaction_id.'/refunds', array(
			'refund' => array(
				'description' => 'Refund'
			)
		));
		if( !empty( $data->status ) && !empty( $data->status->statusCode ) && $data->status->statusCode == 'SUCCESS' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Retrieve Authorization token
	*/
	static public function get_oath_token(){
		$token = get_transient( 'payueu_'.get_current_user_id() );
		if( empty( $token ) ){
			$data = self::http( 'pl/standard/user/oauth/authorize', array(
				'grant_type'	=> 'client_credentials',
				'client_id'		=> adifier_get_option( 'payueu_oauth_client_id' ),
				'client_secret'	=> adifier_get_option( 'payueu_oauth_client_secret' )
			), true);

			if( !empty( $data->access_token ) ){
				$token = $data->access_token;
				set_transient( 'payueu_'.get_current_user_id(), $token, $data->expires_in );
			}
			
		}

		return $token;
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$data = self::http( 'api/v2_1/orders', array(
				"customerIp" 			=> "127.0.0.1",
				'notifyUrl'				=> esc_url( add_query_arg( array( 'adifier_verify_payment' => 'payueu' ), home_url('/') ) ),
				'continueUrl'			=> $_POST['responseUrl'].'#payueu-return',
				'merchantPosId'			=> adifier_get_option( 'payueu_posid' ),
				'description'			=> $order_id,
				'currencyCode'			=> adifier_get_option( 'currency_abbr' ),
				'totalAmount'			=> $order['price'] * 100,
				'extOrderId'			=> $order_id,
				'products'				=> array(
					array(
                        'name'		=> $order_id,
                        'unitPrice' => $order['price']*100,
                        'quantity'	=> 1
                    )
				)
			));

			if( !empty( $data->status ) && !empty( $data->status->statusCode ) ){
				if( $data->status->statusCode == 'SUCCESS' ){
					$response['redirectUri'] = $data->redirectUri;
				}
			}
			else{
				$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );	
			}
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();	
	}

    /*
    Get headers
    */
    static public function get_header_signature(){
    	$signature = '';
        if ( !function_exists('apache_request_headers') ) {
            $headers = array();
            $server_variables = adifier_server_variables();
            foreach ($server_variables as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
                }
            }
        } 
        else {
            $headers = apache_request_headers();
        }


        if( !empty( $headers ) ){
	        foreach($headers as $name => $value){
	            if(preg_match('/X-OpenPayU-Signature/i', $name) || preg_match('/OpenPayu-Signature/i', $name)){
	            	$signatureHeader = $value;
	            }
	        }
	        $signatureData = array();

	        $list = explode(';', rtrim($signatureHeader, ';'));
	        if (empty($list)) {
	            return null;
	        }

	        foreach ($list as $value) {
	            $explode = explode('=', $value);
	            if (count($explode) != 2) {
	                return null;
	            }
	            $signatureData[$explode[0]] = $explode[1];
	        }

	        $signature = $signatureData['signature'];
    	}

    	return $signature;
    }

    /*
	Concate JSON
    */
    static public function concate_body( $body ){
    	$list = '';
    	foreach( $body as $key => $value ){
    		if( is_array( $value ) ){
    			$list .= self::concate_body( $value );
    		}
    		else{
    			$list .= $value;
    		}
    	}

    	return $list;
    }

    /*
    Verify payment of payultam
    */
    static public function verify_payment(){
		$body = adifier_payment_output();
		$raw_data = trim( $body );
		$data = json_decode( $raw_data, true );
    	if( !empty( $data["order"] ) && !empty( $data['order']['status'] ) && $data['order']['status'] == 'COMPLETED' ){
    		$received_signature = self::get_header_signature();
    		$calculated_signature = md5( $raw_data.adifier_get_option('payueu_second_key') );
    		
    		if( $received_signature == $calculated_signature ){    			
				Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'payueu',
					'order_transaction_id' 	=> $data['order']['orderId'],
					'order_id'				=> $data['order']['extOrderId'],
					'order_paid'			=> 'yes'
				));
    		}
    	}
    	echo 'OK';
    }   

	/*
	Build URL
	*/
	static public function payueu_url(){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
		return "https://secure".( $payment_enviroment == 'test' ? '.snd' : '' ).".payu.com/";
	}


	/*
	HTTP calling
	*/
	static public function http( $checkpoint, $data = array(), $token_request = false ){

		if( $token_request ){
			$headers = array(
				'Content-Type' 		=> 'application/x-www-form-urlencoded'
			);
		}
		else{
			$headers = array(
	            'Content-Type'		=> 'application/json',
	            'Authorization'		=> 'Bearer '.self::get_oath_token()
		    );
		}

	    $response = wp_remote_post( self::payueu_url().$checkpoint, array(
	        'method' 		=> 'POST',
	        'timeout' 		=> 45,
	        'redirection' 	=> 0,
	        'httpversion' 	=> '1.0',
	        'blocking' 		=> true,
	        'headers' 		=> $headers,
	        'body' 			=> $token_request ? $data : json_encode( $data ),
	        'cookies' 		=> array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{

		   	return json_decode( $response['body'] );
		}
	}

}
add_filter( 'init', 'Adifier_payueu::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_payueu::register_in_options' );
}
?>