<?php
if( !class_exists('Adifier_WalletOne') ) {
class Adifier_WalletOne{
	/*
	Add stripe options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Wallet one', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Wallet one payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_walletone',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Wallet one', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via Wallet one', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' 		=> 'walletone_merchant_id',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Merchant ID', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Wallet One merchant ID', 'adifier')
                ),
                array(
                    'id' 		=> 'walletone_encryption',
                    'type' 		=> 'select',
                    'title' 	=> esc_html__('Encryption', 'adifier'),
                    'compiler' 	=> 'true',
                    'options'	=> array(
                    	'none'		=> esc_html__( 'None', 'adifier' ),
                    	'md5'		=> esc_html__( 'MD5', 'adifier' ),
                    	'sha1'		=> esc_html__( 'SHA1', 'adifier' )
                    ),
                    'desc' 		=> esc_html__('Select encryption type for your store', 'adifier')
                ),
                array(
                    'id' 		=> 'walletone_secret_key',
                    'type' 		=> 'text',
                    'title' 	=> esc_html__('Secret Key', 'adifier'),
                    'compiler' 	=> 'true',
                    'desc' 		=> esc_html__('Input your Wallet One secret key', 'adifier'),
                    'required' 	=> array('walletone_encryption','!=','none')
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
            if( !empty( $_POST['adifier_verify_payment'] ) && $_POST['adifier_verify_payment'] == 'walletone' ){
                self::verify_payment();
            }			
			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_WalletOne::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_WalletOne::render' );
			}

			/* this is executed fro the backgriound */
			add_action( 'adifier_refund_walletone', 'Adifier_WalletOne::refund', 10, 2 );
			add_filter( 'adifier_payments_dropdown', 'Adifier_WalletOne::select_dropdown' );

			add_action( 'wp_ajax_walletone_create_payment', 'Adifier_WalletOne::create_payment' );

			add_action( 'adifier_manual_refund_list', 'Adifier_WalletOne::add_to_refund_list' );
		}
	}  

	static public function add_to_refund_list( $list ){
		$list[] = 'walletone';
		return $list;
	}

	static public function select_dropdown( $dropdown ){
		$dropdown['walletone'] = esc_html__( 'Wallet One', 'adifier' );
		return $dropdown;
	}


	/*
	First checn if stripe is configured and enabled
	*/
	static public function is_enabled(){
		$enable_walletone = adifier_get_option( 'enable_walletone' );
		$walletone_merchant_id = adifier_get_option( 'walletone_merchant_id' );
		if( $enable_walletone == 'yes' && !empty( $walletone_merchant_id ) ){
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
		wp_enqueue_script('adifier-walletone', get_theme_file_uri( '/js/payments/walletone.js' ), array('jquery', 'adifier-purchase'), false, true);
		wp_enqueue_style( 'adifier-walletone', get_theme_file_uri( '/css/payments/walletone.css' ) );
	}

	/*
	Add stripe to the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="walletone-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/walletone.png' ) ); ?>" alt="walletone" width="148" height="42">
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
	* get 3 digit code for currency
	*/
	static private function get_digit_currency(){
		$currency_abbr = adifier_get_option( 'currency_abbr' );
		$currencies = array(
			'RUB'	=> 643,
			'ZAR'	=> 710,
			'USD'	=> 840,
			'EUR'	=> 978,
			'UAH'	=> 980,
			'KZT'	=> 398,
			'BYN'	=> 974,
			'TJS'	=> 972,
			'AZN'	=> 944,
			'PLN'	=> 985,
		);

		return !empty( $currencies[$currency_abbr] ) ? $currencies[$currency_abbr] : '';
	}

	static private function generate_signature( $fields, $prefix = '' ){
		$walletone_encryption = adifier_get_option( 'walletone_encryption' );
		$walletone_secret_key = adifier_get_option( 'walletone_secret_key' );

		if( in_array( $walletone_encryption, array( 'md5', 'sha1' ) ) ){

			$fieldValues = "";

			foreach( $fields as $value ) {
			    if( is_array( $value ) ){
			        foreach( $value as $v ){
			            $fieldValues .= stripslashes( $v );
			        }
			    }
			    else{
			        $fieldValues .= stripslashes( $value );
			    }
			}

			if( $walletone_encryption == 'md5' ){
				$signature = md5( $prefix.$fieldValues.$walletone_secret_key );
			}
			else{
				$signature = sha1( $prefix.$fieldValues.$walletone_secret_key );
			}

			$signature = adifier_b64_encode( pack( "H*", $signature ) );

			return $signature;

		}
		else{
			return false;
		}
	}

	/*
	Return data for payment initiation
	*/
	static public function create_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );


		if( !empty( $order['price'] ) ){

			$walletone_merchant_id = adifier_get_option( 'walletone_merchant_id' );

			$form = '';
			ob_start();
			$fields = array(
				'WMI_MERCHANT_ID'			=> esc_attr( $walletone_merchant_id ),
				'WMI_PAYMENT_AMOUNT'		=> esc_attr( $order['price'] ),
				'WMI_CURRENCY_ID'			=> esc_attr( self::get_digit_currency() ),
				'WMI_DESCRIPTION'			=> esc_attr( $order_id ),
				'WMI_SUCCESS_URL'			=> $_POST['redirectUrl'].'#walletone-return',
				'WMI_FAIL_URL'				=> $_POST['redirectUrl'].'#walletone-return',
				'WMI_PAYMENT_NO'			=> esc_attr( $order_id ),
				'adifier_verify_payment'	=> 'walletone'
			);

			uksort($fields, "strcasecmp");

			$signature = self::generate_signature( $fields );
			if( $signature ){
				$fields["WMI_SIGNATURE"] = $signature;
			}

			?>
			<form method="post" action="https://wl.walletone.com/checkout/checkout/Index" class="walletone-form">
				<?php

				foreach( $fields as $key => $val ){
				    if( is_array( $val ) ){
				        foreach( $val as $value ){
				            echo '<input name="'.$key.'" value="'.$value.'" type="hidden">';
				        }
				    }
				    else{    
				        echo '<input name="'.$key.'" value="'.$val.'" type="hidden">';
				    }
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
	* Print answer for wallet one
	*/
	static private function print_answer($result, $description = ''){
		echo "WMI_RESULT=".$result;
		if( !empty( $description ) ){
			echo "&WMI_DESCRIPTION=".urlencode( $description );
		}
		die();
	}	

    /*
    Verify payment of walletone
    */
    static public function verify_payment(){
    	if( isset( $_POST["WMI_ORDER_STATE"] ) && isset( $_POST["WMI_PAYMENT_NO"] ) ){
			$fields = array();

			foreach( $_POST as $name => $value ){
				if( $name !== "WMI_SIGNATURE" && $name !== 'adifier_verify_payment' ){
					$fields[$name] = $value;
			
				}
			}

			ksort( $fields, SORT_STRING );

			$signature = self::generate_signature( $fields, 'walletone' );

			/* if signature is not enabled or it matches then return true */
			if ( !$signature || $signature == $_POST["WMI_SIGNATURE"] ){
				$is_valid = true;
			}
			else{
				$is_valid = false;
			}

			if ( $is_valid ){
				if ( strtoupper( $_POST["WMI_ORDER_STATE"] ) == "ACCEPTED" ){
					Adifier_Order::create_order(array(
						'order_payment_type' 	=> 'walletone',
						'order_transaction_id' 	=> $_POST['WMI_ORDER_ID'],
						'order_id'				=> $_POST['WMI_PAYMENT_NO'],
						'order_paid'			=> 'yes'
					));

					self::print_answer( 'OK' );
				}
				else{
					self::print_answer( 'RETRY', esc_html__( 'Unkown order status', 'adifier' ).' '.$_POST["WMI_ORDER_STATE"] );
				}
			}
			else{
				self::print_answer( 'RETRY', esc_html__( 'Wrong digital signature', 'adifier' ).' '.$_POST["WMI_SIGNATURE"] );
			}
    	}
    	else{
    		self::print_answer( 'OK' );
    	}
    }

}
add_filter( 'init', 'Adifier_WalletOne::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_WalletOne::register_in_options' );
}
?>