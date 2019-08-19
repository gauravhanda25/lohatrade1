<?php
if( !class_exists('Adifier_Offline') ){
class Adifier_Offline{
	/*
	Add paypal options to the theme options
	*/
	static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('Offline Payment', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure Offline Payment payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_offline',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable Offline Payment', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment Offline Payment', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' => 'offline_instructions',
                    'type' => 'textarea',
                    'title' => esc_html__('Instructions', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Instructions for user on how to pay. To place order ID use %order to place price use %price', 'adifier')
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
				add_action( 'wp_enqueue_scripts', 'Adifier_Offline::enqueue_scripts' );
				add_action( 'adifier_payment_methods', 'Adifier_Offline::render' );
			}

			if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'invoices' ) ) ){
				add_action( 'wp_enqueue_scripts', 'Adifier_Offline::enqueue_scripts' );
				add_action( 'wp_footer', 'Adifier_Offline::add_modal' );
			}

			add_action( 'wp_ajax_adifier_offline_invoice_modal', 'Adifier_Offline::show_invoice_payment_details' );

			add_action( 'adifier_refund_offline', 'Adifier_Offline::refund', 10, 2 );

			add_filter( 'adifier_payments_dropdown', 'Adifier_Offline::select_dropdown' );
			add_action( 'wp_ajax_offline_execute_payment', 'Adifier_Offline::execute_payment' );	

			add_action( 'adifier_offline_refund_list', 'Adifier_Offline::add_to_refund_list' );

			add_filter( 'adifier_invoices_waiting_payment_offline', 'Adifier_Offline::invoice_waiting_details', 10, 2 );
		}
	}

	static public function add_to_refund_list( $list ){
		$list[] = 'offline';
		return $list;
	}	

	static public function select_dropdown( $dropdown ){
		$dropdown['offline'] = esc_html__( 'Offline Payment', 'adifier' );
		return $dropdown;
	}

	/*
	* For offline account we wait for payment so details about it will be accessable from the list of invoiced
	*/
	static public function invoice_waiting_details( $text, $invoice_id ){
		return '<a href="javascript:void(0)" class="af-button offline-invoice-modal" data-id="'.esc_attr( $invoice_id ).'">'.esc_html__( 'Waiting For Payment - Instructions', 'adifier' ).'</a>';
	}


	/*
	Add modal
	*/
	static public function add_modal(){
		?>
		<div class="modal in" id="offlineinvoice" tabindex="-1" role="dialog">
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
			self::_offline_details( $order_number, $order_details['price'] );
		}
		die();
	}

	/*
	Check if we can actually use offline
	*/
	static public function is_enabled(){
		$enable_offline = adifier_get_option( 'enable_offline' );
		$offline_instructions = adifier_get_option( 'offline_instructions' );
		if( $enable_offline == 'yes' && !empty($offline_instructions) ){
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
		wp_enqueue_script('adifier-offline', get_theme_file_uri( '/js/payments/offline.js' ), $dependency, false, true);
		wp_enqueue_style( 'adifier-offline', get_theme_file_uri( '/css/payments/offline.css' ) );
	}

	/*
	Add paypal t the list of the available payments in the frontend
	*/
	static public function render(){
		?>	
		<li>
			<a href="javascript:void(0);" id="offline-button" >
				<img src="<?php echo esc_url( get_theme_file_uri( '/images/offline.png' ) ); ?>" alt="offline" width="148" height="42">
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
	Builde HTML for offline and order details
	*/
	static private function _offline_details( $order_id, $price ){
        $offline_instructions = adifier_get_option( 'offline_instructions' );

		?>
			<p><?php echo str_replace( array( '%order', '%price' ), array( $order_id, adifier_price_format( $price ) ), $offline_instructions ) ?></p>
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
				'order_payment_type' 	=> 'offline',
				'order_transaction_id' 	=> '',
				'order_id'				=> $order_id,
				'order_paid'			=> 'no'
			));

	        ob_start();
	        self::_offline_details( $order_id, $order['price'] );
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
add_filter( 'init', 'Adifier_Offline::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_Offline::register_in_options' );
}
?>