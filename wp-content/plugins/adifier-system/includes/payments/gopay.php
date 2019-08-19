<?php
if( !class_exists('Adifier_GoPay') ){
class Adifier_GoPay{
    
    /*
    Add paypal options to the theme options
    */
    static public function register_in_options( $sections ){
        $sections[] = array(
            'title'     => esc_html__('GoPay', 'adifier') ,
            'icon'      => '',
            'subsection'=> true,
            'desc'      => esc_html__('Configure GoPay payment.', 'adifier'),
            'fields'    => array(
                array(
                    'id'        => 'enable_gopay',
                    'type'      => 'select',
                    'options'   => array(
                        'yes'       => esc_html__( 'Yes', 'adifier' ),
                        'no'        => esc_html__( 'No', 'adifier' )
                    ),
                    'title'     => esc_html__('Enable GoPay', 'adifier') ,
                    'desc'      => esc_html__('Enable or disable payment via GoPay', 'adifier'),
                    'default'   => 'no'
                ),
                array(
                    'id' => 'gopay_client_id',
                    'type' => 'text',
                    'title' => esc_html__('Client ID', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your client id to connect to GoPay', 'adifier')
                ),
                array(
                    'id' => 'gopay_client_secret',
                    'type' => 'text',
                    'title' => esc_html__('Client Secret', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your client secret to connect to GoPay', 'adifier')
                ),
                array(
                    'id' => 'gopay_goid',
                    'type' => 'text',
                    'title' => esc_html__('GoID', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your goid to connect to GoPay', 'adifier')
                ),
                array(
                    'id' => 'gopay_secure_key',
                    'type' => 'text',
                    'title' => esc_html__('Secure Key', 'adifier'),
                    'compiler' => 'true',
                    'desc' => esc_html__('Input your secure key to connect to GoPay', 'adifier')
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
            if( !empty( $_GET['gopay_return'] ) && !empty( $_GET['id'] ) ){
                self::execute_payment();
            }
            if( !empty( $_REQUEST['adifier_verify_payment'] ) && $_REQUEST['adifier_verify_payment'] == 'gopay' && !empty( $_REQUEST['id'] ) ){
                self::verify_payment();
            }

            if( !empty( $_GET['screen'] ) && in_array( $_GET['screen'], array( 'ads', 'acc_pay' ) ) ){
                add_action( 'wp_enqueue_scripts', 'Adifier_GoPay::enqueue_scripts' );
                add_action( 'adifier_payment_methods', 'Adifier_GoPay::render' );
            }

            /* this is executed fro the backgriound */
            add_action( 'adifier_refund_gopay', 'Adifier_GoPay::refund', 10, 2 );
            add_filter( 'adifier_payments_dropdown', 'Adifier_GoPay::select_dropdown' );

            add_action( 'wp_ajax_gopay_create_payment', 'Adifier_GoPay::create_payment' );
        }
    }

    static public function select_dropdown( $dropdown ){
        $dropdown['gopay'] = esc_html__( 'GoPay', 'adifier' );
        return $dropdown;
    }


    /*
    Check if we can actually use paypal
    */
    static public function is_enabled(){
        $enable_gopay = adifier_get_option( 'enable_gopay' );
        $gopay_client_id = adifier_get_option( 'gopay_client_id' );
        $gopay_client_secret = adifier_get_option( 'gopay_client_secret' );
        $gopay_goid = adifier_get_option( 'gopay_goid' );
        $gopay_secure_key = adifier_get_option( 'gopay_secure_key' );
        if( $enable_gopay == 'yes' && !empty( $gopay_client_id ) && !empty( $gopay_client_secret ) && !empty( $gopay_goid ) && !empty( $gopay_secure_key ) ){
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
        wp_enqueue_script('adifier-gopay', get_theme_file_uri( '/js/payments/gopay.js' ), array('jquery', 'adifier-purchase'), false, true);
        wp_enqueue_style( 'adifier-gopay', get_theme_file_uri( '/css/payments/gopay.css' ) );
    }

    /*
    Add paypal t the list of the available payments in the frontend
    */
    static public function render(){
        ?>  
        <li>
            <a href="javascript:void(0);" id="gopay-button" data-returnmessage="<?php esc_attr_e( 'You payment is being processed and once it is approved you will receive purchased goods', 'adifier' ) ?>">
                <img src="<?php echo esc_url( get_theme_file_uri( '/images/gopay.png' ) ); ?>" alt="gopay" width="148" height="42">
            </a>
        </li>
        <?php
    }


    /*
    Execute refund
    */
    static public function refund( $order, $order_transaction_id ){
        $order_details = Adifier_Order::get_order_details( $order->ID );
        $data = self::http( 'payments/payment/'.$order_transaction_id.'/refund', array(
            'amount'    => $order_details['price'] * 100
        ));
        if( !empty( $data->result ) && in_array( $data->result, array( 'FINISHED', 'ACCEPTED' ) ) ){
            Adifier_Order::mark_as_refunded( $order );
        }
    }

    /*
    Create GoPay payment
    */
    static public function create_payment( $gateway ){
        $order = Adifier_Order::get_order();
        $order_id = Adifier_Order::create_transient( $order );

        if( !empty( $order['price'] ) ){
            $response = self::http('payments/payment', array(
                'target'        => array(
                    'type'  => 'ACCOUNT',
                    'goid'  => adifier_get_option( 'gopay_goid' )
                ),
                'amount'        => $order['price']*100,
                'currency'      => adifier_get_option( 'currency_abbr' ),
                'order_number'  => $order_id,
                'items'         => array(
                    array(
                        'type'          => 'ITEM', 
                        'name'          => $order_id,
                        'amount'        => $order['price']*100,
                        'count'         => 1,
                        'vat_rate'      => 0
                    )
                ),
                'callback'      => array(
                    'return_url'        => add_query_arg( array( 'gopay_return' => '1' ), remove_query_arg( 'id', $_POST['redirectUrl'] ) ),
                    'notification_url'  => add_query_arg( array( 'adifier_verify_payment' => 'gopay' ), home_url('/') ),
                )
            ));
            if( empty( $response->state ) || $response->state != 'CREATED' ){
                $response = array( 'error' => esc_html__( 'Could not create payment', 'adifier' ) );
            }
            else{
                set_transient( 'gopay_'.$response->id, $response->id, 900 );
            }

            echo json_encode( $response );
            die();
        }
    }

    /*
    Create payment
    */
    static public function execute_payment(){
        $order_id = Adifier_Order::get_order_by_transaction_id( $_GET['id'] );
        if( $order_id === false ){
            $response = self::http('payments/payment/'.$_GET['id'], array(), 'GET');
            if( !empty( $response->state ) && in_array( $response->state, array( 'PAID', 'CREATED' ) ) ){
                $result = Adifier_Order::create_order(array(
                    'order_payment_type'    => 'gopay',
                    'order_transaction_id'  => $response->id,
                    'order_id'              => $response->order_number,
                    'order_paid'            => $response->state == 'PAID' ? 'yes' : 'no'
                ));
            }
        }
    }


    /*
    Create payment
    */
    static public function verify_payment(){
        $response = self::http('payments/payment/'.$_GET['id'], array(), 'GET');
        if( !empty( $response->state ) ){
            $order_id = Adifier_Order::get_order_by_transaction_id( $response->id );
            if( $order_id !== false ){
                if( $response->state == 'PAID' ){
                    Adifier_Order::apply_after_verification( $order_id );    
                }
                else if( in_array( $response->state, array( 'CANCELED', 'TIMEOUTED' )) ){
                    wp_delete_post( $order_id, true );
                }                
            }
        }
    }

    /*
    Get access token
    */
    static public function get_access_token(){
        $access_token = get_transient( 'adifier_gopay_access_token' );
        if( !empty( $access_token ) ){
            return $access_token;
        }
        else{
            $response = wp_remote_request( self::_get_url().'oauth2/token', array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(
                    'Authorization' => 'Basic '.adifier_b64_encode(adifier_get_option('gopay_client_id').':'.adifier_get_option('gopay_client_secret')),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                ),
                'body' => array(
                    'grant_type'    => 'client_credentials',
                    'scope'         => 'payment-all'
                ),
                'cookies' => array()
            ));


            if ( is_wp_error( $response ) ) {
            } 
            else{           
                $response = json_decode( $response['body']);         
            }            

            if( !empty( $response->access_token ) ){
                set_transient( 'adifier_gopay_access_token', $response->access_token, $response->expires_in );
                return $response->access_token;
            }
            else{
                return false;
            }
        }
    }

    /*
    Get gopay URL
    */
    static private function _get_url(){
        $url = 'https://gate.gopay.cz/api/';
        if( self::_is_test_enviroment() ){
            $url = 'https://gw.sandbox.gopay.com/api/';
        }

        return $url;
    }

    /*
    is test envioment
    */
    static private function _is_test_enviroment(){
        $payment_enviroment = adifier_get_option( 'payment_enviroment' );
        if( $payment_enviroment == 'test' ){
            return true;
        }
        else{
            return false;
        }
    }

    /*
    GoPay API requests
    */
    static public function http( $checkpoint, $data = array(), $method = 'POST'){
        $response = wp_remote_request( self::_get_url().$checkpoint, array(
            'method' => $method,
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Authorization' => 'Bearer '.self::get_access_token(),
                'Accept'        => 'application/json',
                'Content-Type'  => $checkpoint == 'payments/payment' ? 'application/json' : 'application/x-www-form-urlencoded'
            ),
            'body' => !empty( $data ) ? $checkpoint == 'payments/payment' ? json_encode( $data ) : $data : '',
            'cookies' => array()
        ));

        if ( is_wp_error( $response ) ) {
        } 
        else{           
           return json_decode( $response['body']);         
        }
    }
}
add_filter( 'init', 'Adifier_GoPay::start_payment' );
add_filter( 'adifier_payment_options', 'Adifier_GoPay::register_in_options' );
}
?>