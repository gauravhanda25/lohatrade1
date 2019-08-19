<?php if( !is_author() || ( is_author() && !adifier_is_own_account() ) ): ?>
<footer>
	<?php
	$show_subscription_form = adifier_get_option( 'show_subscription_form' );
	if( $show_subscription_form == 'yes' ){
		?>
		<div class="subscription-footer">
			<div class="container">
				<div class="flex-wrap">
					<div class="flex-left flex-wrap">
						<i class="aficon-paper-plane"></i>
						<div class="subscribe-title">
							<h4><?php esc_html_e( 'Subscribe To Newsletter', 'adifier' ) ?></h4>
							<p><?php esc_html_e( 'and receive new ads in inbox', 'adifier' ) ?></p>
						</div>
					</div>
					<div class="flex-right">
						<form class="ajax-form" autocomplete="off">
							<div class="adifier-form">
								<input type="text" name="email" placeholder="<?php esc_attr_e( 'Input your email address', 'adifier' ); ?>">
								<a href="javascript:void(0)" class="submit-ajax-form"><?php esc_html_e( 'Subscribe', 'adifier' ) ?></a>						
							</div>
							<input type="hidden" name="action" value="subscribe">
							<?php adifier_gdpr_checkbox(); ?>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>

	<?php get_sidebar( 'footer' ) ?>

	<?php if( adifier_get_option( 'show_footer' ) == 'yes' ): ?>
		<div class="copyrights">
			<div class="container">
				<div class="flex-wrap">
					<?php
					$copyrights = adifier_get_option( 'copyrights' );
					if( !empty( $copyrights ) ){
						?>
						<div class="flex-left">
							<?php echo $copyrights ?>
						</div>
						<?php
					}
					?>
					<div class="flex-center">
						<ul class="list-unstyled list-inline social-links">		
							<?php
							$tb_facebook_link = adifier_get_option( 'tb_facebook_link' );
							if( !empty( $tb_facebook_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_facebook_link ) ?>" target="_blank">
									<i class="aficon-facebook"></i>
								</a>
							</li>
							<?php endif; ?>

							<?php
							$tb_twitter_link = adifier_get_option( 'tb_twitter_link' );
							if( !empty( $tb_twitter_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_twitter_link ) ?>" target="_blank">
									<i class="aficon-twitter"></i>
								</a>
							</li>
							<?php endif; ?>
				

							<?php
							$tb_instagram_link = adifier_get_option( 'tb_instagram_link' );
							if( !empty( $tb_instagram_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_instagram_link ) ?>" target="_blank">
									<i class="aficon-instagram"></i>
								</a>
							</li>
							<?php endif; ?>

							<?php
							$tb_youtube_link = adifier_get_option( 'tb_youtube_link' );
							if( !empty( $tb_youtube_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_youtube_link ) ?>" target="_blank">
									<i class="aficon-youtube"></i>
								</a>
							</li>
							<?php endif; ?>

							<?php
							$tb_pinterest_link = adifier_get_option( 'tb_pinterest_link' );
							if( !empty( $tb_pinterest_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_pinterest_link ) ?>" target="_blank">
									<i class="aficon-pinterest"></i>
								</a>
							</li>
							<?php endif; ?>

							<?php
							$tb_rss_link = adifier_get_option( 'tb_rss_link' );
							if( !empty( $tb_rss_link ) ):
							?> 
							<li>
								<a href="<?php echo esc_url( $tb_rss_link ) ?>" target="_blank">
									<i class="aficon-rss"></i>
								</a>
							</li>
							<?php endif; ?>
						</ul>
					</div>
					
					<?php if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'bottom-navigation' ] ) ): ?>
						<div class="flex-right">
							<ul class="list-inline list-unstyled bottom-menu-wrap">
								<?php
									wp_nav_menu( array(
										'theme_location'  	=> 'bottom-navigation',
										'container'			=> false,
										'echo'          	=> true,
										'items_wrap'        => '%3$s',
										'walker' 			=> new adifier_walker
									) );
								?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</footer>
<?php endif; ?>


<?php if( is_singular('advert') && function_exists('adifier_share') ): ?>
<div class="modal in" id="share" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form"  autocomplete="off">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e( 'Share This', 'adifier' ) ?></h5>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>
				<div class="modal-body">
					<?php adifier_share(); ?>
				</div>
				<div class="modal-footer">
					<div class="flex-left">
					</div>
					<div class="flex-right">
						<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
					</div>
				</div>
			</form>
		</div>

	</div>
</div>
<?php endif; ?>

<?php if( is_singular( 'advert' ) ): ?>
	<div class="modal in" id="report-advert" tabindex="-1" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form method="post" class="ajax-form"  autocomplete="off">
					<div class="modal-header">
						<h5 class="modal-title"><?php esc_html_e( 'Report Ad', 'adifier' ) ?></h5>
						<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
					</div>
					<div class="modal-body">
						<div class="form-group has-feedback">
							<label for="reason" class="bold"><?php esc_html_e( 'Reason *', 'adifier' ) ?></label>
							<textarea class="form-control small-height" id="reason" name="reason" placeholder="<?php esc_attr_e( 'Write as much details as you can', 'adifier' ); ?>"></textarea>
						</div>
						<input type="hidden" value="adifier_report_advert" name="action" />
						<input type="hidden" value="<?php the_ID() ?>" name="advert_id" />
					</div>
					<div class="modal-footer">
						<div class="flex-left">
							<div class="ajax-form-result"></div>
						</div>
						<div class="flex-right">
							<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
							<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Report', 'adifier' ) ?> </a>
						</div>
					</div>
				</form>
			</div>

		</div>
	</div>
	<?php if( is_user_logged_in() ): ?>
		<div class="modal in" id="contact-seller" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<form method="post" class="ajax-form" autocomplete="off">
						<div class="modal-header">
							<h5 class="modal-title"><?php esc_html_e( 'Contact Seller', 'adifier' ) ?></h5>
							<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
						</div>
						<div class="modal-body">
							<div class="form-group has-feedback">
								<label for="message" class="bold"><?php esc_html_e( 'Message *', 'adifier' ) ?></label>
								<textarea class="form-control small-height" id="message" name="message" placeholder="<?php esc_attr_e( 'Write your message to the seller', 'adifier' ); ?>"></textarea>
							</div>
							<input type="hidden" value="adifier_initiate_conversation" name="action" />
							<input type="hidden" value="<?php the_ID() ?>" name="advert_id" />
							<input type="hidden" value="" name="con_id" />
						</div>
						<div class="modal-footer">
							<div class="flex-left">
								<div class="ajax-form-result"></div>
							</div>
							<div class="flex-right">
								<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
								<a href="javascript:;" class="submit-ajax-form af-button" data-callbacktrigger="adifier_contact_seller_modal"><?php esc_html_e( 'Send', 'adifier' ) ?> </a>
							</div>
						</div>
					</form>
				</div>

			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>
<?php if( is_author() && !empty( $_GET['screen'] ) && $_GET['screen'] == 'edit'): ?>
	<div class="modal in" id="contact-buyer" tabindex="-1" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form method="post" class="ajax-form"  autocomplete="off">
					<div class="modal-header">
						<h5 class="modal-title"><?php esc_html_e( 'Contact Buyer', 'adifier' ) ?></h5>
						<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
					</div>
					<div class="modal-body">
						<div class="form-group has-feedback">
							<label for="message" class="bold"><?php esc_html_e( 'Message *', 'adifier' ) ?></label>
							<textarea class="form-control small-height" id="message" name="message" placeholder="<?php esc_attr_e( 'Write your message to the buyer', 'adifier' ); ?>"></textarea>
						</div>
						<input type="hidden" value="adifier_initiate_conversation" name="action" />
						<input type="hidden" value="<?php echo esc_attr( $_GET['id'] ) ?>" name="advert_id" />
						<input type="hidden" value="1" name="auction_contact" />
						<input type="hidden" value="" name="buyer_id" />
					</div>
					<div class="modal-footer">
						<div class="flex-left">
							<div class="ajax-form-result"></div>
						</div>
						<div class="flex-right">
							<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
							<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Send', 'adifier' ) ?> </a>
						</div>
					</div>
				</form>
			</div>

		</div>
	</div>
<?php endif; ?>

<?php if( is_author() ):  ?>
	<?php if( !empty( $_GET['screen'] ) && $_GET['screen'] == 'messages' ): ?>
		<div class="modal in" id="write-review" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<form method="post" class="ajax-form"  autocomplete="off">
						<div class="modal-header">
							<h5 class="modal-title"><?php esc_html_e( 'Write Review', 'adifier' ) ?></h5>
							<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
						</div>
						<div class="modal-body">
							<div class="form-group has-feedback">
								<label class="bold"><?php esc_html_e( 'Review *', 'adifier' ) ?></label>
								<div class="rate-user">
									<span class="aficon-star-o" data-toggle="tooltip" data-placement="right" title="<?php esc_html_e( 'Very Bad', 'adifier' ); ?>"></span>
									<span class="aficon-star-o" data-toggle="tooltip" data-placement="right" title="<?php esc_html_e( 'Bad', 'adifier' ); ?>"></span>
									<span class="aficon-star-o" data-toggle="tooltip" data-placement="right" title="<?php esc_html_e( 'Good', 'adifier' ); ?>"></span>
									<span class="aficon-star-o" data-toggle="tooltip" data-placement="right" title="<?php esc_html_e( 'Very Good', 'adifier' ); ?>"></span>
									<span class="aficon-star-o" data-toggle="tooltip" data-placement="right" title="<?php esc_html_e( 'Excellent', 'adifier' ); ?>"></span>
									<div class="rate-user-textual"></div>
									<input type="hidden" name="rating" class="rating-value">
								</div>
							</div>
							<div class="form-group has-feedback">
								<label for="review" class="bold"><?php esc_html_e( 'Your Review *', 'adifier' ) ?></label>
								<textarea class="form-control small-height" id="review" name="review" placeholder="<?php esc_attr_e( 'Write about your experience with the seller and product/service (This can not be changed)', 'adifier' ); ?>"></textarea>
							</div>
							<input type="hidden" value="write_review" name="action" />
							<input type="hidden" value="" name="con_id" />
						</div>
						<div class="modal-footer">
							<div class="flex-left">
								<div class="ajax-form-result"></div>
							</div>
							<div class="flex-right">
								<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
								<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Send', 'adifier' ) ?> </a>
							</div>
						</div>
					</form>
				</div>

			</div>
		</div>
	<?php elseif( !empty( $_GET['screen'] ) && $_GET['screen'] == 'ads' && adifier_get_option( 'enable_promotions' ) == 'yes' ): ?>
		<div class="modal in" id="promote" tabindex="-1" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php esc_html_e( 'Purchase Promotions For Your Ad', 'adifier' ) ?></h5>
						<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
					</div>
					<div class="modal-body">
						<input type="hidden" value="" name="con_id" />
						<?php
						foreach( adifier_available_promotions() as $promotion ){
							adifier_display_promotion( $promotion['id'] );	
						}
						?>
					</div>
					<div class="modal-footer">
						<div class="flex-left">
							<div class="ajax-form-result"></div>
						</div>
						<div class="flex-right">
							<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
							<a href="javascript:;" class="purchase-promotion af-button" data-empty="<?php esc_attr_e( 'No promotions selected', 'adifier' ) ?>"><?php esc_html_e( 'Purchase', 'adifier' ) ?> </a>
						</div>
					</div>
				</div>

			</div>
		</div>
	<?php endif; ?>
	<div class="modal in" id="purchase" tabindex="-1" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e( 'Purchase', 'adifier' ) ?></h5>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>
				<div class="modal-body">
					<div class="purchase-response"></div>
					<textarea class="hidden"></textarea>
					<input type="hidden" class="order_user" value="<?php echo esc_attr( get_current_user_id() ) ?>">
					<ul class="list-unstyled list-inline payments-list">
						<?php do_action( 'adifier_payment_methods' ) ?>
					</ul>
					<div class="purchase-loader">
						<i class="aficon-spin aficon-circle-notch"></i>
					</div>
				</div>
				<div class="modal-footer">
					<div class="flex-right">
						<button type="button" class="af-button af-secondary" data-dismiss="modal"><?php esc_html_e( 'Close', 'adifier' ) ?></button>
					</div>
				</div>
			</div>

		</div>
	</div>
<?php endif; ?>

<?php if( !is_user_logged_in() ): ?>
<div class="modal in lrr" id="login" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form" autocomplete="off">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e( 'Login', 'adifier' ) ?></h5>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>
				<div class="modal-body">

					<div class="ajax-form-result"></div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group has-feedback">
								<label for="log_username" class="bold"><?php esc_html_e( 'Username / Email *', 'adifier' ) ?></label>
								<input type="text" class="form-control" id="log_username" name="log_username" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group has-feedback relative-wrap">
								<label for="log_password" class="bold"><?php esc_html_e( 'Password *', 'adifier' ) ?></label>
								<input type="password" class="form-control reveal-password" id="log_password" name="log_password" placeholder="<?php echo adifier_get_option( 'direction' ) == 'rtl' ? esc_attr( '&larr;' ) : esc_attr( '&rarr;' ) ?>" />
								<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
							</div>
						</div>
					</div>

					<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Login', 'adifier' ) ?> </a>

					<div class="text-center">
						<a href="#" class="forgot" data-toggle="modal" data-target="#recover" data-dismiss="modal"><?php esc_html_e( 'Forgotten your password?', 'adifier' ) ?></a>
					</div>

					<div class="or-divider"><h6><?php esc_html_e( 'OR', 'adifier' ) ?></h6></div>

					<ul class="list-unstyled list-inline social-login">
						<?php do_action( 'adifier_social_login_button' ) ?>
					</ul>

					<div class="text-center">
						<a href="#" class="register-acc" data-toggle="modal" data-target="#register" data-dismiss="modal"><?php esc_html_e( 'Don\'t have an account? Create one here.', 'adifier' ) ?></a>
					</div>

					<input type="hidden" value="adifier_login" name="action" />
				</div>
			</form>
		</div>

	</div>
</div>

<div class="modal in lrr" id="register" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form"  autocomplete="off">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e( 'Register', 'adifier' ) ?></h5>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>
				<div class="modal-body">
					<div class="ajax-form-result"></div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group has-feedback">
								<label for="reg_username" class="bold"><?php esc_html_e( 'Username *', 'adifier' ) ?></label>
								<input type="text" class="form-control" id="reg_username" name="reg_username" placeholder="<?php esc_attr_e( 'Your desired username', 'adifier' ) ?>" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group has-feedback">
								<label for="reg_email" class="bold"><?php esc_html_e( 'Email *', 'adifier' ) ?></label>
								<input type="text" class="form-control" id="reg_email" name="reg_email" placeholder="<?php esc_attr_e( 'It will be verified', 'adifier' ) ?>" />
							</div>							
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group has-feedback relative-wrap">
								<label for="reg_password" class="bold"><?php esc_html_e( 'Password *', 'adifier' ) ?></label>
								<input type="password" class="form-control reveal-password" id="reg_password" name="reg_password" placeholder="<?php esc_attr_e( 'Use a strong password', 'adifier' ) ?>" />
								<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
							</div>							
						</div>
						<div class="col-sm-6">
							<div class="form-group has-feedback relative-wrap">
								<label for="reg_r_password" class="bold"><?php esc_html_e( 'Repeat Password *', 'adifier' ) ?></label>
								<input type="password" class="form-control reveal-password" id="reg_r_password" name="reg_r_password" placeholder="<?php esc_attr_e( 'To make sure that it is correct', 'adifier' ) ?>" />
								<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
							</div>
						</div>
					</div>

					<?php adifier_gdpr_checkbox(); ?>
					<div class="form-group has-feedback">
						<?php adifier_terms_checkbox( 'register_terms' ); ?>
					</div>

					<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Register', 'adifier' ) ?> </a>

					<div class="or-divider"><h6><?php esc_html_e( 'OR', 'adifier' ) ?></h6></div>

					<ul class="list-unstyled list-inline social-login">
						<?php do_action( 'adifier_social_login_button' ) ?>
					</ul>

					<div class="text-center">
						<a href="#" class="register-acc" data-toggle="modal" data-target="#login" data-dismiss="modal"><?php esc_html_e( 'Already have an account? Login here.', 'adifier' ) ?></a>
					</div>

					<input type="hidden" value="adifier_register" name="action" />					
				</div>
			</form>
		</div>

	</div>
</div>

<div class="modal in lrr" id="recover" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form"  autocomplete="off">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e( 'Recover Password', 'adifier' ) ?></h5>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>
				<div class="modal-body">
					<div class="ajax-form-result"></div>
					<?php if( !empty( $_GET['rec_hash'] ) && !empty( $_GET['login'] ) ): ?>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group has-feedback relative-wrap">
									<label for="rec_password" class="bold"><?php esc_html_e( 'Password *', 'adifier' ) ?></label>
									<input type="password" class="form-control reveal-password" id="rec_password" name="rec_password" placeholder="<?php esc_attr_e( 'Use a strong password', 'adifier' ) ?>" />
									<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group has-feedback relative-wrap">
									<label for="rec_r_password" class="bold"><?php esc_html_e( 'Repeat Password *', 'adifier' ) ?></label>
									<input type="password" class="form-control reveal-password" id="rec_r_password" name="rec_r_password" placeholder="<?php esc_attr_e( 'To make sure that it is correct', 'adifier' ) ?>" />
									<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
								</div>
							</div>
						</div>
						<input type="hidden" value="<?php echo esc_attr( $_GET['rec_hash'] ) ?>" name="rec_hash" />
						<input type="hidden" value="<?php echo esc_attr( $_GET['login'] ) ?>" name="rec_username" />
						<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Set Password', 'adifier' ) ?> </a>
					<?php else: ?>
						<div class="form-group has-feedback">
							<label for="rec_email" class="bold"><?php esc_html_e( 'Email *', 'adifier' ) ?></label>
							<input type="text" class="form-control" id="rec_email" name="rec_email" placeholder="<?php esc_attr_e( 'Your registered email', 'adifier' ) ?>" />
						</div>
						<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Recover', 'adifier' ) ?> </a>
					<?php endif; ?>

					<div class="text-center">
						<a href="#" class="register-acc" data-toggle="modal" data-target="#login" data-dismiss="modal"><?php esc_html_e( 'Already have an account? Login here.', 'adifier' ) ?></a>
					</div>

					<input type="hidden" value="adifier_recover" name="action" />					
				</div>
			</form>
		</div>

	</div>
</div>
<?php endif; ?>

<div class="modal in" id="quick-search" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form"  autocomplete="off">
				<div class="modal-header">
					<h6 class="modal-title"><?php esc_html_e( 'Quick Search', 'adifier' ) ?></h6>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>

				<div class="modal-body">

					<div class="adifier-form quick-search-form">
						<label for="qs-search"><?php esc_html_e( 'Find ad (min. 4 chars)', 'adifier' ); ?></label>
						<input type="text" value="" id="qs-search" name="s" placeholder="<?php esc_attr_e( 'Search for...', 'adifier' ) ?>" />
						<a href="javascript:void(0);" class="quick-search-status"></a>
					</div>

					<div class="ajax-form-result"></div>
				</div>
			</form>
		</div>

	</div>
</div>

<?php if( adifier_get_option( 'enable_compare' ) == 'yes' ): ?>
<div class="modal in" id="compare" tabindex="-1" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<form method="post" class="ajax-form"  autocomplete="off">
				<div class="modal-header">
					<h6 class="modal-title"><?php esc_html_e( 'Compare', 'adifier' ) ?></h6>
					<a href="#" data-dismiss="modal"><i class="aficon-times"></i></a>
				</div>

				<div class="modal-body"></div>
			</form>
		</div>

	</div>
</div>
<?php endif ?>


<?php if( function_exists('adifier_create_post_types') ): ?>
<div class="search-sidebar animation">
	<?php $is_labeled = true; ?>
	<div class="flex-wrap">
		<form action="<?php echo adifier_get_search_link() ?>" class="labeled-main-search">
			<h5><?php esc_html_e( 'I\'m interested in...', 'adifier' ); ?></h5>
			<div>
				<label for="keyword"><?php esc_html_e( 'Keyword', 'adifier' ) ?></label>
				<?php include( get_theme_file_path( 'includes/headers/search-parts/keyword.php' ) ); ?>
			</div>
			<?php include( get_theme_file_path( 'includes/headers/search-parts/location.php' ) ); ?>
			<div>
				<label for="category"><?php esc_html_e( 'Category', 'adifier' ) ?></label>
				<?php include( get_theme_file_path( 'includes/headers/search-parts/category.php' ) ); ?>
			</div>
			<div class="search-submit">
				<?php include( get_theme_file_path( 'includes/headers/search-parts/submit.php' ) ); ?>
			</div>
		</form>
	</div>
</div>
<?php endif; ?>	