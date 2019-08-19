<?php 
include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/header-search.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );

$filter = !empty( $_GET['filter-seller-ads'] ) ? $_GET['filter-seller-ads'] : '';

$phone = get_user_meta( $author->ID, 'phone', true );
$description = get_user_meta( $author->ID, 'description', true );
$location = get_user_meta( $author->ID, 'location', true );
$address = adifier_show_profile_address( $location, $author->ID );
$social = adifier_profile_social_links( $author->ID );
?>
<main class="author-visited">
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
				<div class="white-block author-has-posts">
					<div class="white-block-content">

						<div class="seller-details flex-wrap flex-start-h">
							<a href="<?php echo get_author_posts_url( $author->ID ) ?>" class="avatar-wrap">
								<?php echo get_avatar( $author->ID, 70 ); ?>
							</a>

							<div class="seller-name">
								<h5>
									<a href="<?php echo get_author_posts_url( $author->ID ) ?>">
										<?php echo adifier_author_name( $author )  ?>
									</a>
								</h5>
								<?php adifier_user_rating( $author->ID ); ?>
								<?php echo adifier_seller_online_status( $author->ID ) ?>
							</div>
						</div>

						<ul class="author-details-list list-unstyled cf-advert-list">
							<?php if( !empty( $address ) ): ?>
								<li>
									<span class="cf-label">
										<i class="aficon-map-marker-alt-o"></i>
										<?php esc_html_e( 'Location:', 'adifier' ) ?>
									</span>
									<div class="cf-value">
										<?php echo implode( ', ', $address ) ?>
									</div>
								</li>
							<?php endif; ?>
							<li>
								<span class="cf-label">
									<i class="aficon-user-alt"></i>
									<?php esc_html_e( 'Joined:', 'adifier' ) ?>
								</span>
								<div class="cf-value">
									<?php echo date_i18n( get_option( 'date_format' ), strtotime( $author->user_registered ) ) ?>
								</div>
							</li>
						</ul>

						<?php adifier_phone_html( $phone ); ?>
					</div>
				</div>

				<?php
				if( !empty( $description ) || !empty( $social ) ){
					?>
					<div class="white-block">
						<div class="white-block-title">
							<h5><?php esc_html_e( 'About Us', 'adifier' ) ?></h5>
						</div>
						<div class="white-block-content">
							<?php echo esc_html( $description ); ?>
							<?php if( !empty( $social ) ): ?>
								<ul class="list-inline list-unstyled social-contact">
									<?php
									foreach( $social as $network => $link ){
										?>
										<li>
											<a href="<?php echo esc_url( $link ) ?>" target="_blank" rel="nofollow" class="af-button">
												<i class="aficon-<?php echo esc_attr( $network ) ?>"></i>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							<?php endif; ?>
						</div>
					</div>						
					<?php
				}
				?>

				
				<?php 
				if( adifier_get_option( 'use_google_location' ) == 'yes' && !empty( $location['lat'] ) && !empty( $location['long'] ) ){
					?>
					<div class="white-block map-wrapper">
						<div class="location-map" data-lat="<?php echo esc_attr( $location['lat'] ) ?>" data-long="<?php echo esc_attr( $location['long'] ) ?>"></div>
						<?php if( adifier_get_option('use_google_direction') == 'yes' ): ?>
							<a href="javascript:void(0);" target="_blank" class="af-get-directions" title="<?php esc_attr_e( 'Get Directions', 'adifier' ) ?>">
								<img src="<?php echo esc_url( get_theme_file_uri( '/images/directions.png' ) ) ?>" alt="directions">
							</a>
						<?php endif; ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="col-sm-8">
				<?php
				$page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

				$pagination = paginate_links( array(
					'prev_next' 	=> true,
					'end_size' 		=> 2,
					'mid_size' 		=> 2,
					'current' 		=> $page,
					'prev_next' 	=> false,
				));

				if( have_posts() || !empty( $filter ) ){
					?>
					<div class="white-block author-listing">
						<div class="white-block-content">
							<div class="flex-wrap">
								<h5 class="no-margin"><?php esc_html_e( 'Our Ads', 'adifier' ) ?></h5>
								<form method="GET" action="<?php echo esc_url( get_author_posts_url( $author->ID ) ) ?>">
									<input type="text" class="form-control" name="filter-seller-ads" placeholder="<?php esc_attr_e( 'Search for...', 'adifier' ); ?>" value="<?php echo esc_attr( $filter ) ?>">
								</form>
							</div>
						</div>
					</div>				
					<?php
					if( have_posts() ){
						?>
						<div class="af-items-1 af-listing-list">
							<?php
							while( have_posts() ){
								the_post();
								?>
								<div class="af-item-wrap">
									<?php include( get_theme_file_path( 'includes/advert-boxes/list.php' ) ); ?>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
		
					<?php
					if( !empty( $pagination ) ){
						?>
						<div class="pagination">
							<?php echo $pagination ?>
						</div>
						<?php
					}
					?>
				<?php
				}
				wp_reset_postdata();
				?>
			</div>			
		</div>
		<?php
		$reviews = new Adifier_Reviews_Frontend(array(
			'author_id' => $author->ID
		));
		if( $reviews->found_results > 0 ){
			?>
			<div class="author-reviews" data-author="<?php echo esc_attr( $author->ID ) ?>">
				<div class="white-block">
					<div class="white-block-content">
						<div class="flex-wrap">
							<h5><?php esc_html_e( 'Our Recommendations', 'adifier' ) ?></h5>
							<div class="styled-select">
								<select class="reviews-filter">
									<option value=""><?php esc_html_e( 'All', 'adifier' ) ?></option>
									<option value="seller"><?php esc_html_e( 'As Seller', 'adifier' ) ?></option>
									<option value="buyer"><?php esc_html_e( 'As Buyer', 'adifier' ) ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="author-reviews-ajax">
					<?php $reviews->display_frontend_reviews(); ?>
				</div>
			</div>
			<?php
		}
		?>		
	</div>
</main>