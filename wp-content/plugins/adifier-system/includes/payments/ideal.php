<?php
if( !class_exists('Adifier_iDEAL') ){
class Adifier_iDEAL{
    
    /*
    Add paypal options to the theme options
    */
    static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('iDEAL', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure iDEAL payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_ideal',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable iDEAL', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via iDEAL', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' => 'ideal_live_key',
                    'type' => 'text',
                    'title' => esc_html__('Mollie Live API Key', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your mollie live key to connect to iDEAL', 'adifier')
                ),
                array(
                    'id' => 'ideal_test_key',
                    'type' => 'text',
                    'title' => esc_html__('Mollie Test API Key', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your mollie test key to connect to iDEAL', 'adifier')
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
            if( !empty( $_GET['adifier_verify_payment'] ) && $_GET['adifier_verify_payment'] == 'ideal' ){
                self::verify_payment();
            }
            if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
                add_action( 'wp_enqueue_scripts', 'Adifier_iDEAL::enqueue_scripts' );
                add_action( 'adifier_payment_methods', 'Adifier_iDEAL::render' );
            }

            /* this is executed fro the backgriound */
            add_action( 'adifier_refund_ideal', 'Adifier_iDEAL::refund', 10, 2 );
            add_filter( 'adifier_payments_dropdown', 'Adifier_iDEAL::select_dropdown' );

            add_action( 'adifier_payment_response', 'Adifier_iDEAL::payment_response' );

            add_action( 'wp_ajax_ideal_create_payment', 'Adifier_iDEAL::create_payment' );
        }
    }

    static public function select_dropdown( $dropdown ){
        $dropdown['ideal'] = esc_html__( 'iDEAL', 'adifier' );
        return $dropdown;
    }


    /*
    Check if we can actually use paypal
    */
    static public function is_enabled(){
        $ideal_live_key = adifier_get_option( 'ideal_live_key' );
        $ideal_test_key = adifier_get_option( 'ideal_test_key' );
        if( !empty( $ideal_live_key ) && !empty( $ideal_test_key ) ){
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
        wp_enqueue_script('adifier-ideal', get_theme_file_uri( '/js/payments/ideal.js' ), array('jquery', 'adifier-purchase'), false, true);
        wp_enqueue_style( 'adifier-ideal', get_theme_file_uri( '/css/payments/ideal.css' ) );
    }

    /*
    Add paypal t the list of the available payments in the frontend
    */
    static public function render(){
        ?>  
        <li>
            <a href="javascript:void(0);" id="ideal-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
                <img src="<?php echo esc_url( get_theme_file_uri( '/images/ideal.png' ) ); ?>" alt="ideal" width="148" height="42">
            </a>
        </li>
        <?php
    }


    /*
    Execute refund
    */
    static public function refund( $order, $order_transaction_id ){
        $data = self::http( 'payments/'.$order_transaction_id.'/refunds' );
        if( !empty( $data->payment ) && $data->payment->status == 'refunded' ){
            Adifier_Order::mark_as_refunded( $order );
        }
    }

    /*
    Create iDEAL payment
    */
    public function create_payment( $gateway ){
        $order = Adifier_Order::get_order();
        $order_id = Adifier_Order::create_transient( $order );

        if( !empty( $order['price'] ) ){
            $result = Adifier_Order::create_order(array(
                'order_payment_type'    => 'ideal',
                'order_transaction_id'  => '',
                'order_id'              => $order_id,
                'order_paid'            => 'no'
            ));
            if( !empty( $result['order_id'] ) ){
                $data = self::http( 'payments', array(
                    'amount'        => $order['price'],
                    'description'   => $order_id,
                    'redirectUrl'   => $_POST['redirectUrl'].'#ideal-return',
                    'webhookUrl'    => esc_url( add_query_arg( array( 'adifier_verify_payment' => 'ideal' ), home_url('/') ) ),
                    'metadata'      => array(
                        'order_id'      => $result['order_id']
                    ),
                    'method'        => 'ideal'
                ));

                if( !empty( $data->id ) ){
                    update_post_meta( $result['order_id'], 'order_transaction_id', $data->id );
                    $response = array( 'paymentID' => $data->id, 'paymentUrl' => $data->links->paymentUrl );
                }
                else{
                    wp_delete_post( $result['order_id'], true );
                    $response = array( 'error' => esc_html__( 'We could not create payment at this point, try again', 'adifier' ) );
                }                
            }
            else{
                $response = array( 'error' => esc_html__( 'We could not create order at this point, try again', 'adifier' ) );
            }

            echo json_encode( $response );
            die();
        }
    }

    /*
    Verify payment of ideal
    */
    static public function verify_payment(){
        $data = self::http( 'payments/'.$_POST['id'], array(), 'GET');

        if( !empty( $data->status ) ){
            if( $data->status == 'paid' ){
                Adifier_Order::apply_after_verification( $data->metadata->order_id );
            }
            else if( in_array( $data->status, array( 'cancelled', 'expired', 'failed', 'charged_back' ) ) ){
                wp_delete_post( $data->metadata->order_id, true );
            }
        }
    }

    /*
    Get API key for iDEAL
    */
    static public function get_api_key(){
        $payment_enviroment = adifier_get_option( 'payment_enviroment' );
        if( $payment_enviroment == 'live' ){
            return adifier_get_option( 'ideal_live_key' );
        }
        else{
            return adifier_get_option( 'ideal_test_key' );
        }
    }

    /*
    iDEAL API requests
    */
    static public function http( $checkpoint, $data = array(), $method = 'POST' ){
        $response = wp_remote_request( 'https://api.mollie.nl/v1/'.$checkpoint, array(
            'method' => $method,
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Authorization' => 'Bearer '.self::get_api_key()
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
add_filter( 'init', 'Adifier_iDEAL::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_iDEAL::register_in_options' );
}
?>