<?php
if( !class_exists('Adifier_Bank') ){
class Adifier_Bank{
	/*
	Add paypal options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Bank Transfer', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Bank Transfer payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_bank',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Bank Transfer', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment Bank Transfer', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' => 'bank_account_name',
                    'type' => 'text',
                    'title' => esc_html__('Bank Account Name', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your bank account name.', 'adifier')
                ),                    
                array(
                    'id' => 'bank_name',
                    'type' => 'text',
                    'title' => esc_html__('Bank Name', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your bank name.', 'adifier')
                ),
                array(
                    'id' => 'bank_account_number',
                    'type' => 'text',
                    'title' => esc_html__('Bank Account Number', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your bank account number.', 'adifier')
                ),
                array(
                    'id' => 'bank_sort_number',
                    'type' => 'text',
                    'title' => esc_html__('Sort Number', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your sort number.', 'adifier')
                ),
                array(
                    'id' => 'bank_iban_number',
                    'type' => 'text',
                    'title' => esc_html__('IBAN Code', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your IBAN code.', 'adifier')
                ),
                array(
                    'id' => 'bank_bic_swift_number',
                    'type' => 'text',
                    'title' => esc_html__('BIC / Swift Code', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your BIC / Swift code.', 'adifier')
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Bank::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Bank::render' );
			}

			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'invoices' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_Bank::enqueue_scripts' );
				add_action( 'wp_footer', 'Adifier_Bank::add_modal' );
			}

			add_action( 'wp_ajax_adifier_bank_invoice_modal', 'Adifier_Bank::show_invoice_payment_details' );

			add_action( 'adifier_refund_bank', 'Adifier_Bank::refund', 10, 2 );

			add_filter( 'adifier_payments_dropdown', 'Adifier_Bank::select_dropdown' );
			add_action( 'wp_ajax_bank_execute_payment', 'Adifier_Bank::execute_payment' );	

			add_action( 'adifier_manual_refund_list', 'Adifier_Bank::add_to_refund_list' );

			add_filter( 'adifier_invoices_waiting_payment_bank', 'Adifier_Bank::invoice_waiting_details', 10, 2 );
		}
	}

	static public function add_to_refund_list( $list ){
		$list[] = 'bank';
		return $list;
	}	

	static public function select_dropdown( $dropdown ){
		$dropdown['bank'] = esc_html__( 'Bank Transfer', 'adifier' );
		return $dropdown;
	}

	/*
	* For bank account we wait for payment so details about it will be accessable from the list of invoiced
	*/
	static public function invoice_waiting_details( $text, $invoice_id ){
		return '<a href="javascript:void(0)" class="af-button bank-invoice-modal" data-id="'.esc_attr( $invoice_id ).'">'.esc_html__( 'Waiting For Payment - Instructions', 'adifier' ).'</a>';
	}


	/*
	Add modal
	*/
	static public function add_modal(){
		?>
		<div class="modal in" id="bankinvoice" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php esc_html_e( 'Instructions', 'adifier' ) ?></h5>
						<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
					</div>
					<div class="modal-body"></div>
					<div class="modal-footer">
						<div class="flex-right">
							<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
						</div>
					</div>
				</div>

			</div>
		</div>		
		<?php
	}

	/*
	Show invoice details
	*/
	static public function show_invoice_payment_details(){
		if( !empty( $_POST['id'] ) ){
			$order_details = Adifier_Order::get_order_details( $_POST['id'] );
			$order_number = get_post_meta( $_POST['id'], 'order_number', true );
			self::_bank_details( $order_number, $order_details['price'] );
		}
		die();
	}

	/*
	Check if we can actually use bank
	*/
	static public function is_enabled(){
		$enable_bank = adifier_get_option( 'enable_bank' );
		if( $enable_bank == 'yes' ){
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
		$dependency = array( 'jquery' );
		if( !empty( $_GE['screen'] ) && $_GET['screen'] !== 'invoices' ){
			$dependency[] = 'adifier-purchase';
		}
		wp_enqueue_script('adifier-bank', get_theme_file_uri( '/js/payments/bank.js' ), $dependency, false, true);
		wp_enqueue_style( 'adifier-bank', get_theme_file_uri( '/css/payments/bank.css' ) );
	}

	/*
	Add paypal t the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="bank-button" >
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/bank.png' ) ); ?>" alt="bank" width="148" height="42">
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
	Builde HTML for bank and order details
	*/
	static private function _bank_details( $order_id, $price, $extra_text = '' ){
        $bank_account_name 		= adifier_get_option( 'bank_account_name' );
        $bank_name 				= adifier_get_option( 'bank_name' );
        $bank_account_number 	= adifier_get_option( 'bank_account_number' );
        $bank_sort_number 		= adifier_get_option( 'bank_sort_number' );
        $bank_iban_number 		= adifier_get_option( 'bank_iban_number' );
        $bank_bic_swift_number 	= adifier_get_option( 'bank_bic_swift_number' );

		?>
			<p>
				<?php 
				printf( 
				    esc_html__( 'Make your payment in amount of %s directly into our bank account. Please use %s as the payment reference. Your order won’t be processed until the funds have cleared in our account.', 'adifier' ).$extra_text,
				    '<strong>'.adifier_price_format( $price ).'</strong>',
				    '<strong>'.$order_id.'</strong>'
				);
				?>
			</p>
			<div class="bank-details-title">
			    <h5><?php esc_html_e( 'Our Bank Details', 'adifier' ) ?></h5>
			    <?php echo  esc_html( $bank_account_name.' - '.$bank_name ); ?>
			</div>
		    <ul class="list-unstyled list-inline bank-details clearfix">
		        <li>
		            <p class="small-bank-title"><?php esc_html_e( 'ACCOUNT NUMBER', 'adifier' ); ?></p>
		            <strong><?php echo esc_html( $bank_account_number ) ?></strong>
		        </li>
		        <li>
		            <p class="small-bank-title"><?php esc_html_e( 'SORT CODE', 'adifier' ); ?></p>
		            <strong><?php echo esc_html( $bank_sort_number ) ?></strong>
		        </li>
		        <li>
		            <p class="small-bank-title"><?php esc_html_e( 'IBAN', 'adifier' ); ?></p>
		            <strong><?php echo esc_html( $bank_iban_number ) ?></strong>
		        </li>
		        <li>
		            <p class="small-bank-title"><?php esc_html_e( 'BIC', 'adifier' ); ?></p>
		            <strong><?php echo esc_html( $bank_bic_swift_number ) ?></strong>
		        </li>
		    </ul>
		<?php        
	}

	/*
	Once user have confirmed payment we can execute it
	*/
	static public function execute_payment(){
		$order = Adifier_Order::get_order();
		$order_id = Adifier_Order::create_transient( $order );

		if( !empty( $order['price'] ) ){
			$result = Adifier_Order::create_order(array(
				'order_payment_type' 	=> 'bank',
				'order_transaction_id' 	=> '',
				'order_id'				=> $order_id,
				'order_paid'			=> 'no'
			));

	        ob_start();
	        self::_bank_details( $order_id, $order['price'], esc_html__( 'These details are also available from the list of your invoices', 'adifier' ) );
			$content = ob_get_contents();
			ob_end_clean();

			$results = array_merge( $result, array( 'success' => $content ) );
		}
		else{
			$results = array( 'error' => '<div class="alert-error">'.esc_html__( 'We were unable to process your payment at the moment', 'adifier' ).'</div>' );
		}

		echo json_encode( $results );
		die();
	}

}
add_filter( 'init', 'Adifier_Bank::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Bank::register_in_options' );
}
?>