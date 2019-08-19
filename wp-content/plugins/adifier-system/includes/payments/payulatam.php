<?php
if( !class_exists('Adifier_PayUlatam') ) {
class Adifier_PayUlatam{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('PayU Latam', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure PayUlatam payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_payulatam',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable PayUlatam', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via PayUlatam', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id'        => 'payulatam_merchantId',
                    'type'      => 'text',
                    'title'     => esc_html__('Merchant ID', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUlatam merchant ID', 'adifier'),
                ),
                array(
                    'id'        => 'payulatam_accountId',
                    'type'      => 'text',
                    'title'     => esc_html__('Account ID', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUlatam account ID', 'adifier'),
                ),
                array(
                    'id'        => 'payulatam_api_key',
                    'type'      => 'text',
                    'title'     => esc_html__('API Key', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUlatam API key', 'adifier'),
                ),
                array(
                    'id'        => 'payulatam_api_login',
                    'type'      => 'text',
                    'title'     => esc_html__('API Login', 'adifier') ,
                    'desc'      => esc_html__('Input your PayUlatam API Login', 'adifier'),
                ),
                array(
                    'id'        => 'payulatam_encryption',
                    'type'      => 'select',
                    'options'	=> array(
                    	'md5'		=> esc_html__( 'MD5', 'adifier' ),
                    	'sha1'		=> esc_html__( 'SHA1', 'adifier' ),
                    	'sha256'	=> esc_html__( 'SHA256', 'adifier' ),
                    ),
                    'title'     => esc_html__('Encription', 'adifier') ,
                    'desc'      => esc_html__('Select signature encription', 'adifier'),
                    'default'	=> 'md5'
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
            if( !empty( $_REQUEST['adifier_verify_payment'] ) && $_REQUEST['adifier_verify_payment'] == 'payulatam' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_PayUlatam::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_PayUlatam::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_payulatam', 'Adifier_PayUlatam::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_PayUlatam::select_dropdown' );

			add_action('wp_ajax_payulatam_create_payment', 'Adifier_PayUlatam::create_payment');

			add_action( 'adifier_manual_refund_list', 'Adifier_PayUlatam::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'payulatam';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['payulatam'] = esc_html__( 'PayUlatam', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_payulatam = adifier_get_option( 'enable_payulatam' );
		$merchantId = adifier_get_option( 'payulatam_merchantId' );
		$accountId = adifier_get_option( 'payulatam_accountId' );
		$api_key = adifier_get_option( 'payulatam_api_key' );
		if( $enable_payulatam == 'yes' && !empty( $merchantId ) && !empty( $accountId ) && !empty( $api_key ) ){
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
		wp_enqueue_script('adifier-payulatam', get_theme_file_uri( '/js/payments/payulatam.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-payulatam', get_theme_file_uri( '/css/payments/payulatam.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="payulatam-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/payulatam.png' ) ); ?>" alt="payulatam" width="148" height="42">
			</a>
		</li>
		<?php
	}

	static private function generate_signature( $fields, $complete_array = false ){
		$api_key = adifier_get_option( 'payulatam_api_key' );
		$payulatam_encryption = adifier_get_option( 'payulatam_encryption' );

		if( !$complete_array ){
			$signature = $api_key.'~'.$fields['merchantId'].'~'.$fields['referenceCode'].'~'.$fields['amount'].'~'.$fields['currency'];
		}
		else{
			$signature = $api_key;
			foreach( $fields as $value ){
				$signature .= '~'.$value;
			}
		}
		return hash( $payulatam_encryption, $signature );
	}

	/*
	Execute refund
	*/
	static public function refund( $order, $order_transaction_id ){
		Adifier_Order::mark_as_refunded( $order );
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$merchantId = adifier_get_option( 'payulatam_merchantId' );
			$accountId = adifier_get_option( 'payulatam_accountId' );
			$payment_enviroment = adifier_get_option( 'payment_enviroment' );

			$form = '';
			$user = wp_get_current_user();
			ob_start();
			$fields = array(
				'merchantId'				=> esc_attr( $merchantId ),
				'accountId'					=> esc_attr( $accountId ),
				'description'				=> esc_attr( $order_id ),
				'referenceCode'				=> esc_attr( $order_id ),
				'amount'					=> esc_attr( $order['price'] ),
				'tax'						=> 0,
				'taxReturnBase'				=> 0,
				'currency'					=> adifier_get_option( 'currency_abbr' ),
				'buyerEmail'				=> $user->user_email,
				'responseUrl'				=> $_POST['responseUrl'].'#payulatam-return',
				'adifier_verify_payment'	=> 'payulatam',
				'confirmationUrl'			=> esc_url( add_query_arg( array( 'adifier_verify_payment' => 'payulatam' ), home_url('/') ) ),
			);

			$fields["signature"] = self::generate_signature( $fields );

			?>
			<form method="post" action="https://<?php echo $payment_enviroment == 'live' ? esc_attr( '' ) : esc_attr( 'sandbox.' ); ?>checkout.payulatam.com/ppp-web-gateway-payu/" class="payulatam-form">
				<?php

				foreach( $fields as $key => $val ){
				    echo '<input name="'.$key.'" value="'.$val.'" type="hidden">';
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
    Verify payment of payultam
    */
    static public function verify_payment(){
    	if( isset( $_POST["state_pol"] ) && $_POST['state_pol'] == '4' ){
    		$temp = explode( '.', $_POST['value'] );
			$fields = array(
				'merchantId'		=> $_POST['merchant_id'],
				'reference_sale'	=> $_POST['reference_sale'],
				'value'				=> ( !empty( $temp[1] ) && $temp[1] == '00' ) ? $temp[0].'.0' : $_POST['value'],
				'currency'			=> $_POST['currency'],
				'state_pol'			=> $_POST['state_pol'],

			);

			$signature = self::generate_signature( $fields, true );

			$is_valid = false;
			/* if signature is not enabled or it matches then return true */
			if ( $signature == $_POST["sign"] ){
				$is_valid = true;
			}

			if ( $is_valid ){
				Adifier_Order::create_order(array(
					'order_payment_type' 	=> 'payulatam',
					'order_transaction_id' 	=> $_POST['transaction_id'],
					'order_id'				=> $_POST['description'],
					'order_paid'			=> 'yes'
				));
			}
    	}
    }   
}
add_filter( 'init', 'Adifier_PayUlatam::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_PayUlatam::register_in_options' );
}
?>