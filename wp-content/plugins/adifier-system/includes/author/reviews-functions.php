<?php
if( !class_exists('Adifier_Reviews_Frontend') ){
class Adifier_Reviews_Frontend{

	public $reviews;
	public $pagination;
	public $found_results;
	public $args;

	public function __construct( $args ){
		$this->args = array_merge( array(
			'per_page'	=> 10,
			'paged'		=> 1,
			'filter'	=> '',
			'author_id'	=> ''
		), $args);

		$this->_fetch_reviews();
		$this->_pagination();
	}

	private function _fetch_reviews(){
		global $wpdb;

		$offset = ($this->args['paged'] - 1) * $this->args['per_page'];

		$reviews_sql = $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS ar.*, ar2.review AS response FROM {$wpdb->prefix}adifier_reviews AS ar LEFT JOIN {$wpdb->prefix}adifier_reviews AS ar2 ON ar.review_id = ar2.parent WHERE ar.reviewed_id = %d", $this->args['author_id'] );

		if( !empty( $this->args['filter'] ) ){
			if( $this->args['filter'] == 'seller' ){
				$reviews_sql .= " AND ar.is_seller = 1";
			}
			else{
				$reviews_sql .= " AND ar.is_seller = 0";	
			}
		}

		$reviews_sql .= $wpdb->prepare( " ORDER BY ar.created DESC LIMIT %d OFFSET %d", $this->args['per_page'], $offset );
		$this->reviews = $wpdb->get_results( $reviews_sql );
		$this->found_results = $wpdb->get_var( "SELECT FOUND_ROWS()" );
	}	

	private function _pagination(){
		$this->pagination = paginate_links( 
			array(
				'base' 		=> '%_%',
				'format' 	=> '?cpage=%#%',
				'end_size' 	=> 2,
				'mid_size' 	=> 2,
				'type'		=> 'array',
				'total' 	=> ceil( $this->found_results / $this->args['per_page'] ),
				'current' 	=> $this->args['paged'],	
				'prev_next' => false,
			)
		);
	}

	private function _ajax_pagination(){
		if( !empty( $this->pagination ) ){
			foreach( $this->pagination as &$page_item ){
				preg_match( '/>(.*?)<\/a>/', $page_item, $match );
				if( !empty( $match[1] ) ){
					$page_item = '<a href="javascript:void(0);" data-page="'.esc_attr( str_replace( array( ',', '.' ), '', $match[1] ) ).'">'.$match[1].'</a>';
				} 
			}
		}
	}

	public function display_frontend_reviews(){
		$this->_ajax_pagination();
		if( !empty( $this->reviews ) ){
			?>
			<div class="owl-carousel">
				<?php
				foreach( $this->reviews as $review ){
					?>
					<div class="user-review hover-shadow">

						<div class="flex-wrap flex-center">
							<div class="user-rating">
								<?php adifier_rating_display( $review->rating ) ?>
							</div>
							<div>									
								<a href="<?php echo esc_url( get_author_posts_url( $review->reviewer_id ) ) ?>" target="_blank" class="profile-small-title">
									<?php echo esc_html__( 'By ', 'adifier' ).get_the_author_meta( 'display_name', $review->reviewer_id ) ?>
								</a>
								<a href="javascript:void(0);" class="toggle-review-details">
									<i class="aficon-caret-down"></i>
								</a>
							</div>
						</div>

						<div class="review-details">
							<div class="flex-wrap flex-center">
								<p class="profile-small-title"><?php echo date_i18n( get_option('date_format'), $review->created ); ?></p>
								<p class="profile-small-title"><?php echo  esc_html( $review->advert_title ); ?></p>
							</div>
						</div>					
						
						<div class="review-text">
							<p class="no-margin"><?php echo nl2br(stripslashes( $review->review )); ?></p>
							<?php if( !empty( $review->response ) ): ?>
								<div class="review-response"><p class="profile-small-title"><?php esc_html_e( 'Author response:', 'adifier' ) ?></p><br><?php echo nl2br(stripslashes( $review->response )) ?></div>
							<?php endif; ?>
						</div>
					</div>					
					<?php
				}
				?>
			</div>
			<?php
			if( !empty( $this->pagination ) ){
				echo '<div class="pagination">'.implode( '', $this->pagination ).'</div>';
			}
		}
	}
}
}

