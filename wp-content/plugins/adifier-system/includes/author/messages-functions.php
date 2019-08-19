<?php

if( !class_exists('Adifier_Conversation_Query') ){
class Adifier_Conversation_Query{

	public $conversations;
	public $pagination;
	public $found_results;
	public $args;

	public function __construct(){

		$this->args = array(
			'per_page'		=> 50,
			'paged'			=> !empty( $_GET['page'] ) ? $_GET['page'] : 1,
			'user_id'		=> get_current_user_id(),
			'keyword'		=> !empty( $_GET['keyword'] ) ? $_GET['keyword'] : ''
		);

		$this->_fetch_conversations();
		$this->_pagination();
	}

	private function _fetch_conversations(){
		global $wpdb;

		$query = $wpdb->prepare("
			SELECT SQL_CALC_FOUND_ROWS ac.*, message, is_read, source_id, created, display_name, user_login, CASE WHEN sender_id = %d THEN recipient_id ELSE sender_id END AS other_id 
			FROM {$wpdb->prefix}adifier_conversations AS ac 
				LEFT JOIN {$wpdb->prefix}adifier_conversation_messages ON message_id = last_message_id 
				LEFT JOIN {$wpdb->prefix}users AS users ON users.ID = ( CASE WHEN sender_id = %d THEN recipient_id ELSE sender_id END ) 
			WHERE (( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 )) ", $this->args['user_id'], $this->args['user_id'], $this->args['user_id'], $this->args['user_id'] );

		if( !empty( $this->args['keyword'] ) ){
			$query .= "AND ( advert_title LIKE '%".esc_sql( $this->args['keyword'] )."%' OR display_name LIKE '%".esc_sql( $this->args['keyword'] )."%' ) ";
		}

		$offset = $this->args['per_page'] * ( $this->args['paged'] - 1 );
		$query .= $wpdb->prepare("ORDER BY created DESC LIMIT %d OFFSET %d", $this->args['per_page'], $offset );

		$this->conversations = $wpdb->get_results( $query );

		$this->found_results = $wpdb->get_var( "SELECT FOUND_ROWS()" );
		$this->total_pages = ceil( $this->found_results / $this->args['per_page'] );
	}

	private function _pagination(){
		$pagination = paginate_links( 
			array(
				'end_size' 	=> 2,
				'mid_size' 	=> 2,
				'type'		=> 'array',
				'total' 	=> ceil( $this->found_results / $this->args['per_page'] ),
				'current' 	=> $this->args['paged'],	
				'prev_next' => false,
			)
		);

		if( !empty( $pagination ) ){
			foreach( $pagination as &$page_item ){
				preg_match( '/>(.*?)<\/a>/', $page_item, $match );
				if( empty( $match[1] ) ){
					preg_match( '/>(.*?)<\/span>/', $page_item, $match );
				} 
				
				if( !empty( $match[1] ) ){
					$page_item = '<a href="javascript:void(0);" class="'.( $match[1] == $this->args['paged'] ? esc_attr( 'current' ) : ''  ).'" data-page="'.esc_attr( str_replace( array( ',', '.' ), '', $match[1] ) ).'">'.$match[1].'</a>';
				} 
			}
		}

		$this->pagination = $pagination;		
	}

	public function display_pagination(){
		if( !empty( $this->pagination ) ){
			echo '<div class="pagination">'.implode( '', $this->pagination ).'</div>';
		}
	}

	public function display_frontend_conversations(){
		if( !empty( $this->conversations ) ){
			?>
			<div class="conversations-list">
				<?php
				foreach( $this->conversations as $conversation ){
					if( !empty( $_GET['con_id'] ) && $conversation->con_id == $_GET['con_id'] ){
						$conversation->is_read = 1;
					}

					$interlocutor_url = get_author_posts_url( $conversation->other_id );
					?>
					<div class="conversation-wrap animation <?php echo ( !empty( $_REQUEST['con_id'] ) && $_REQUEST['con_id'] == $conversation->con_id ) ? esc_attr( 'current' ) : '' ?> <?php echo  $conversation->is_read == 0 && $conversation->source_id != $this->args['user_id'] ? esc_attr( 'unread' ) : '' ?>">
						<a href="javascript:void(0);" data-con_id="<?php echo esc_attr( $conversation->con_id ) ?>" class="start-messages">
							<div class="flex-wrap flex-start">
								<div class="flex-left">	
									<?php echo get_avatar( $conversation->other_id ); ?>
									<i class="aficon-spin aficon-circle-notch con-loading con-loading-<?php echo esc_attr( $conversation->con_id ) ?> hidden"></i>
								</div>
								<div class="flex-right">
									<h6><?php echo adifier_author_name( $conversation ); ?></h6>
									<div class="profile-small-title conversation-time">
										<?php echo date_i18n( get_option( 'date_format' ), $conversation->created ); ?>
									</div>
									<div class="conversation-last-message">
										<?php echo adifier_limit_string( stripslashes( $conversation->message ), 100); ?>
									</div>
								</div>
							</div>
						</a>
						<div class="styled-checkbox">
							<input type="checkbox" id="<?php echo esc_attr( $conversation->con_id ) ?>" name="con_ids[]" value="<?php echo esc_attr( $conversation->con_id ) ?>"/>
							<label for="<?php echo esc_attr( $conversation->con_id ) ?>"></label>
						</div>
					</div>
					<?php
				}
			?>
			</div>
			<?php
		}
		else{
			?>
			<h6 class="no-conversations text-center"><?php esc_html_e( 'No conversations', 'adifier' ) ?></h6>
			<?php
		}
	}
}
}

/*
* Fetch ajax conversations
*/
if( !function_exists('adifier_ajax_conversations') ){
function adifier_ajax_conversations(){
	$response = array();
	$cons = new Adifier_Conversation_Query();
	
	ob_start();
	$cons->display_frontend_conversations();
	$response['conversations'] = ob_get_contents();
	ob_end_clean();

	ob_start();
	$cons->display_pagination();
	$response['pagination'] = ob_get_contents();
	ob_end_clean();

	$response['unread'] = Adifier_Messages::has_unread_messages();

	echo json_encode( $response );

	die();
}
add_action( 'wp_ajax_adifier_ajax_conversations', 'adifier_ajax_conversations' );
add_action( 'wp_ajax_nopriv_adifier_ajax_conversations', 'adifier_ajax_conversations' );
}

if( !class_exists( 'Adifier_Conversations' ) ){
class Adifier_Conversations{

	static public function launch(){
		add_action( 'wp_ajax_adifier_delete_conversations', 'Adifier_Conversations::hide_conversations' );
		add_action( 'wp_ajax_nopriv_adifier_delete_conversations', 'Adifier_Conversations::hide_conversations' );
		add_action( 'wp_ajax_adifier_initiate_conversation', 'Adifier_Conversations::initiate_conversation' );
	}

	/*
	* Delete conversation from frontend but leave it for administrator
	*/
	static public function hide_conversations(){
		if( !is_user_logged_in() ){
			return;
		}
		if( !empty( $_GET['con_ids'] ) ){
			global $wpdb;
			$user_id = get_current_user_id();
			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}adifier_conversations SET sender_delete = ( CASE WHEN sender_id = %d THEN 1 ELSE sender_delete END ), recipient_delete = ( CASE WHEN recipient_id = %d THEN 1 ELSE recipient_delete END ) WHERE con_id IN ( ".esc_sql( $_GET['con_ids'] )." )", $user_id, $user_id));
		}

		adifier_ajax_conversations();
	}

	static private function _initiate_sent_message( $con_id, $message ){
		$message_id = Adifier_Messages::save_message( $con_id, $message );
		if( $message_id ){
			$data['last_message_id'] = $message_id;
			self::update_conversation( $data, $con_id );
			$response['message'] = '<div class="alert-success">'.esc_html__( 'Message sent', 'adifier' ).'</div>';
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Message sending failed', 'adifier' ).'</div>';	
		}

		return $response;
	}

	static public function initiate_conversation_write_message( $args, $message ){
		global $wpdb;
		$type = adifier_get_advert_meta( $args['advert_id'], 'type', true );
		$result = $wpdb->insert(
			$wpdb->prefix.'adifier_conversations',
			array(
				'advert_title'		=> get_the_title( $args['advert_id'] ),
				'post_id'			=> $args['advert_id'],
				'sender_id'			=> $args['sender_id'],
				'recipient_id'		=> $args['recipient_id'],
				'sender_review'		=> 0,
				'recipient_review'	=> 0,
				'invert_review'		=> ( $type == 3 || !empty( $_POST['auction_contact'] ) ) ? 1 : 0,
				'sender_delete'		=> 0,
				'recipient_delete'	=> 0
			),
			array(
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d'
			)
		);
		if( $result ){
			$con_id = $wpdb->insert_id;
			$response = self::_initiate_sent_message( $con_id, $message );
			Adifier_Messages::send_unread_mail( array(
				'sender_id'			=> $args['sender_id'],
				'recipient_id'		=> $args['recipient_id'],
				'post_id'			=> $args['advert_id'],
				'con_id'			=> $con_id
			), $message);
			$response['con_id'] = $con_id;
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Message sending failed', 'adifier' ).'</div>';
		}

		return $response;
	}

	/*
	* Initiate conversation from frontend modal ( on seller page or from auction )
	*/
	static public function initiate_conversation(){
		if( !is_user_logged_in() ){
			return false;
		}
		$advert_id = !empty( $_POST['advert_id'] ) ? $_POST['advert_id'] : '';
		$message = !empty( $_POST['message'] ) ? $_POST['message'] : '';
		$con_id = !empty( $_POST['con_id'] ) ? $_POST['con_id'] : ''; /* This is used if contact modal is opened and users sends multiple messages */
		if( !empty( $advert_id ) && !empty( $message ) ){
			if( !empty( $con_id ) ){
				$response = self::_initiate_sent_message( $con_id, $message );
			}
			else{
				$response = self::initiate_conversation_write_message(array(
					'advert_id'		=> $advert_id,
					'sender_id' 	=> get_current_user_id(),
					'recipient_id'	=> !empty( $_POST['buyer_id'] ) ? $_POST['buyer_id'] : get_post_field( 'post_author', $advert_id )
				), $message);
			}
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Required fields are empty', 'adifier' ).'</div>';
		}

		echo json_encode( $response );

		die();
	}

	/*
	* Check if there is conversation already started and print URL to it
	*/
	static public function has_conversation_started( $visitor_id, $author_id, $post_id = 0 ){
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		global $wpdb;
		$con_id = $wpdb->get_var($wpdb->prepare("SELECT con_id FROM {$wpdb->prefix}adifier_conversations WHERE ( ( sender_id = %d AND recipient_id = %d ) OR ( sender_id = %d AND recipient_id = %d ) ) AND sender_delete = 0 AND recipient_delete = 0 AND post_id = %d", $visitor_id, $author_id, $author_id, $visitor_id, $post_id));
		if( !empty( $con_id ) ){
			return add_query_arg( array( 'screen' => 'messages', 'con_id' => $con_id ), get_author_posts_url( get_current_user_id() ) );
		}
		else{
			return 'javascript:void(0);';
		}
	}

	/*
	* Update conversation ( used by reviews and messages )
	*/
	static public function update_conversation( $data, $con_id ){
		global $wpdb;
		$wpdb->update(
			"{$wpdb->prefix}adifier_conversations",
			$data,
			array(
				'con_id' => $con_id
			)
		);
	}


	/*
	* Get conversation by its ID ( user by reviews and messages )
	*/
	static public function get_conversation_by_id( $con_id ){
		global $wpdb;
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}adifier_conversations WHERE con_id = %d", $con_id ));
		if( !empty( $results ) ){
			$results = array_shift( $results );
		}

		return $results;
	}

}
Adifier_Conversations::launch();
}



