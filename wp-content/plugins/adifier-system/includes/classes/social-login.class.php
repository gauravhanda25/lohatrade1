<?php
if( !class_exists( 'Adifier_Social_Login' ) ){
class Adifier_Social_Login{

	public $networks;

	public function __construct(){
		add_action('adifier_social_login_button', array( $this, 'show_buttons' ) );
		add_action('wp_enqueue_scripts', array( $this, 'load_scripts' ), 12 );

		$this->networks = array(
			'facebook' 	=> array(
				'title' 	=> esc_html__( 'Facebook', 'adifier' ),
				'data'		=> array()
			),
			'twitter'	=> array(
				'title'		=> esc_html__( 'Twitter', 'adifier' ),
				'data'		=> array()
			),
			'google'	=> array(
				'title'		=> esc_html__( 'Google', 'adifier' ),
				'data'		=> array()
			)			
		);	

		$this->start_social_login();	
	}

	public function start_social_login(){
		foreach( $this->networks as $network => &$data ){
			$data['data'] = array(
				'id'		=> adifier_get_option( $network.'_app_id' ),
				'secret'	=> adifier_get_option( $network.'_app_secret' )
			);
		}

		if( !empty( $_GET['adifier-connect'] ) ){
			switch( $_GET['adifier-connect'] ){
				case 'facebook' : $this->facebook_connect(); break;
				case 'twitter' : $this->twitter_connect(); break;
				case 'google' : $this->google_connect(); break;
			}
		}

		add_action( 'init', array( $this, 'callback' ) );
	}

	public function callback(){
		if( !empty( $_GET['adifier-callback'] ) ){
			switch( $_GET['adifier-callback'] ){
				case 'facebook' : $this->facebook_callback(); break;
				case 'twitter' : $this->twitter_callback(); break;
				case 'google' : $this->google_callback(); break;
			}
		}
	}

	public function load_scripts(){
		wp_localize_script( 'adifier-custom', 'adifier_sc', array(
			'facebook' 	=> site_url( 'index.php?adifier-connect=facebook' ),
			'twitter' 	=> site_url( 'index.php?adifier-connect=twitter' ),
			'google' 	=> site_url( 'index.php?adifier-connect=google' )
		));
	}

	public function show_buttons(){
		foreach( $this->networks as $network => $data ){
			if( !empty( $data['data']['id'] ) && !empty( $data['data']['secret'] ) ){
				?>
				<li>
					<a href="javascript:void(0);" class="<?php echo esc_attr( $network ) ?>">
						<i class="aficon-<?php echo esc_attr( $network ) ?>"></i>
						<span><?php echo esc_html( $data['title'] ) ?></span>
					</a>
				</li>
				<?php
			}
		}
	}

	private function _login_user( $userdata ){
		$user_id = $this->_get_registered_user( $userdata['af_user_id'] );
		if( empty( $user_id ) ){
			$user_id = email_exists( $userdata['user_email'] );
			if( !empty( $user_id ) ){
				update_user_meta( $user_id, 'af_user_id', $userdata['af_user_id'] );
			}
			else{
				$userdata['user_login'] = $this->_unique_username( $userdata['user_login'] );
				$userdata['user_pass'] = wp_generate_password();
				$user_id = wp_insert_user( $userdata );
				update_user_meta( $user_id, 'af_user_id', $userdata['af_user_id'] );
			}
		}
		if( !empty( $user_id ) ){
			update_user_meta( $user_id, 'adifier_avatar_url', $userdata['user_avatar'] );
		}

		wp_set_auth_cookie( $user_id );
		?>
		<html>
			<head>
				<script>
					function init() {
						window.opener.location.reload();
						window.close();
					}
				</script>
			</head>
			<body onload="init();"></body>
		</html>
		<?php		
	}

	private function _get_registered_user( $af_user_id ){
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'af_user_id' AND meta_value = %s", $af_user_id));
	}

	private function _unique_username( $user_login, $c = 1 ) {
		if ( username_exists( $user_login ) ) {
			if ( $c > 5 ){
				$append = '_'.substr(md5($user_login),0,3) . $c;
			}
			else{
				$append = $c;
			}

			$user_login = $user_login.$append;
			return $this->_unique_username( $user_login, ++$c );
		} 
		else{
			return $user_login;
		}
	}	

	/***---------FACEBOOK CONNECT----------***/
	public function facebook_connect(){
		wp_redirect( 'https://graph.facebook.com/oauth/authorize?scope=email&client_id='.esc_attr( $this->networks['facebook']['data']['id'] ).'&redirect_uri='.site_url('index.php?adifier-callback=facebook') );
		die();
	}

	public function facebook_callback(){
		if( isset( $_GET['code'] ) ){
			$response = $this->_http_facebook( 'oauth/access_token', array(
				'client_id'		=> $this->networks['facebook']['data']['id'],
				'redirect_uri'	=> site_url( 'index.php?adifier-callback=facebook' ),
				'client_secret'	=> $this->networks['facebook']['data']['secret'],
				'code'			=> urlencode( $_GET['code'] )
			), 'GET');

			if( !empty( $response->access_token ) ){
				$user = $this->_http_facebook( 'me',  array(
					'access_token'		=> $response->access_token,
					'fields'			=> 'id,name,email,first_name,last_name'
				), 'GET');

				if( !empty( $user->id ) ){
					$this->_login_user(array(
						'af_user_id'	=> $user->id,
						'user_email' 	=> $user->email,
						'first_name' 	=> $user->first_name,
						'last_name' 	=> $user->last_name,
						'user_login'	=> sanitize_title( $user->first_name.$user->last_name ),
						'user_avatar'	=> '//graph.facebook.com/'.$user->id.'/picture?type=normal'
					));
				}
				else{
					esc_html_e( 'Could not retrieve user data', 'adifier' );
				}
			}
			else{
				esc_html_e( 'Could not generate access_token', 'adifier' );
			}
		} 
		else {
			$this->facebook_connect();
		}
		die();
	}


	private function _http_facebook( $checkpoint, $data, $method = 'POST' ) {
	    $response = wp_remote_request( 'https://graph.facebook.com/'.$checkpoint, array(
	        'method' 		=> $method,
	        'timeout' 		=> 45,
	        'redirection' 	=> 5,
	        'httpversion' 	=> '1.0',
	        'blocking' 		=> true,
	        'body' => $data,
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
		   	return json_decode( $response['body']);		   	
		}
	}
	/***--------- END FACEBOOK CONNECT----------***/

	/***---------TWITTER CONNECT----------***/
	public function twitter_connect(){
		$response = $this->_http_twitter( 'oauth/request_token', array(
			'oauth_callback' => site_url('/index.php?adifier-callback=twitter')
		));

		if( !empty( $response['oauth_callback_confirmed'] ) && $response['oauth_callback_confirmed'] == true ){
			wp_redirect( 'https://api.twitter.com/oauth/authenticate?oauth_token='.$response['oauth_token'] );
		}
		else{
			esc_html_e( 'Could not generate oauth_token since credentials are invalid', 'adifier' );
		}
		die();
	}

	public function twitter_callback(){
		if( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) ){
			$response = $this->_http_twitter( 'oauth/access_token', array(
				'oauth_token' => $_GET['oauth_token']
			), array(
				'oauth_verifier'	=> $_GET['oauth_verifier']
			));

			if( !empty( $response['oauth_token'] ) && !empty( $response['oauth_token_secret'] ) ){
				$user = $this->_http_twitter( '1.1/account/verify_credentials.json', array(
					'oauth_token' 			=> $response['oauth_token'],
					'oauth_token_secret' 	=> $response['oauth_token_secret']
				), array(), 'GET');

				if( !empty( $user->name ) ){
					$site_url = parse_url( site_url() );
					$this->_login_user(array(
						'af_user_id'	=> $user->id,
						'user_email' 	=> 'tw_'.md5( $user->id ).'@'.$site_url['host'],
						'first_name' 	=> $user->name,
						'last_name' 	=> '',
						'user_login'	=> $user->screen_name,
						'user_avatar' 	=> !empty( $user->profile_image_url ) ? str_replace( 'http:', '', $user->profile_image_url ) : ''
					));					
				}
				else{
					esc_html_e( 'Could not retrieve user data', 'adifier' );	
				}
			}
			else{
				esc_html_e( 'Could not generate oauth_token_secret since credentials are invalid', 'adifier' );
			}

		} 
		else {
			esc_html_e( 'Login error', 'adifier' );
		}
		die();
	}

	private function _http_twitter( $checkpoint, $authentication, $data = array(), $method = 'POST' ){

		$authentication = array_merge(array(
			'oauth_consumer_key'		=> $this->networks['twitter']['data']['id'],
			'oauth_nonce'				=> md5( microtime().mt_rand() ),
			'oauth_signature_method'	=> 'HMAC-SHA1',
			'oauth_timestamp'			=> time(),
			'oauth_version'				=> '1.0'
		), $authentication);

		ksort( $authentication );

		$auth_data = array();
		$sign_data = array();
		foreach( $authentication as $key => $value ){
			$auth_data[] =  $key.'="'.rawurlencode( $value ).'"';
			$sign_data[] = $key.'='.rawurlencode( $value );
		}

		$url = 'https://api.twitter.com/'.$checkpoint;

		$base_signature = $method.'&'.rawurlencode( $url ).'&'.rawurlencode( implode('&', $sign_data) );

		$key = rawurlencode( $this->networks['twitter']['data']['secret'] ).'&';
		if( !empty( $authentication['oauth_token_secret'] ) ){
			$key .= rawurlencode( $authentication['oauth_token_secret'] );
		}

		$auth_data[] = 'oauth_signature="'.rawurlencode( adifier_b64_encode( hash_hmac('sha1', $base_signature, $key, true) ) ).'"';

	    $response = wp_remote_request( $url, array(
	        'method' 		=> $method,
	        'timeout' 		=> 45,
	        'redirection' 	=> 5,
	        'httpversion' 	=> '1.0',
	        'blocking' 		=> true,
	        'headers' 		=> array(
	        	'Accept'		=> '*/*',
	            'Authorization' => 'OAuth '.implode(', ', $auth_data),
	        ),
	        'body'			=> $data
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
			if( $method == 'POST' ){
				parse_str( $response['body'], $res );
			}
			else{
				$res = json_decode( $response['body'] );
			}
			return $res;
		}
	}
	/***--------- END TWITTER CONNECT----------***/

	/***---------GOOGLE CONNECT----------***/
	public function google_connect(){
		$data = array(
			'client_id'			=> $this->networks['google']['data']['id'],
			'redirect_uri'		=> site_url('index.php?adifier-callback=google'),
			'scope'				=> 'openid%20email%20profile',
			'access_type'		=> 'offline',
			'response_type'		=> 'code'
		);

		wp_redirect( 'https://accounts.google.com/o/oauth2/v2/auth?'.$this->_google_prepare_data( $data ) );
		die();
	}

	public function google_callback(){
		if( isset( $_GET['code'] ) ){
			$response = $this->_http_google( 'oauth2/v4/token', array(
				'code'			=> $_GET['code'],
				'client_id'		=> $this->networks['google']['data']['id'],
				'client_secret'	=> $this->networks['google']['data']['secret'],
				'redirect_uri'	=> site_url('index.php?adifier-callback=google'),
				'grant_type'	=> 'authorization_code'
			));

			if( !empty( $response->access_token ) ){
				$user = $this->_http_google( 'oauth2/v2/userinfo', array(), 'GET',array(
					'Authorization'	=> 'Bearer '.$response->access_token
				));

				if( !empty( $user->id ) ){
					$this->_login_user(array(
						'af_user_id'	=> $user->id,
						'user_email' 	=> $user->email,
						'first_name' 	=> $user->given_name,
						'last_name' 	=> $user->family_name,
						'user_login'	=> !empty( $user->given_name.$user->family_name ) ? sanitize_title( $user->given_name.$user->family_name ) : sanitize_title( $user->email ),
						'user_avatar'	=> isset( $user->picture ) ? $user->picture : ''
					));					
				}
				else{
					esc_html_e( 'Could not retrieve user data', 'adifier' );	
				}
			}
			else{
				esc_html_e( 'Could not retrieve access_token', 'adifier' );	
			}
		} 
		else {
			esc_html_e( 'Login error', 'adifier' );
		}
		die();
	}

	private function _google_prepare_data( $data ){
		$data_list = array();
		foreach( $data as $key => $value ){
			$data_list[] = $key.'='.$value;
		}

		return implode( '&', $data_list );
	}

	private function _http_google( $checkpoint, $data, $method = 'POST', $headers = array() ){

		$headers = array_merge(array(
			'Content-Type'	=> 'application/x-www-form-urlencoded'
		), $headers);

	    $response = wp_remote_request( 'https://www.googleapis.com/'.$checkpoint, array(
	        'method' 		=> $method,
	        'timeout' 		=> 45,
	        'redirection' 	=> 5,
	        'httpversion' 	=> '1.0',
	        'blocking' 		=> true,
	        'headers' 		=> $headers,
	        'body' 			=> $this->_google_prepare_data( $data ),
	    ));

		if ( is_wp_error( $response ) ) {
		} 
		else{
		   	return json_decode( $response['body']);		   	
		}
	}
	/***--------- END GOOGLE CONNECT----------***/
}
}

if( !is_user_logged_in() ){
$adifier_login = new Adifier_Social_Login();
}

?>