/*
*  Ajax fetch reviews
*/
if( !function_exists('adifier_fetch_ajax_reviews') ){
function adifier_fetch_ajax_reviews(){
		$reviews = new Adifier_Reviews_Frontend(array(
			'filter'	=> !empty( $_POST['filter'] ) 		? $_POST['filter'] 		: '',
			'paged'		=> !empty( $_POST['page'] ) 		? $_POST['page'] 		: '',
			'author_id'	=> !empty( $_POST['author_id'] ) 	? $_POST['author_id'] 	: ''
		));

		$reviews->display_frontend_reviews();

		die();
}
}
add_action( 'wp_ajax_adifier_fetch_ajax_reviews', 'adifier_fetch_ajax_reviews' );
add_action( 'wp_ajax_nopriv_adifier_fetch_ajax_reviews', 'adifier_fetch_ajax_reviews' );

/*
* Get review status
*/
if( !function_exists('adifier_advert_review_action') ){
function adifier_advert_review_action( $conversation ){
	if( $conversation->recipient_id == get_current_user_id() ){
		$status = $conversation->recipient_review;
	}
	else{
		$status = $conversation->sender_review;
	}


	if( $status == 1 ){
		?>
		<a href="javascript:void(0);" class="launch-review" data-con_id="<?php echo esc_attr( $conversation->con_id ) ?>">
			<i class="aficon-star-o"></i>
			<span><?php esc_html_e( 'Leave A Review', 'adifier' ); ?></span>
		</a>
		<?php
	}
}
}

/*
* Check if user can leave review
*/
if( !function_exists('adifier_can_review') ){
function adifier_can_review( $conversation ){
	if( ( $conversation->recipient_id == get_current_user_id() && $conversation->recipient_review == '1' ) || ( $conversation->sender_id == get_current_user_id() && $conversation->sender_review == '1' ) ){
		return true;
	}
	return false;
}
}