if( !class_exists('Adifier_Messages_Query') ){
class Adifier_Messages_Query{
	public $messages;
	public $args;

	public function __construct( $args ){
		$this->args = array_merge(array(
			'user_id' => get_current_user_id()
		), $args);

		$this->_fetch_messages();
	}

	/*
	* Get messages of conversation
	*/
	private function _fetch_messages(){
		global $wpdb;

		$messages = $wpdb->get_results( $wpdb->prepare( "
			SELECT * 
			FROM {$wpdb->prefix}adifier_conversations AS ac 
				LEFT JOIN {$wpdb->prefix}adifier_conversation_messages AS acm ON ac.con_id = acm.con_id 
				LEFT JOIN {$wpdb->prefix}users AS users ON users.ID = ( CASE WHEN sender_id = %d THEN recipient_id ELSE sender_id END ) 
			WHERE (( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 )) AND ac.con_id = %d ORDER BY created ASC", $this->args['user_id'], $this->args['user_id'], $this->args['user_id'], $this->args['con_id'] ) );

		$this->messages = $messages;
	}


	/*
	* Mark message as read
	*/
	private function _mark_message_as_read( $message_id ){
		global $wpdb;
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}adifier_conversation_messages SET is_read = 1 WHERE message_id = %d", $message_id));
	}

	public function linkify_helper( $matches ){
		$url = $matches[1];
		if( empty( $url ) ){
			return $matches[0];
		}
		else{
			$html = '<a href="'.esc_url( $url ).'" target="_blank">';
			$data = pathinfo( $url );
			if( !empty( $data['extension'] ) && in_array( strtolower($data['extension']), array( 'png', 'jpg', 'gif' ) ) ){
				$html .= '<img src="'.esc_url( $url ).'" />';
			}
			else{
				$html .= $url;
			}
			$html .= '</a>';

			return $html;
		}
	}

	private function _linkify( $text ){
		return preg_replace_callback("/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", array( $this, 'linkify_helper' ), $text);
	}

	/*
	* List messages
	*/
	public function display_messages(){
		if( !empty( $this->messages ) ){
			$sender = '';
			$message_from_id = '';
			$date = date_i18n( get_option( 'date_format' ) );
			$closed_message_wrap = false;

			foreach( $this->messages as $message ){
				/* mark as read */
				if( $message->is_read == 0 && $message->source_id != get_current_user_id() ){
					$this->_mark_message_as_read( $message->message_id );
				}
				/* populate sender and recipient */
				if( empty( $sender ) ){
					$sender = '<a href="'.esc_url( get_author_posts_url( $message->ID ) ).'" target="_blank" class="message-avatar">'.get_avatar( $message->ID, 50 ).'</a>';
				}								

				$message_date = date_i18n( get_option( 'date_format' ), $message->created );

				if( $message_date !== $date ){									
					$date = $message_date;
					if( !empty( $message_from_id )){
						echo '</div></div>';
						$closed_message_wrap = true;
					}
					echo '<div class="message-date-header-wrap"><div class="message-date-header">'.$date.'</div></div>';
				}

				/* if multipe message one  after another then only once display header of user */
				if( $message_from_id !== $message->source_id || $closed_message_wrap ){
					if( !empty( $message_from_id ) && !$closed_message_wrap ){
						echo '</div></div>';
					}
					$closed_message_wrap = false;
					$message_from_id = $message->source_id;
					echo '<div class="message-wrap flex-wrap '.( $message_from_id == get_current_user_id() ? esc_attr('this-user') : esc_attr('other-user') ).'">';
					echo  $message_from_id == get_current_user_id() ? '' : $sender;
					echo '<div class="flex-right">';
				}

				echo '<div class="flex-wrap"><div class="message">'.$this->_linkify(nl2br(stripslashes( $message->message ))).'</div><div class="message-time">'.date_i18n( get_option( 'time_format' ), $message->created ).'</div></div>';

			}
			/* close flex wrap and flex-right */
			echo '</div></div>';
		}
	}
}
}

if( !class_exists('Adifier_Messages') ){
class Adifier_Messages{
	static public function launch(){
		add_action( 'wp_ajax_adifier_send_message', 'Adifier_Messages::send_message' );
		add_action( 'wp_ajax_nopriv_adifier_send_message', 'Adifier_Messages::send_message' );

		add_action( 'wp_ajax_adifier_get_unread_message_num', 'Adifier_Messages::get_unread_message_num' );
		add_action( 'wp_ajax_nopriv_adifier_get_unread_message_num', 'Adifier_Messages::get_unread_message_num' );		
	}


	/*
	* Encho unread messages count
	*/
	static public function get_unread_message_num(){
		echo Adifier_Messages::has_unread_messages();

		die();
	}

	/*
	* Has unread messages?
	*/
	static public function has_unread_messages(){
		global $wpdb;
		if( is_user_logged_in() ){
			$user_id = get_current_user_id();
			$unread = $wpdb->get_col($wpdb->prepare("SELECT COUNT(message_id) FROM {$wpdb->prefix}adifier_conversations AS ac LEFT JOIN {$wpdb->prefix}adifier_conversation_messages AS acm ON ac.con_id = acm.con_id WHERE ( ( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ) ) AND is_read = 0 AND source_id != %d", $user_id, $user_id, $user_id));

			if( $unread[0] > 0 ){
				return '<span class="unread-badge">'.$unread[0].'</span>';
			}
		}
	}

	/*
	* Write message top database
	*/
	static public function save_message( $con_id, $message ){
		global $wpdb;
		$result = $wpdb->insert( 
			$wpdb->prefix.'adifier_conversation_messages', 
			array( 
				'con_id' 	=> $con_id, 
				'message' 	=> $message,
				'source_id'	=> get_current_user_id(),
				'is_read'	=> 0,
				'created'	=> current_time( 'timestamp' )
			), 
			array( 
				'%d', 
				'%s',
				'%d',
				'%d',
				'%f' 
			) 
		);

		if( $result ){
			return $wpdb->insert_id;
		}
		else{
			return false;
		}
	}


	/*
	* Send email if recipient is offline
	*/
	static public function send_unread_mail( $conversation, $message ){
		if( is_array( $conversation ) ){
			$conversation = (object)$conversation;
		}
		/*lets inform recepinet if he is offline*/
		$recipient_id = get_current_user_id() == $conversation->sender_id ? $conversation->recipient_id : $conversation->sender_id;
		if( !adifier_is_online( $recipient_id ) ){

			$sender_id = get_current_user_id() == $conversation->sender_id ? $conversation->sender_id : $conversation->recipient_id;
			$sender = get_user_by( 'ID', $sender_id );
			$sender_name = adifier_author_name( $sender );
			$recipient = get_user_by( 'ID', $recipient_id );
			$advert_url = get_the_permalink( $conversation->post_id );
			$advert_title = get_the_title( $conversation->post_id );
			$conversation_url = add_query_arg( array( 'screen' => 'messages', 'con_id' => $conversation->con_id ), get_author_posts_url( $recipient_id ) );

			ob_start();
			include( get_theme_file_path( 'includes/emails/unread-messages.php' ) );
			$message_email = ob_get_contents();
			ob_end_clean();
			adifier_send_mail( $recipient->user_email, esc_html__( 'New Messages Waiting', 'adifier' ), $message_email );
		}
	}


	/*
	* Send message
	*/
	static public function send_message(){
		if( !is_user_logged_in() ){
			return;
		}
		
		$conversation = Adifier_Conversations::get_conversation_by_id( $_POST['con_id'] );
		if( !empty( $conversation ) && in_array( get_current_user_id(), array( $conversation->sender_id, $conversation->recipient_id ) ) ){
			$message_id = self::save_message( $_POST['con_id'], $_POST['message'] );

			if( ( $message_id ) ){
				$data = array(
					'last_message_id' 	=> $message_id,
					'sender_delete'		=> 0,
					'recipient_delete'	=> 0
				);
				if( $conversation->recipient_id == get_current_user_id() && $conversation->recipient_review == '0' ){
					$data['sender_review'] = '1';
					$data['recipient_review'] = '1';
				}
				Adifier_Conversations::update_conversation( $data, $_POST['con_id'] );

				self::send_unread_mail( $conversation, $_POST['message'] );

				adifier_ajax_messages();
			}
		}

		die();
	}
}
Adifier_Messages::launch();
}

/*
* Fetch ajax messages
*/
if( !function_exists('adifier_ajax_messages') ){
function adifier_ajax_messages(){
	$response = array();

	if( !is_user_logged_in() ){
		return;
	}

	$messages = new Adifier_Messages_Query(array(
		'con_id' => $_POST['con_id']
	));

	ob_start();
	$messages->display_messages();
	$response['messages'] = ob_get_contents();
	ob_clean();
	$conversation = Adifier_Conversations::get_conversation_by_id( $_POST['con_id'] );
	adifier_advert_review_action( $conversation );
	$response['review'] = ob_get_contents();
	$response['title'] = $conversation->advert_title;
	ob_end_clean();

	$response['unread'] = Adifier_Messages::has_unread_messages();

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_adifier_ajax_messages', 'adifier_ajax_messages' );
add_action( 'wp_ajax_nopriv_adifier_ajax_messages', 'adifier_ajax_messages' );
}
?>