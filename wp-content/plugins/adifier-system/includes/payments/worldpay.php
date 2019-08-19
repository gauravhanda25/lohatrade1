<?php
if( !class_exists('Adifier_Worldpay') ) {
class Adifier_Worldpay{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Worldpay', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Worldpay payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_worldpay',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Worldpay', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Worldpay', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'worldpay_service_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Service Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Worldpay service key', 'adifier'),
                ),
                array(
                    'id' 		=> 'worldpay_client_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Client Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Worldpay client key', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Worldpay::enqueue_scripts' );
				add_action( 'wp_footer', 'Adifier_Worldpay::add_modal' );
				add_action( 'adifier_payment_methods', 'Adifier_Worldpay::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_worldpay', 'Adifier_Worldpay::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_Worldpay::select_dropdown' );

			add_action( 'wp_ajax_worldpay_create_payment', 'Adifier_Worldpay::create_payment' );
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['worldpay'] = esc_html__( 'Worldpay', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_worldpay = adifier_get_option( 'enable_worldpay' );
		$worldpay_service_key = adifier_get_option( 'worldpay_service_key' );
		$worldpay_client_key = adifier_get_option( 'worldpay_client_key' );
		if( $enable_worldpay == 'yes' && !empty( $worldpay_service_key ) && !empty( $worldpay_client_key ) ){
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
		wp_enqueue_script('adifier-worldpay-script', 'https://cdn.worldpay.com/v1/worldpay.js', false, false, true);
		wp_enqueue_script('adifier-worldpay', get_theme_file_uri( '/js/payments/worldpay.js' ), array('jquery', 'adifier-purchase', 'adifier-worldpay-script'), false, true);
		wp_enqueue_style( 'adifier-worldpay', get_theme_file_uri( '/css/payments/worldpay.css' ) );
	}

	/*
	Add modal to footer since their is junk
	*/
	static public function add_modal(){
		?>
		<div class="modal in" id="worldpay" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="worldpay-loader"><i class="aficon-spin aficon-circle-notch"></i></div>
					<form action="" id="worldpay-form" method="post">

						<div class="modal-body">
							<div id="worldpay-section"></div>
						</div>

						<div class="modal-footer">
							<div class="flex-left">
							</div>
							<div class="flex-right">
								<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
								<input type="submit" value="Place Order" onclick="Worldpay.submitTemplateForm()" />
							</div>
						</div>
					</form>
				</div>

			</div>
		</div>		
		<?php
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="worldpay-button" data-clientkey="<?php esc_attr( adifier_get_option( 'worldpay_client_key' ) ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/worldpay.png' ) ); ?>" alt="worldpay" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		$data = self::http( 'orders/'.$order_transaction_id.'/refund' );
		if( empty( $data->httpStatusCode ) ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Create price
	*/
	static public function check_price( $price, $abbr ){
		$no_decimals = array( 'CLP', 'ISK', 'JPY', 'KRW', 'VND' );
		if( in_array( $abbr, $no_decimals ) ){
			return number_format( $price, 0 );
		}
		else{
			return $price * 100;
		}

	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){
			$currency_abbr = adifier_get_option( 'currency_abbr' );
			$data = self::http( 'orders', array(
				"token"				=> $_POST['token'],
				"orderDescription"	=> esc_attr( $order_id ),
				"amount"			=> self::check_price( $order['price'], $currency_abbr ),
				"currencyCode"		=> $currency_abbr
			));

			if( !empty( $data->paymentStatus ) && $data->paymentStatus == 'SUCCESS' ){
				$response = Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'worldpay',
					'order_transaction_id' 	=> $data->orderCode,
					'order_id'				=> $order_id,
					'order_paid'			=> 'yes'
				));
			}
			else{
				$response['error'] = esc_html__( 'Failed to execute payment', 'adifier' );	
			}
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

	/*
	HTTP calling
	*/
	static public function http( $checkpoint, $data = array() ){
	    $response = wp_remote_post( 'https://api.worldpay.com/v1/'.$checkpoint, array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => adifier_get_option( 'worldpay_service_key' ),
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
add_filter( 'init', 'Adifier_Worldpay::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Worldpay::register_in_options' );
}
?>