/*
* Write review
*/
if( !function_exists('adifier_write_review') ){
function adifier_write_review(){
	global $wpdb;
	if( !is_user_logged_in() ){
		return false;
	}
	$con_id = $_POST['con_id'];
	$rating = $_POST['rating'];
	$review = $_POST['review'];
	if( !empty( $con_id ) && !empty( $rating ) && !empty( $review ) ){
		$conversation = Adifier_Conversations::get_conversation_by_id( $con_id );
		if( !empty( $conversation ) && adifier_can_review( $conversation ) ){
			$reviewed_id = $conversation->recipient_id == get_current_user_id() ? $conversation->sender_id : $conversation->recipient_id;
			$is_seller = $conversation->recipient_id == get_current_user_id() ? 0 : 1;
			if( $conversation->invert_review == 1 ){
				$is_seller = $is_seller == 0 ? 1 : 0;
			}
			$review_id = $wpdb->insert(
				"{$wpdb->prefix}adifier_reviews",
				array(
					'reviewer_id'	=> get_current_user_id(),
					'reviewed_id'	=> $reviewed_id,
					'con_id'		=> $con_id,
					'review'		=> $review,
					'rating'		=> $rating,
					'advert_title'	=> $conversation->advert_title,
					'is_seller'		=> $is_seller,
					'created'		=> current_time( 'timestamp' ),
					'parent'		=> '0'
				),
				array(
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s',
					'%d',
					'%d',
					'%d'
				)
			);

			if( $review_id ){
				if( $conversation->recipient_id == get_current_user_id() ){
					$data = array(
						'recipient_review' => '2'
					);
				}
				else{
					$data = array(
						'sender_review' => '2'
					);
				}
				Adifier_Conversations::update_conversation( $data, $con_id );
				adifier_calculate_user_review( $reviewed_id );

				$reviewed_user = get_userdata( $reviewed_id );
				ob_start();
				include( get_theme_file_path( 'includes/emails/review.php' ) );
				$message = ob_get_contents();
				ob_end_clean();
				adifier_send_mail( $reviewed_user->user_email, esc_html__( 'New Review', 'adifier' ), $message );				

				$response['message'] = '<div class="alert-success">'.esc_html__( 'Review saved.', 'adifier' ).'</div>';
			}
			else{
				$response['message'] = '<div class="alert-error">'.esc_html__( 'Could not write to database.', 'adifier' ).'</div>';
			}
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Invalid conversation.', 'adifier' ).'</div>';
		}
	}
	else{
		$response['message'] = '<div class="alert-error">'.esc_html__( 'All fields are required.', 'adifier' ).'</div>';
	}

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_write_review', 'adifier_write_review' );
}

/*
* Write response to review
*/
if( !function_exists('adifier_review_response') ){
function adifier_review_response(){
	global $wpdb;
	$review_id = $_POST['review_id'];
	$review_response = $_POST['review_response'];
	if( !empty( $review_response ) && !empty( $review_id ) ){
		$review = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}adifier_reviews WHERE review_id = %d", $review_id ));
		if( $review && $review >= 1 ){
			$result = $wpdb->insert(
				"{$wpdb->prefix}adifier_reviews",
				array(
					'review' 	=> $review_response,
					'parent'	=> $review_id
				),
				array(
					'%s',
					'%d'
				)
			);

			if( !empty( $result ) ){
				$response['success'] = '<div class="review-response"><p class="profile-small-title">'.esc_html__( 'Author response:', 'adifier' ).'</p><br/>'.nl2br(stripslashes( $review_response )).'</div>';
			}
			else{
				$response['error'] = '<div class="alert-error">'.esc_html__( 'Could not write to database', 'adifier' ).'</div>';
			}
		}
		else{
			$response['error'] = '<div class="alert-error">'.esc_html__( 'Invalid review ID', 'adifier' ).'</div>';
		}
	}
	else{
		$response['error'] = '<div class="alert-error">'.esc_html__( 'All fields are required', 'adifier' ).'</div>';
	}

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_review_response', 'adifier_review_response' );
}

/*
* Update reviews for the user
*/
if( !function_exists('adifier_calculate_user_review') ){
function adifier_calculate_user_review( $user_id ){
	global $wpdb;
	$results = $wpdb->get_results($wpdb->prepare("SELECT SUM(rating) AS total_sum, COUNT(rating) AS total_count FROM {$wpdb->prefix}adifier_reviews WHERE parent = 0 AND reviewed_id = %d", $user_id));
	if( !empty( $results[0]->total_sum ) ){
		$average = $results[0]->total_sum / $results[0]->total_count;
		update_user_meta( $user_id, 'af_rating_average', $average );
		update_user_meta( $user_id, 'af_rating_count', $results[0]->total_count );
	}
	else{
		delete_user_meta( $user_id, 'af_rating_average' );
		delete_user_meta( $user_id, 'af_rating_count' );
	}
}
}

/*
* Display stars of the review
*/
if( !function_exists('adifier_rating_display') ){
function adifier_rating_display( $average ){
	if( empty( $average ) ){
		$average = 0;
	}
	$stars = array();
	if( $average < 0.5 ){
		for( $i=0; $i<5; $i++ ){
			$stars[] = '<span class="aficon-star-o"></span>';
		}
	}
	else if( $average < 1 ){
		$stars[] = '<span class="aficon-star"></span>';
		for( $i=0; $i<5; $i++ ){
			$stars[] = '<span class="aficon-star-o"></span>';
		}		
	}
	else{
		$flag = false;
		for( $i=1; $i<=5; $i+=0.5 ){
			if( $i <= $average ){
				if( floor( $i ) == $i ){
					$stars[] = '<span class="aficon-star"></span>';
				}
			}
			else{
				if( !$flag ){
					if( floor( $i ) == $i ){
						$stars[] = '<span class="aficon-star-half"></span>';
					}
					$flag = true;
				}
				else{
					if( floor( $i ) == $i ){
						$stars[] = '<span class="aficon-star-o"></span>';
					}
				}
			}
		}
	}

	echo join( "", $stars);
}
}

/*
*Display user rating
*/
if( !function_exists('adifier_user_rating') ){
function adifier_user_rating( $user_id, $show_count = false ){
	$average = get_user_meta( $user_id, 'af_rating_average', true );
	?>
	<div class="user-rating">
		<?php adifier_rating_display( $average ); ?>
		<?php
		if( $show_count ){
			$af_rating_count = get_user_meta( $user_id, 'af_rating_count', true );
			$af_rating_count = !empty( $af_rating_count ) ?  $af_rating_count : 0;

			echo '<div class="text-center text-reviews">'.sprintf( _n( '%s review', '%s reviews', $af_rating_count, 'adifier' ), $af_rating_count ).'</div>';
		}
		?>
	</div>
	<?php
}
}
?>