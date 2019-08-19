<?php
/*=============================
	DEFAULT SINGLE
=============================*/
get_header();
the_post();

include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );

$enable_logout_contact = adifier_get_option( 'enable_logout_contact' );

adifier_increase_advert_view();
$advert_gallery = get_post_meta( get_the_ID(), 'advert_gallery' );
$images = !empty( $advert_gallery ) ? $advert_gallery : array();
has_post_thumbnail() ? array_unshift( $images, get_post_thumbnail_id() ) : '';
$images = array_unique( $images );

$video_thumbnail = adifier_get_option( 'video_thumbnail' );
if( !empty( $video_thumbnail ) ){
	$videos = get_post_meta( get_the_ID(), 'advert_videos' );
}

$author_id = get_the_author_meta('ID');
$author = get_user_by( 'ID', $author_id );

$advert_details = new Adifier_Custom_Front_Advert();

$type = adifier_get_advert_meta( get_the_ID(), 'type', true );

$phone = adifier_get_advert_phone();
$location = adifier_get_advert_geo_location();

$address = adifier_show_single_address( $location );

if( !adifier_is_expired() && adifier_get_advert_meta( get_the_ID(), 'sold', true ) == 0 && get_post_status() != 'private' ):

ob_start();
?>

<div class="single-price-wrap">
	<div class="white-block single-price">
		<i class="aficon-dollar-sign"></i>
		<div class="white-block-content">
			<?php if( $type == 2 && !is_user_logged_in() ): ?>
				<div class="flex-wrap">
					<?php echo adifier_get_advert_price() ?>
					<a href="#" data-toggle="modal" data-target="#login" class="bid-login" title="<?php esc_attr_e( 'Place your bid', 'adifier' ) ?>"><i class="aficon-arrow-alt-up"></i></a>
				</div>
			<?php else: ?>
				<?php 
				echo adifier_get_advert_price();
				?>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
$price_html = ob_get_contents();
ob_end_clean();
?>

