<?php
if( !class_exists('Adifier_PagSeguro') ) {
class Adifier_PagSeguro{
	/*
	Add pagseguro options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PagSeguro', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PagSeguro payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_pagseguro',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PagSeguro', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via PagSeguro', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'pagseguro_email',
                    'type'      => 'text',
                    'title'     => esc_html__('Email', 'adifier') ,
                    'desc'      => esc_html__('Input your pagseguro email', 'adifier'),
                ),                
                array(
                    'id'        => 'pagseguro_token',
                    'type'      => 'text',
                    'title'     => esc_html__('Token', 'adifier') ,
                    'desc'      => esc_html__('Input your pagseguro token', 'adifier'),
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
            if( !empty( $_POST['notificationCode'] ) && !empty( $_POST['notificationType'] ) ){
                self::verify_payment();
            }

			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_PagSeguro::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_PagSeguro::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_pagseguro', 'Adifier_PagSeguro::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PagSeguro::select_dropdown' );

			add_action('wp_ajax_pagseguro_create_payment', 'Adifier_PagSeguro::create_payment');
			add_action('wp_ajax_pagseguro_execute_payment', 'Adifier_PagSeguro::execute_payment');

			add_action( 'adifier_manual_refund_list', 'Adifier_PagSeguro::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'pagseguro';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['pagseguro'] = esc_html__( 'PagSeguro', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if pagseguro is configured and enabled
	*/
	static public function is_enabled(){
		$enable_pagseguro = adifier_get_option( 'enable_pagseguro' );
		$pagseguro_token = adifier_get_option( 'pagseguro_token' );
		$pagseguro_email = adifier_get_option( 'pagseguro_email' );
		if( $enable_pagseguro == 'yes' && !empty( $pagseguro_token ) && !empty( $pagseguro_email ) ){
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
		wp_enqueue_script('adifier-pagseguro-checkout', 'https://stc.'.( $payment_enviroment == 'test' ? esc_attr( 'sandbox.' ) : esc_attr( '' ) ).'pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js', false, false, true);
		wp_enqueue_script('adifier-pagseguro', get_theme_file_uri( '/js/payments/pagseguro.js' ), array('jquery', 'adifier-purchase'), false, true);

		wp_enqueue_style( 'adifier-pagseguro', get_theme_file_uri( '/css/payments/pagseguro.css' ) );
	}

	/*
	Add pagseguro to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="pagseguro-button" >
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/pagseguro.png' ) ); ?>" alt="pagseguro" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id = '' ){
	    Adifier_Order::mark_as_refunded( $order );
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$data = array(
				'email'				=> adifier_get_option( 'pagseguro_email' ),
				'token'				=> adifier_get_option( 'pagseguro_token' ),
				'currency'			=> adifier_get_option( 'currency_abbr' ),
				'itemId1'   		=> $order_id,
				'itemDescription1'	=> $order_id,
				'itemAmount1'		=> $order['price'],
				'itemQuantity1'		=> 1,
				'reference'			=> $order_id,
			);
			$http_response = self::http( 'checkout', $data );
			if( !empty( $http_response->code ) ){
				$response = array(
					'order_id'	=> $order_id,
					'code'		=> $http_response->code
				);
			}
			else{
				$response['error'] = esc_html__( 'Could not generate code', 'adifier' );
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
		$transaction_id = $_POST['transaction_id'];
		$order_id = $_POST['order_id'];
		$order = get_transient( $order_id );

		if( !empty( $transaction_id ) ){
			if( !empty( $order ) ){
				$response = Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'pagseguro',
					'order_transaction_id' 	=> $transaction_id,
					'order_id'				=> $order_id,
					'order_paid'			=> 'no'
				));
				if( !empty( $response['success'] ) ){
					$response['success'] = '<div class="alert-info">'.esc_html__( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ).'</div>';
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
    Verify payment of pagseguro
    */
    static public function verify_payment(){
    	$credentials = array(
			'email'				=> adifier_get_option( 'pagseguro_email' ),
			'token'				=> adifier_get_option( 'pagseguro_token' ),
    	);
        $data = self::http( 'transactions/notifications/'.$_POST['notificationCode'], $credentials, 'GET');


        if( !empty( $data->status ) && in_array( $data->status, array( '3', '6', '7' ) ) ){
        	$posts = get_posts(array(
        		'posts_per_page' 	=> 1,
        		'post_type'			=> 'ad-order',
        		'meta_key'			=> 'order_number',
        		'meta_value'		=> str_replace( 'order_', '', $data->reference )
        	));
        	if( !empty( $posts ) ){
        		$post = array_shift( $posts );
        		if( !empty( $post->ID ) ){
        			if( $data->status == '3' ){
        				Adifier_Order::apply_after_verification( $post->ID );
        			}
        			else if( in_array( $data->status, array( '6', '7' ) ) ){
        				self::refund( $post );
        			}
        		}
        	}
        }
    }


	/*
	To http post for pagseguro
	*/
	static public function http( $checkpoint, $data, $method = 'POST' ){
		$payment_enviroment = adifier_get_option( 'payment_enviroment' );
	    $response = wp_remote_post( 'https://ws.'.( $payment_enviroment == 'test' ? esc_attr( 'sandbox.' ) : esc_attr( '' ) ).'pagseguro.uol.com.br/v2/'.$checkpoint, array(
	        'method' => $method,
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Content-Type' => 'application/x-www-form-urlencoded;charset=ISO-8859-1'
	        ),
	        'body' => $data,
	        'cookies' => array()
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
			$xml = simplexml_load_string( $response['body'] );
			$json = json_encode( $xml );
		   	return json_decode( $json );		   	
		}
	}

}
add_filter( 'init', 'Adifier_PagSeguro::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PagSeguro::register_in_options' );
}
?>