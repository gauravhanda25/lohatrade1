<?php
if( !class_exists('Adifier_QuickPay') ) {
class Adifier_QuickPay{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('QuickPay', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure QuickPay payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_quickpay',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable QuickPay', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via QuickPay', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'quickpay_merchant_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your QuickPay merchant ID', 'adifier')
                ),
                array(
                    'id' 		=> 'quickpay_agreement_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Agreement ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your QuickPay agreement ID', 'adifier')
                ),
                array(
                    'id' 		=> 'quickpay_account_private_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Account Private Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your QuickPay account private key', 'adifier'),
                ),
                array(
                    'id' 		=> 'quickpay_payment_window_api',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Payment Window API', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your QuickPay payment window api key', 'adifier'),
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
            if( !empty( $_REQUEST['verify_payment'] ) && $_REQUEST['verify_payment'] == 'quickpay' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_QuickPay::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_QuickPay::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_quickpay', 'Adifier_QuickPay::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_QuickPay::select_dropdown' );

			add_action( 'wp_ajax_quickpay_create_payment', 'Adifier_QuickPay::create_payment' );

			add_action( 'adifier_manual_refund_list', 'Adifier_QuickPay::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'quickpay';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['quickpay'] = esc_html__( 'QuickPay', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_quickpay = adifier_get_option( 'enable_quickpay' );
		$quickpay_merchant_id = adifier_get_option( 'quickpay_merchant_id' );
		$quickpay_agreement_id = adifier_get_option( 'quickpay_agreement_id' );
		$quickpay_account_private_key = adifier_get_option( 'quickpay_account_private_key' );
		$quickpay_payment_window_api = adifier_get_option( 'quickpay_payment_window_api' );
		if( $enable_quickpay == 'yes' && !empty( $quickpay_merchant_id ) && !empty( $quickpay_agreement_id ) && !empty( $quickpay_account_private_key ) && !empty( $quickpay_payment_window_api ) ){
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
		wp_enqueue_script('adifier-quickpay', get_theme_file_uri( '/js/payments/quickpay.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-quickpay', get_theme_file_uri( '/css/payments/quickpay.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="quickpay-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/quickpay.png' ) ); ?>" alt="quickpay" width="148" height="42">
			</a>
		</li>
		<?php
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		Adifier_Order::mark_as_refunded( $order );
	}

	/*
	Flatten params
	*/
	static private function _flatten_params($obj, $result = array(), $path = array()) {
	    if (is_array($obj)) {
	        foreach ($obj as $k => $v) {
	            $result = array_merge($result, self::_flatten_params($v, $result, array_merge($path, array($k))));
	        }
	    } else {
	        $result[implode("", array_map(function($p) { return "[{$p}]"; }, $path))] = $obj;
	    }

	    return $result;
	}	

	/*
	Generate signature
	*/
	static private function _generate_signature( $fields, $key ){
		$flattened_params = self::_flatten_params($fields);

		ksort($flattened_params);

		$base = implode(" ", $flattened_params);

		return hash_hmac("sha256", $base, $key);

	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$quickpay_merchant_id = adifier_get_option( 'quickpay_merchant_id' );
			$quickpay_agreement_id = adifier_get_option( 'quickpay_agreement_id' );
			$quickpay_payment_window_api = adifier_get_option( 'quickpay_payment_window_api' );

			$form = '';
			ob_start();
			$fields = array(
				'version'					=> 'v10',
				'merchant_id'				=> esc_attr( $quickpay_merchant_id ),
				'agreement_id'				=> esc_attr( $quickpay_agreement_id ),
				'order_id'					=> esc_attr( $order_id ),
				'amount'					=> esc_attr( $order['price']*100 ),
				'currency'      			=> adifier_get_option( 'currency_abbr' ),
				'continueurl'				=> $_POST['redirectUrl'].'#quickpay-return',
				'cancelurl'					=> $_POST['redirectUrl'],
				'callbackurl'				=> add_query_arg( 'verify_payment', 'quickpay', site_url('/') ),
				'type'						=> 'payment'
			);

			$fields["checksum"] = self::_generate_signature( $fields, $quickpay_payment_window_api );

			?>
			<form method="post" action="https://payment.quickpay.net/" class="quickpay-form">
				<?php

				foreach( $fields as $key => $value ){
					echo '<input name="'.$key.'" value="'.$value.'" type="hidden">';
				}
				?>
			</form>	
			<?php
			$form = ob_get_contents();
			ob_end_clean();

			$response = array(
				'form' => $form
			);		
		}
		else{
			$response['error'] = esc_html__( 'Can not process your request, contact administration', 'adifier' );
		}

		echo json_encode( $response );
		die();		
	}

    /*
    Verify payment of quickpay
    */
    static public function verify_payment(){
		$request_body = adifier_payment_output();
		$quickpay_account_private_key = adifier_get_option( 'quickpay_account_private_key' );
		$checksum     = self::_generate_signature( $request_body, $quickpay_account_private_key );
		$server_variables = adifier_server_variables();
		if ( $checksum == $server_variables["HTTP_QUICKPAY_CHECKSUM_SHA256"] ){
			$data = json_decode( $request_body, true );
			if( $data['accepted'] === true ){
				Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'quickpay',
					'order_transaction_id' 	=> $data['id'],
					'order_id'				=> $data['order_id'],
					'order_paid'			=> 'yes'
				));
			}
		} 
    }

}
add_filter( 'init', 'Adifier_QuickPay::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_QuickPay::register_in_options' );
}
?>