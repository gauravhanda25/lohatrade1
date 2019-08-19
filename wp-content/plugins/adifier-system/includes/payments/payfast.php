<?php
if( !class_exists('Adifier_PayFast') ) {
class Adifier_PayFast{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayFast', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayFast payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_payfast',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PayFast', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via PayFast', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'payfast_merchant_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your PayFast merchant ID', 'adifier')
                ),
                array(
                    'id' 		=> 'payfast_merchant_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your PayFast merchant key', 'adifier'),
                ),
                array(
                    'id' 		=> 'payfast_passphrase',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Passphrase (optional)', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your PayFast Passphrase if it is set', 'adifier'),
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
            if( !empty( $_POST['custom_str1'] ) && $_POST['custom_str1'] == 'payfast' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_PayFast::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_PayFast::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_payfast', 'Adifier_PayFast::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PayFast::select_dropdown' );

			add_action( 'wp_ajax_payfast_create_payment', 'Adifier_PayFast::create_payment' );

			add_action( 'adifier_manual_refund_list', 'Adifier_PayFast::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'payfast';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['payfast'] = esc_html__( 'PayFast', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_payfast = adifier_get_option( 'enable_payfast' );
		$payfast_merchant_id = adifier_get_option( 'payfast_merchant_id' );
		$payfast_merchant_key = adifier_get_option( 'payfast_merchant_key' );
		if( $enable_payfast == 'yes' && !empty( $payfast_merchant_id ) && !empty( $payfast_merchant_key ) ){
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
		wp_enqueue_script('adifier-payfast', get_theme_file_uri( '/js/payments/payfast.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-payfast', get_theme_file_uri( '/css/payments/payfast.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="payfast-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/payfast.png' ) ); ?>" alt="payfast" width="148" height="42">
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
	Generate signature
	*/
	static private function generate_signature( $fields ){
		$payfast_passphrase = adifier_get_option( 'payfast_passphrase' );

		$list = array();

		foreach( $fields as $key => $value ) {
		    if( !empty( $value ) ){
		        $list[] = $key.'='.urlencode( trim( $value ) );
		    }
		}

		if( !empty( $payfast_passphrase ) ){
			$list[] = 'passphrase='.urlencode( trim( $payfast_passphrase ) );
		}

		return md5( implode( "&", $list ) );
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$payfast_merchant_id = adifier_get_option( 'payfast_merchant_id' );
			$payfast_merchant_key = adifier_get_option( 'payfast_merchant_key' );

			$form = '';
			ob_start();
			$fields = array(
				'merchant_id'				=> esc_attr( $payfast_merchant_id ),
				'merchant_key'				=> esc_attr( $payfast_merchant_key ),
				'return_url'				=> $_POST['redirectUrl'].'#payfast-return',
				'cancel_url'				=> $_POST['redirectUrl'],
				'notify_url'				=> site_url('/'),
				'm_payment_id' 				=> $order_id,
				'amount'					=> esc_attr( $order['price'] ),
				'item_name'					=> esc_attr( $order_id ),
				'custom_str1'				=> 'payfast',
			);

			$fields["signature"] = self::generate_signature( $fields );

			?>
			<form method="post" action="<?php echo esc_url( self::get_url() ) ?>eng/process" class="payfast-form">
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
	* Print answer for payfast
	*/
	static private function print_answer(){
		echo 'OK';
		die();
	}	

    /*
    Verify payment of payfast
    */
    static public function verify_payment(){
    	if( !empty( $_POST["payment_status"] ) && $_POST["payment_status"] == 'COMPLETE' ){
			$fields = array();

			$_POST = array_map('stripslashes', $_POST);

			foreach( $_POST as $key => $value ){
				if( $key !== 'signature' ){
					$fields[] = $key.'='.urlencode( $value );
				}
			}

			$signature = self::generate_signature( $fields );

			/* if signature is not enabled or it matches then return true */
			if ( $signature || $_POST['signature'] ){
				// Variable initialization
				$validHosts = array(
				    'www.payfast.co.za',
				    'sandbox.payfast.co.za',
				    'w1w.payfast.co.za',
				    'w2w.payfast.co.za',
				);

				$validIps = array();

				foreach( $validHosts as $pfHostname ){
				    $ips = gethostbynamel( $pfHostname );
				    if( $ips !== false ){
				        $validIps = array_merge( $validIps, $ips );
				    }
				}

				// Remove duplicates
				$validIps = array_unique( $validIps );

				$server_variables = adifier_server_variables();
				if( in_array( $server_variables['REMOTE_ADDR'], $validIps ) ){
				    $is_valid = true;
				}
				else{
					$is_valid = false;	
				}
				
			}
			else{
				$is_valid = false;
			}

			if ( $is_valid ){
				Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'payfast',
					'order_transaction_id' 	=> $_POST['pf_payment_id'],
					'order_id'				=> $_POST['m_payment_id'],
					'order_paid'			=> 'yes'
				));

				self::print_answer( 'OK' );
			}
			else{
				self::print_answer( 'OK' );
			}
    	}
    	else{
    		self::print_answer( 'OK' );
    	}
    }

    /*
	* Get URL
    */
    static public function get_url(){
    	$payment_enviroment = adifier_get_option( 'payment_enviroment' );
		return $payment_enviroment == 'test' ? 'https://sandbox.payfast.co.za/' : 'https://www.payfast.co.za/';
    }

}
add_filter( 'init', 'Adifier_PayFast::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PayFast::register_in_options' );
}
?>