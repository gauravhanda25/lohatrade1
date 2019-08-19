<?php
if( !class_exists('Adifier_Stripe') ) {
class Adifier_Stripe{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Stripe', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Stripe payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_stripe',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Stripe', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Stripe', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'pk_client_id',
                    'type'      => 'text',
                    'title'     => esc_html__('Public Client ID', 'adifier') ,
                    'desc'      => esc_html__('Input your stripe public client ID', 'adifier'),
                ),
                array(
                    'id'        => 'sk_client_id',
                    'type'      => 'text',
                    'title'     => esc_html__('Secret Client ID', 'adifier') ,
                    'desc'      => esc_html__('Input your stripe secret client ID', 'adifier'),
                ),
                array(
                    'id'        => 'ap_client_id',
                    'type'      => 'text',
                    'title'     => esc_html__('Client ID', 'adifier') ,
                    'desc'      => esc_html__('Input your stripe secret client ID ( Settings -> Connect Settings -> Live/Test mode client ID )', 'adifier'),
                ),
                array(
                    'id'        => 'stripe_logo',
                    'type'      => 'media',
                    'title'     => esc_html__('Logo', 'adifier') ,
                    'desc'      => esc_html__('If you wish to have logo on stripe pop up window you can add it here', 'adifier'),
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Stripe::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Stripe::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_stripe', 'Adifier_Stripe::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_Stripe::select_dropdown' );

			add_action('wp_ajax_stripe_create_payment', 'Adifier_Stripe::create_payment');
			add_action('wp_ajax_stripe_execute_payment', 'Adifier_Stripe::execute_payment');
		}
	}  

	static public function select_dropdown( $dropdown ){
		$dropdown['stripe'] = esc_html__( 'Stripe', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_stripe = adifier_get_option( 'enable_stripe' );
		$pk_client_id = adifier_get_option( 'pk_client_id' );
		$sk_client_id = adifier_get_option( 'sk_client_id' );
		$ap_client_id = adifier_get_option( 'ap_client_id' );
		if( $enable_stripe == 'yes' && !empty( $pk_client_id ) && !empty( $sk_client_id ) && !empty( $ap_client_id ) ){
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
		wp_enqueue_script('adifier-stripe-checkout', 'https://checkout.stripe.com/checkout.js', false, false, true);
		wp_enqueue_script('adifier-stripe', get_theme_file_uri( '/js/payments/stripe.js' ), array('jquery', 'adifier-purchase'), false, true);

		wp_enqueue_style( 'adifier-stripe', get_theme_file_uri( '/css/payments/stripe.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		$pk_client_id = adifier_get_option( 'pk_client_id' );
		?>	
		<li>
			<a href="javascript:void(0);" id="stripe-button" data-pk="<?php echo esc_attr( $pk_client_id ); ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/stripe.png' ) ); ?>" alt="stripe" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
	    $data = self::http( 'refunds', array(
		    'charge' => $order_transaction_id,
		));

		if( !empty( $data->status ) && $data->status == 'succeeded' ){
			Adifier_Order::mark_as_refunded( $order );
		}
	}

	/*
	Create price
	*/
	static public function check_price( $price, $abbr ){
		$no_decimals = array( 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF' );
		if( in_array( $abbr, $no_decimals ) ){
			return $price;
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
			$stripe_logo = adifier_get_option( 'stripe_logo' );

			$response = array(
				'name'			=> get_bloginfo( 'name' ),
				'amount' 		=> self::check_price( $order['price'], $currency_abbr ),
				'locale'		=> 'auto',
				'order_id'		=> $order_id,
				'currency'		=> $currency_abbr,
				'image'			=> !empty( $stripe_logo ) ? $stripe_logo['url'] : ''
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
		$token = $_POST['token'];
		$order_id = $_POST['order_id'];
		$order = get_transient( $order_id );

		if( !empty( $token ) ){
			if( !empty( $order ) ){
				$currency_abbr = adifier_get_option( 'currency_abbr' );
			    $data = self::http( 'charges', array(
			        'amount' 		=> self::check_price( $order['price'], $currency_abbr ),
			        'currency' 		=> adifier_get_option( 'currency_abbr' ),
			        'card' 			=> $token['id'],
			        'receipt_email' => $token['email'],
			    ));

			    if( !empty( $data->paid ) && $data->paid === true ){
					$response = Adifier_Order::create_order(array(
						'order_payment_type' 	=> 'stripe',
						'order_transaction_id' 	=> $data->id,
						'order_id'				=> $order_id,
						'order_paid'			=> 'yes'
					));
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
	To http post for stripe
	*/
	static public function http( $checkpoint, $data ){
	    $response = wp_remote_post( 'https://api.stripe.com/v1/'.$checkpoint, array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'redirection' => 5,
	        'httpversion' => '1.0',
	        'blocking' => true,
	        'headers' => array(
	            'Authorization' => 'Bearer '.adifier_get_option( 'sk_client_id' )
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
add_filter( 'init', 'Adifier_Stripe::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Stripe::register_in_options' );
}
?>