<?php
if( !class_exists('Adifier_MercadoPago') ) {
class Adifier_MercadoPago{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('MercadoPago', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure MercadoPago payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_mercadopago',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable MercadoPago', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via MercadoPago', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'mercadopago_access_token',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Access Token', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your MercadoPago access token', 'adifier')
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
            if( !empty( $_REQUEST['verify_payment'] ) && $_REQUEST['verify_payment'] == 'mercadopago' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_MercadoPago::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_MercadoPago::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_mercadopago', 'Adifier_MercadoPago::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_MercadoPago::select_dropdown' );

			add_action( 'wp_ajax_mercadopago_create_payment', 'Adifier_MercadoPago::create_payment' );
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['mercadopago'] = esc_html__( 'MercadoPago', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_mercadopago = adifier_get_option( 'enable_mercadopago' );
		$mercadopago_access_token = adifier_get_option( 'mercadopago_access_token' );
		if( $enable_mercadopago == 'yes' && !empty( $mercadopago_access_token ) ){
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
		wp_enqueue_script('adifier-mercadopago', get_theme_file_uri( '/js/payments/mercadopago.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-mercadopago', get_theme_file_uri( '/css/payments/mercadopago.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="mercadopago-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/mercadopago.png' ) ); ?>" alt="mercadopago" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$data = self::http( 'v1/payments/'.$order_transaction_id.'/refunds?access_token='.adifier_get_option( 'mercadopago_access_token' ) );
		if( !empty( $data->status ) && $data->status == 'approved' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Create price
	*/
	static public function check_price( $price, $abbr ){
		$no_decimals = array( 'CLP', 'COP', 'HNL', 'NIO', 'PYG' );
		if( in_array( $abbr, $no_decimals ) ){
			$price = number_format( $price, 0 );
		}

		return (float)$price;

	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment( $user_data = array() ){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$currency_abbr = adifier_get_option( 'currency_abbr' );

			$data = self::http( 'checkout/preferences?access_token='.adifier_get_option( 'mercadopago_access_token' ), array(
				"items"				=> array(
					array(
						"title"				=> esc_attr( $order_id ),
						"description"		=> esc_attr( $order_id ),
						"quantity"			=> 1,
						"unit_price"		=> self::check_price( $order['price'], $currency_abbr ),
						"currency_id"		=> $currency_abbr,
					)
				),
				'notification_url'	=> add_query_arg( 'verify_payment', 'mercadopago', site_url('/') ),
				'back_urls'			=> array(
					'success'	=> $_POST['redirectUrl'].'#mercadopago_return',
					'failure'	=> $_POST['redirectUrl'],
					'pending'	=> $_POST['redirectUrl'],
				)				
			));

			if( !empty( $data->init_point ) ){
				$response['url'] = adifier_get_option( 'payment_enviroment' ) == 'live' ? $data->init_point : $data->sandbox_init_point;
			}
			else{
				$response['error'] = esc_html__( 'Failed to create payment', 'adifier' );	
			}

		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();
	}

    /*
    Verify payment of mercadopago
    */
    static public function verify_payment(){
		if (!isset($_GET["data_id"]) || !ctype_digit($_GET["data_id"])) {
			http_response_code(400);
			return;
		}

		$data = self::http( 'v1/payments/'.$_GET['data_id'].'?access_token='.adifier_get_option( 'mercadopago_access_token' ), array(), 'GET');

		if ( !empty( $data->status ) && $data->status == 'approved') {
			Adifier_Order::create_order(array(
				'order_payment_type' 	=> 'mercadopago',
				'order_transaction_id' 	=> $data->id,
				'order_id'				=> $data->description,
				'order_paid'			=> 'yes'
			));
		}

		echo 'OK';
		die();
    }

	/*
	HTTP calling
	*/
	static public function http( $checkpoint, $data = array(), $method = 'POST' ){
	    $response = wp_remote_post( 'https://api.mercadopago.com/'.$checkpoint, array(
	        'method' => $method,
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
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
add_filter( 'init', 'Adifier_MercadoPago::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_MercadoPago::register_in_options' );
}
?>