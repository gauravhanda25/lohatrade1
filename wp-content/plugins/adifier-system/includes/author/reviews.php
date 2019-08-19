<?php
$filter = !empty( $_GET['filter'] ) ? $_GET['filter'] : '';
?>

<div class="author-panel">
	<div class="white-block white-block-extra-padding">
		<div class="white-block-title">
			<div class="flex-wrap flex-center invoice-title">
				<h5><?php esc_html_e( 'Your Reviews', 'adifier' ) ?></h5>
				<div class="adverts-filter flex-wrap">
					<ul class="list-unstyled list-inline">
						<li class="<?php echo empty( $filter ) ? esc_attr('active') : esc_attr( '' ) ?>">
							<a href="<?php echo esc_url( add_query_arg( 'screen', 'reviews', $author_url ) ) ?>">
								<?php esc_html_e( 'All', 'adifier' ) ?>
							</a>
						</li>
						<li class="<?php echo  $filter == 'seller' ? esc_attr('active') : esc_attr( '' ) ?>">
							<a href="<?php echo esc_url( add_query_arg( array( 'screen' => 'reviews', 'filter' => 'seller' ), $author_url ) ) ?>">
								<?php esc_html_e( 'As Seller', 'adifier' ) ?>
							</a>
						</li>
						<li class="<?php echo  $filter == 'buyer' ? esc_attr('active') : esc_attr( '' ) ?>">
							<a href="<?php echo esc_url( add_query_arg( array( 'screen' => 'reviews', 'filter' => 'buyer' ), $author_url ) ) ?>">
								<?php esc_html_e( 'As Buyer', 'adifier' ) ?>
							</a>
						</li>
					</ul>
				</div>						
			</div>
		</div>
		<div class="white-block-content">
			<div class="row">
				<?php
				$counter = 0;
				$reviews = new Adifier_Reviews_Frontend(array(
					'author_id' => get_current_user_id(),
					'filter'	=> $filter,
					'paged'		=> !empty( $_GET['cpage'] ) ? $_GET['cpage'] : 1
				));

				if( !empty( $reviews->reviews ) ){
					foreach( $reviews->reviews as $review ){
						if( $counter == 2 ){
							echo '</div><div class="row">';
						}
						?>
						<div class="col-sm-6">
							<div class="user-review">

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
									<?php elseif( (int)$review->reviewer_id !== get_current_user_id() ): ?>
										<div class="send-response-wrap">
											<a href="javascript:void(0);" class="open-reponse-form" data-target=".form-<?php echo esc_attr( $review->review_id ) ?>">
												<i class="aficon-reply"></i>
											</a>
											<form class="form-group hidden form-<?php echo esc_attr( $review->review_id ) ?>" data-review_id="<?php echo esc_attr( $review->review_id ) ?>">
												<label for="review_response"><?php esc_html_e( 'Response *', 'adifier' ) ?></label>
												<textarea name="review_response" id="review_response"></textarea>
												<p class="description"><?php esc_html_e( 'Respond to this review (This can not be changed)', 'adifier' ) ?></p>
												<div class="response-result"></div>
												<a href="javascript:void(0);" class="send-response af-button"><?php esc_html_e( 'Send', 'adifier' ) ?></a>
												<input type="hidden" name="action" value="review_response">
												<input type="hidden" name="review_id" value="<?php echo esc_attr( $review->review_id ) ?>">
											</form>
										</div>
									<?php endif; ?>									
								</div>
							</div>
						</div>
						<?php
					}
				}
				else{
					?>
					<div class="text-center author-no-listing">
						<i class="aficon-info-circle"></i>
						<h5><?php esc_html_e( 'No reviews so far', 'adifier' ) ?></h5>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<?php if( !empty( $reviews->pagination ) ): ?>
		<div class="pagination">
			<?php echo implode( '', $reviews->pagination ); ?>
		</div>
	<?php endif; ?>
</div>