<main>
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<div class="single-advert-tags">
					<?php include( get_theme_file_path( 'includes/advert-boxes/ribbons.php' ) ) ?>
				</div>				
				<?php 
				$counter = 0;
				if( !empty( $images ) || !empty( $videos ) ){
					echo '<div class="single-advert-media">';					
						$main_slider = '<div class="owl-carousel single-slider">';
						$thumb_slider = '<div class="owl-carousel single-slider-thumbs hide-print">';
						if( !empty( $images ) ){
							foreach( $images as $image_id ){
								$big_image = wp_get_attachment_image_src( $image_id, 'full' );
								$main_slider .= '<a href="'.esc_url( $big_image[0] ).'" class="single-slider-href">'. wp_get_attachment_image( $image_id, 'adifier-single-slider' ).'</a>';
								$thumb_slider .= '<div class="single-thumb-item animation '.( $counter == 0 ? esc_attr( 'active' ) : esc_attr( '' ) ).'" data-item="'.esc_attr( $counter ).'">'.wp_get_attachment_image( $image_id, 'thumbnail' ).'</div>';
								$counter++;
							}
						}
						if( !empty( $videos ) ){
							foreach( $videos as $video ){
								$main_slider .= '<a href="'.esc_url( str_replace('m.you', 'www.you', $video ) ).'" class="single-slider-href owl-video"></a>';
								$thumb_slider .= '<div class="single-thumb-item" data-item="'.esc_attr( $counter ).'"><img src="'.esc_url( $video_thumbnail['url'] ).'" height="'.esc_attr( $video_thumbnail['height'] ).'" width="'.esc_attr( $video_thumbnail['width'] ).'" alt="video"></div>';
								$counter++;
							}
						}
						$main_slider .= '</div>';
						$thumb_slider .= '</div>';
						echo $main_slider;
						if( $counter > 1 ){
							echo $thumb_slider;
						}
					echo '</div>';
				}
				?>

				<div class="hide-price-big">
					<?php echo  $price_html; ?>
				</div>

				<?php $advert_details->print_cf_data() ?>

				<div class="white-block">
					<div class="white-block-content">
						<h1 class="blog-item-title h4-size"><?php the_title(); ?></h1>
						<div class="post-content clearfix">
							<?php the_content() ?>
						</div>						
						<ul class="list-inline list-unstyled single-meta top-advert-meta">
							<?php if( !empty( $address ) ): ?>
								<li>
									<i class="aficon-map-marker-alt-o"></i>
									<?php echo implode( ', ', $address ) ?>
								</li>
							<?php endif; ?>							
							<?php
							$condition = adifier_get_advert_condition();
							if( !empty( $condition ) ):
							?>
							<li>
								<i class="aficon-info-circle"></i>
								<?php echo esc_html( $condition ); ?>
							</li>
							<?php endif; ?>
							<li>
								<i class="aficon-eye"></i>
								<?php echo adifier_get_advert_views(); ?>
								&nbsp;&nbsp;#<?php the_ID() ?>
								&nbsp;&nbsp;<i class="aficon-calendar-alt"></i> <?php echo date_i18n( get_option( 'date_format' ), get_the_time('U') ) ?>
							</li>
						</ul>
					</div>
				</div>
				<div class="white-block details">
					<div class="white-block-content">
						<h1 class="blog-item-title h4-size">More Details</h1>
						<ul class="list-unstyled cf-advert-list list-inline">
							<li class="flex-wrap">
								<span class="cf-label"> Brand </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'brand', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Thickness </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'thickness', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Material Grade </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'material_grade', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Is It ISO Certified? </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'iso_cert', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Surface Treatment </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'surface_treatment', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Length in MM/ in feet </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'length', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Width in MM/ in feet </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'width', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Feature </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'feature', true ); ?> </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-label"> Shape </span>
								<span class="cf-value"> <?php echo adifier_get_advert_meta( $id, 'shape', true ); ?> </span>
							</li>
							
						</ul>
						
					</div>
				</div>
				<div class="hide-print small-screen-last">
					<?php comments_template( '', true ) ?>
				</div>

			</div>
			<div class="col-sm-4">

				<?php echo wp_kses_post( $price_html ) ?>

				<?php
				if( $type == 2 ){
					$expire = adifier_get_advert_meta( get_the_ID(), 'expire', true );
					if( !empty( $expire ) ){
						?>
						<div class="white-block countdown-wrap">
							<div class="white-block-content">
								<div class="flex-wrap flex-start-h">
									<i class="aficon-stopwatch"></i>
									<div class="flex-right">
										<p><?php esc_html_e( 'Auction Ends In:', 'adifier' ) ?></p>
										<h5 class="countdown" data-expire="<?php echo esc_attr( $expire ) ?>" data-current-time="<?php echo esc_attr( current_time( 'timestamp' ) ); ?>" data-single="<?php esc_attr_e( 'Day', 'adifier' ) ?>" data-multiple="<?php esc_html_e( 'Days', 'adifier' ) ?>"></h5>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
				}
				?>

				<?php
				if( $type == 2 && $author_id !== get_current_user_id() && is_user_logged_in() ){
					$min_bid = adifier_min_bid_price();
					?>
					<div class="white-block advert-bidding hide-print">
						<div class="white-block-title">
							<h5><?php esc_html_e( 'Bid For Goods', 'adifier' ) ?></h5>
						</div>
						<div class="white-block-content">
							<form class="ajax-form bidding-form">
								<div class="flex-wrap flex-start-v">
									<div class="form-group">
										<input type="text" name="bid" placeholder="<?php esc_attr_e( 'Min bid: ', 'adifier' ) ?><?php echo esc_attr( strip_tags( adifier_price_format( $min_bid, adifier_get_advert_meta( get_the_ID(), 'currency', true ) ) ) ) ?>">
									</div>
									<input type="hidden" name="action" value="adifier_place_bid">
									<input type="hidden" name="advert_id" value="<?php echo esc_attr( get_the_ID() ) ?>">
									<a href="javascript:void(0);" class="af-button submit-ajax-form af-cta" data-callbacktrigger="bidding-response"><?php esc_html_e( 'Place Bid', 'adifier' ) ?></a>
								</div>
								<div class="ajax-form-result"></div>
							</form>
							<form class="ajax-form bidding-history-form">
								<input type="hidden" name="action" value="adifier_bid_history">
								<input type="hidden" name="advert_id" value="<?php echo esc_attr( get_the_ID() ) ?>">
								<input type="hidden" name="bidpage" value="1">
								<div class="ajax-form-result bidding-history-results"></div>
								<a href="javascript:void(0);" class="bidding-history af-button submit-ajax-form bidding-excerpt" data-callbacktrigger="bidding-history-response">
									<?php esc_html_e( 'See Latest Bidds', 'adifier' ) ?>
								</a>
							</form>
						</div>
					</div>
					<?php
				}
				?>

				<div class="white-block contact-scroll-details">
					<div class="white-block-title">
						<h5><?php $type == 3 ? esc_html_e( 'Buyer Information', 'adifier' ) : esc_html_e( 'Seller Information', 'adifier' ) ?></h5>
					</div>
					<div class="white-block-content">
						<div class="seller-details flex-wrap flex-start-h">
							<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ) ?>" class="avatar-wrap">
								<?php echo get_avatar( $author_id, 70 ); ?>
							</a>

							<div class="seller-name">
								<h5>
									<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ) ?>">
										<?php echo adifier_author_name( $author )  ?>
									</a>
								</h5>
								<?php adifier_user_rating( $author_id ); ?>
								<?php echo adifier_seller_online_status( $author_id ) ?>
							</div>
						</div>

						<?php adifier_phone_html( $phone );?>
						<ul class="list-unstyled cf-advert-list list-inline" style="margin-top: 20px;">
							<li class="flex-wrap">
								<span class="cf-value"> Firm Name </span>
								<span class="cf-label"> <?php echo get_user_meta( $author_id, 'firm_name', true );?>  </span>
							</li>
							<li class="flex-wrap">
								<span class="cf-value"> GST No. </span>
								<span class="cf-label"> <?php echo get_user_meta( $author_id, 'gst', true );?>  </span>
							</li>
						</ul>
						<?php if( ( is_user_logged_in() && $author_id !== get_current_user_id() ) || ( !is_user_logged_in() && $enable_logout_contact == 'no' ) ): ?>
							<?php
							$link = "#";
							$target = 'data-toggle="modal" data-target="#login"';
							if( is_user_logged_in() ){
								$link = Adifier_Conversations::has_conversation_started( get_current_user_id(), $author_id );
								$link = stristr( $link, 'http' ) ? $link : '#';
								$target = $link == '#' ? 'data-toggle="modal" data-target="#contact-seller"' : '';
							}
							?>
							<a href="<?php echo esc_url( $link ); ?>" class="contact-seller flex-wrap flex-start-h hide-print" <?php echo  $target ?> data-advert-id="<?php the_ID() ?>">
								<i class="aficon-envelope"></i>
								<span class="flex-right">
									<em><?php $type == 3 ? esc_html_e( 'Contact Buyer', 'adifier' ) : esc_html_e( 'Contact Seller', 'adifier' ) ?></em>
									<span class="description"><?php esc_html_e( 'Ask questions about offer', 'adifier' ) ?></span>
								</span>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<?php if( !is_user_logged_in() && $enable_logout_contact == 'yes' ): ?>
					<div class="white-block hide-print">
						<div class="white-block-title">
							<h5><?php $type == 3 ? esc_html_e( 'Contact Buyer', 'adifier' ) : esc_html_e( 'Contact Seller', 'adifier' ) ?></h5>
						</div>
						<div class="white-block-content">
							<form class="ajax-form logout-contact" action="">
								<div class="form-control">
									<label for="lcf_email"><?php esc_html_e( 'Email *', 'adifier' ) ?></label>
									<input type="text" name="lcf_email" id="lcf_email" placeholder="<?php esc_attr_e( 'Your email for replies', 'adifier' ) ?>" />
								</div>
								<div class="form-control">
									<label for="lcf_name"><?php esc_html_e( 'Name *', 'adifier' ) ?></label>
									<input type="text" name="lcf_name" id="lcf_name" placeholder="<?php esc_attr_e( 'Your name', 'adifier' ) ?>" />
								</div>
								<div class="form-control">
									<label for="lcf_message"><?php esc_html_e( 'Message *', 'adifier' ) ?></label>
									<textarea name="lcf_message" id="lcf_message" placeholder="<?php esc_attr_e( 'Ask us anything', 'adifier' ) ?>"></textarea>
								</div>
								<div class="hidden lcf-toggle">
									<div class="form-control">
										<label for="lcf_username"><?php esc_html_e( 'Username *', 'adifier' ) ?></label>
										<input type="text" name="lcf_username" id="lcf_username" placeholder="<?php esc_attr_e( 'Your desired username', 'adifier' ) ?>" />
									</div>
									<div class="form-control relative-wrap">
										<label for="lcf_password"><?php esc_html_e( 'Password *', 'adifier' ) ?></label>
										<input type="password" name="lcf_password" class="reveal-password" id="lcf_password" placeholder="<?php esc_attr_e( 'Use a strong password', 'adifier' ) ?>" />
										<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
									</div>
									<div class="form-control relative-wrap">
										<label for="lcf_r_password"><?php esc_html_e( 'Password Repeat *', 'adifier' ) ?></label>
										<input type="password" name="lcf_r_password" class="reveal-password" id="lcf_r_password" placeholder="<?php esc_attr_e( 'To make sure that it is correct', 'adifier' ) ?>" />
										<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
									</div>
									<?php adifier_gdpr_checkbox(); ?>
									<div class="form-control">
										<?php adifier_terms_checkbox( 'register_terms' ); ?>
									</div>
								</div>								
								<div class="form-control">
									<div class="styled-checkbox">
										<input id="lcf_register" name="lcf_register" value="1" type="checkbox">
										<label for="lcf_register"><?php esc_html_e( 'Create an account for me', 'adifier' ) ?></label>
									</div>
								</div>

								<input type="hidden" name="action" value="adifier_logout_contact">
								<input type="hidden" name="lcf_ad_id" value="<?php echo esc_attr( get_the_ID() ) ?>">
								<div class="ajax-form-result"></div>
								<a href="javascript:void(0)" class="bidding-history af-button submit-ajax-form"><?php esc_html_e( 'Send', 'adifier' ) ?></a>
							</form>
						</div>
					</div>
				<?php endif; ?>

				<div class="white-block hide-print">
					<div class="white-block-title">
						<h5><?php esc_html_e( 'Ad Action', 'adifier' ) ?></h5>
					</div>
					<div class="white-block-content">
						<ul class="list-unstyle list-inline single-advert-actions flex-wrap">
							<li>
								<a href="#" class="share-advert" data-toggle="modal" data-target="#share">
									<i class="aficon-share-alt"></i>
									<?php esc_html_e( 'Share', 'adifier' ) ?>
								</a>								
							</li>
							<li>
								<a href="javascript:void(0);" class="print-advert">
									<i class="aficon-print"></i>
									<?php esc_html_e( 'Print', 'adifier' ) ?>
								</a>
							</li>
							<li>
								<?php adifier_get_favorites_html(); ?>
							</li>
							<?php
							$reporting_ads_can = adifier_get_option( 'reporting_ads_can' );
							if( $reporting_ads_can !== 'logged' || ( $reporting_ads_can == 'logged' && is_user_logged_in() ) ):
							?>
								<li>
									<a href="javascript:void(0);" class="report-advert">
										<i class="aficon-flag"></i>
										<?php esc_html_e( 'Report', 'adifier' ) ?>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<?php 
				if( adifier_get_option( 'use_google_location' )== 'yes' && !empty( $location['lat'] ) && !empty( $location['long'] ) ){
					$marker = adifier_get_advert_marker( get_the_ID() );
					?>
					<div class="white-block hide-print map-wrapper">
						<div class="location-map" data-lat="<?php echo esc_attr( $location['lat'] ) ?>" data-long="<?php echo esc_attr( $location['long'] ) ?>" data-icon="<?php echo !empty( $marker[0] ) ? esc_url( $marker[0] ) : esc_attr( '' ) ?>" data-iconwidth="<?php echo !empty( $marker[1] ) ? esc_attr( $marker[1] ) : '' ?>" data-iconheight="<?php echo !empty( $marker[2] ) ? esc_attr( $marker[2] ) : '' ?>"></div>
						<?php if( adifier_get_option('use_google_direction') == 'yes' ): ?>
							<a href="javascript:void(0);" target="_blank" class="af-get-directions" title="<?php esc_attr_e( 'Get Directions', 'adifier' ) ?>">
								<img src="<?php echo esc_url( get_theme_file_uri( '/images/directions.png' ) ) ?>" alt="directions">
							</a>
						<?php endif; ?>
					</div>
					<?php		
				}
				?>
				<?php
				$random_author = adifier_get_option( 'random_author' );
				if( !empty( $random_author ) ){
					$ads = new Adifier_Advert_Query(array(
						'posts_per_page' 	=> $random_author,
						'post_type'			=> 'advert',
						'post_status'		=> 'publish',
						'author'			=> $author_id,
						'orderby'			=> 'rand',
						'post__not_in'		=> array( get_the_ID() )
					));
					if( $ads->have_posts() ){
						?>
						<div class="white-block hide-print">
							<div class="white-block-title">
								<h5><?php esc_html_e( 'More Ads From This User', 'adifier' ) ?></h5>
							</div>
							<div class="white-block-content">
								<ul class="list-unstyled random-author-ads">
									<?php
									while( $ads->have_posts() ){
										$ads->the_post();
										?>
										<li>
											<a href="<?php the_permalink() ?>">
												<?php adifier_get_advert_image('thumbnail'); ?>
											</a>
											<div class="flex-right">
												<?php include( get_theme_file_path( 'includes/advert-boxes/title.php' ) ); ?>
												<?php include( get_theme_file_path( 'includes/advert-boxes/bottom-meta.php' ) ); ?>
											</div>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<?php
					}
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
	</div>
</main>
<?php else: ?>
<main>
	<div class="container">
		<div class="white-block big-no">
			<div class="white-block-content">
				<i class="aficon-question-circle"></i>
				<h2><?php esc_html_e( 'This Ad Has Expired', 'adifier'); ?></h2>
			</div>
		</div>
	</div>
</main>
<?php endif; ?>

<?php get_footer(); ?>