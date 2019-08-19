<?php
$keyword 	= !empty( $_GET['keyword'] ) ? $_GET['keyword'] : '';
$filter 	= !empty( $_GET['filter'] ) ? $_GET['filter'] : '';
$orderby 	= !empty( $_GET['orderby'] ) ? $_GET['orderby'] : '';
$order 		= !empty( $_GET['order'] ) ? $_GET['order'] : '';
$type 		= !empty( $_GET['type'] ) ? $_GET['type'] : '';
$approval_method = adifier_get_option( 'approval_method' );
$enable_promotions = adifier_get_option( 'enable_promotions' );
?>

<div class="author-panel">
	<div class="white-block white-block-extra-padding">
		<div class="white-block-title">
			<h5>
				<?php
				switch( $screen ){
					case 'auctions' 	: esc_html_e( 'Auctions', 'adifier' ); break;
					case 'favorites' 	: esc_html_e( 'Your Favorite Ads', 'adifier' ); break;
					default 			: esc_html_e( 'Your Posted Ads', 'adifier' ); 
				}
				?>
			</h5>
		</div>
		<div class="white-block-content">
			<div class="adverts-filter flex-wrap flex-center <?php echo  $screen == 'auctions' ? esc_attr( 'auctions-filter' ) : esc_attr( '' ) ?>">
				<ul class="list-unstyled list-inline">
					<li class="<?php echo empty( $filter ) ? esc_attr('active') : '' ?>">
						<a href="<?php echo esc_url( add_query_arg( 'screen', $screen, $author_url ) ) ?>">
							<?php esc_html_e( 'All', 'adifier' ) ?>
						</a>
					</li>
					<li class="<?php echo  $filter == 'live' ? esc_attr('active') : '' ?>">
						<a href="<?php echo esc_url( add_query_arg( array( 'screen' => $screen, 'filter' => 'live' ), $author_url ) ) ?>">
							<?php esc_html_e( 'Live', 'adifier' ) ?>
						</a>
					</li>
					<li class="<?php echo  $filter == 'expired' ? esc_attr('active') : '' ?>">
						<a href="<?php echo esc_url( add_query_arg( array( 'screen' => $screen, 'filter' => 'expired' ), $author_url ) ) ?>">
							<?php esc_html_e( 'Expired', 'adifier' ) ?>
						</a>
					</li>
					<li class="<?php echo  $filter == 'sold' ? esc_attr('active') : '' ?>">
						<a href="<?php echo esc_url( add_query_arg( array( 'screen' => $screen, 'filter' => 'sold' ), $author_url ) ) ?>">
							<?php esc_html_e( 'Sold', 'adifier' ) ?>
						</a>
					</li>
					<?php if( $screen == 'ads' && $approval_method == 'manual' ): ?>
						<li class="<?php echo  $filter == 'update' ? esc_attr('active') : '' ?>">
							<a href="<?php echo esc_url( add_query_arg( array( 'screen' => $screen, 'filter' => 'update' ), $author_url ) ) ?>">
								<?php esc_html_e( 'Update', 'adifier' ) ?>
							</a>
						</li>
						<li class="<?php echo  $filter == 'pending' ? esc_attr('active') : '' ?>">
							<a href="<?php echo esc_url( add_query_arg( array( 'screen' => $screen, 'filter' => 'pending' ), $author_url ) ) ?>">
								<?php esc_html_e( 'Pending', 'adifier' ) ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>

				<form action="<?php echo esc_url( $author_url ) ?>" class="key-submit-form">
					<input type="text" name="keyword" placeholder="<?php esc_attr_e( 'Filter ads...', 'adifier' ) ?>" value="<?php echo esc_attr( $keyword ) ?>" />
					<div class="styled-select inline-select change-submit">
						<select name="orderby">
							<option value="expire" <?php selected( $orderby, 'expire' ) ?>><?php esc_html_e( 'Sort By Expire', 'adifier' ) ?></option>
							<option value="date" <?php selected( $orderby, 'date' ) ?>><?php esc_html_e( 'Sort By Date', 'adifier' ) ?></option>
							<option value="price" <?php selected( $orderby, 'price' ) ?>><?php esc_html_e( 'Sort By Price', 'adifier' ) ?></option>
							<option value="title" <?php selected( $orderby, 'title' ) ?>><?php esc_html_e( 'Sort By Title', 'adifier' ) ?></option>
						</select>
					</div>
					<div class="styled-select inline-select change-submit">
						<select name="order">
							<option value="DESC" <?php selected( $order, 'DESC' ) ?>><?php esc_html_e( 'Descending', 'adifier' ) ?></option>
							<option value="ASC" <?php selected( $order, 'ASC' ) ?>><?php esc_html_e( 'Ascending', 'adifier' ) ?></option>
						</select>
					</div>
					<?php if( $screen !== 'auctions' && !adifier_is_single_ad_type() ): ?>
						<div class="styled-select inline-select change-submit">
							<select name="type">
								<option value=""><?php esc_html_e( 'All Types', 'adifier' ) ?></option>
								<?php if( adifier_is_allowed_ad_type(1) ): ?>
									<option value="1" <?php selected( $type, '1' ) ?>><?php esc_html_e( 'Sell', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(2) ): ?>
									<option value="2" <?php selected( $type, '2' ) ?>><?php esc_html_e( 'Auction', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(3) ): ?>
									<option value="3" <?php selected( $type, '3' ) ?>><?php esc_html_e( 'Buy', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(4) ): ?>
									<option value="4" <?php selected( $type, '4' ) ?>><?php esc_html_e( 'Exchange', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(5) ): ?>
									<option value="5" <?php selected( $type, '5' ) ?>><?php esc_html_e( 'Gift', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(6) ): ?>
									<option value="6" <?php selected( $type, '6' ) ?>><?php esc_html_e( 'Rent', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(7) ): ?>
									<option value="7" <?php selected( $type, '7' ) ?>><?php esc_html_e( 'Job - Offer', 'adifier' ) ?></option>
								<?php endif; ?>
								<?php if( adifier_is_allowed_ad_type(8) ): ?>
									<option value="8" <?php selected( $type, '8' ) ?>><?php esc_html_e( 'Job - Wanted', 'adifier' ) ?></option>
								<?php endif; ?>
							</select>
						</div>
					<?php endif; ?>
					<input type="hidden" name="screen" value="<?php echo esc_attr( $screen ) ?>" />
					<input type="hidden" name="filter" value="<?php echo esc_attr( $filter ) ?>" />
				</form>
			</div>
			<?php
			$cur_page = !empty( $_GET['cpage'] ) ? $_GET['cpage'] : 1;
			$run_query = true;

			$args = array(
				'post_type'			=> 'advert',
				'post_status'		=> 'publish',
				'orderby'			=> $orderby,
				'order'				=> $order,
				'paged'				=> $cur_page,
				'posts_per_page'	=> 25,
				's'					=> $keyword,
				'type'				=> $type
			);

			if( $filter == 'expired' ){
				$args['return_all'] = false;
				$args['expired'] = true;
			}
			else if( $filter == 'sold' ){
				$args['return_all'] = false;
				$args['sold'] = true;
			}
			else if ( $filter == 'live' ){
				$args['return_all'] = false;
			}
			else{
				$args['return_all'] = true;
			}


			if( $screen == 'favorites' ){
				$args['post__in'] = get_user_meta( get_current_user_id(), 'favorites_ads', true);
			}
			else if( $screen == 'auctions' ){
				$args['post__in'] = adifier_get_auctions_ids();
			}
			else{
				$args['author'] = $author->ID;
				$args['post_status'] = 'publish,draft';
			}

			if( $filter == 'pending' ){
				$args['post_status'] = 'draft';
			}
			else if( $filter == 'update' ){
				$args['meta_query'] = array(
					array(
						'key' 	=> 'adifier_has_update',
						'value'	=> '1'
					)	
				);
			}

			if( isset( $args['author'] ) || !empty( $args['post__in'] ) ){
				$ads = new Adifier_Advert_Query( $args ); 

				$page_links_total =  $ads->max_num_pages;
				$page_links = paginate_links( 
					array(
						'base' => '%_%',
						'format' => '?cpage=%#%',
						'prev_next' => true,
						'end_size' => 2,
						'mid_size' => 2,
						'total' => $page_links_total,
						'current' => $cur_page,	
						'prev_next' => false,
					)
				);

				if( $ads->have_posts() ){
					?>
					<div class="profile-advert-listing">
						<div class="profile-advert-listing-titles profile-advert">
							<div>

							</div>
							<div>
								<?php esc_html_e('Title', 'adifier') ?>
							</div>
							<div>
								<?php esc_html_e('Status', 'adifier') ?>
							</div>
							<div>
								<?php esc_html_e('Price', 'adifier') ?>
							</div>
							<div>
								<?php esc_html_e('Expires', 'adifier') ?>
							</div>
							<?php if( $screen == 'ads' ): ?>
								<div class="profile-advert-views">
									<?php esc_html_e('Views', 'adifier') ?>
								</div>
							<?php endif; ?>
							<div class="action <?php echo $screen !== 'ads' ? esc_attr( 'action-expand' ) : '' ?>">
								<?php esc_html_e('Action', 'adifier') ?>
							</div>
						</div>
						<?php
						while( $ads->have_posts() ){
							$ads->the_post();
							$views = adifier_get_advert_meta( get_the_ID(), 'advert_views', true );
							$views = empty( $views ) ? 0 : $views;
							?>
							<div class="profile-advert advert-<?php echo esc_attr( get_the_ID() ); ?>" <?php echo adifier_get_advert_attr_data(); ?>>
								<div>
									<a href="<?php the_permalink() ?>" target="_blank">
										<?php adifier_get_advert_image() ?>
									</a>
								</div>
								<div>
									<h5>
										<a href="<?php the_permalink() ?>" target="_blank">
											<?php the_title() ?>
										</a>
									</h5>
									<div class="profile-advert-cats">
										<?php  
										$categories = get_the_terms( get_the_ID(), 'advert-category' );
										$categories = adifier_taxonomy_hierarchy( $categories );
										adifier_category_hierarchy_profile_adverts( $categories );
										?>
									</div>
								</div>
								<div>
									<?php echo adifier_get_advert_status() ?>
								</div>
								<div>
									<?php echo adifier_get_advert_price(); ?>
								</div>
								<div class="profile-advert-expire">
									<?php
									$expire = adifier_get_advert_meta( get_the_ID(), 'expire', true );
									if( !empty( $expire ) ){
										echo date_i18n( get_option( 'date_format' ), $expire );
									}
									?>
								</div>
								<?php if( $screen == 'ads' ): ?>
									<div class="profile-advert-views">
										<?php echo esc_html( $views ); ?>
									</div>
								<?php endif; ?>
								<div class="action <?php echo $screen !== 'ads' ? esc_attr( 'action-expand' ) : '' ?>">
									<?php
									if( $approval_method == 'auto' ){
										$edit_id = get_the_ID();
									}
									else{
										$children = get_children(array(
											'post_type' 	=> 'advert',
											'post_parent' 	=> get_the_ID(),
											'numberposts'	=> '1'
										));
										if( !empty( $children ) ){
											$child = array_shift( $children );
											$edit_id = $child->ID;
										}
										else{
											$edit_id = get_the_ID();
										}
									}
									?>									
									<a href="<?php the_permalink( $edit_id ) ?>" target="_blank" title="<?php esc_attr_e( 'View Ad', 'adifier' ) ?>">
										<i class="aficon-eye"></i>
									</a>
									<?php if( $screen == 'ads' ): ?>
										<a href="<?php echo esc_url( add_query_arg( array( 'screen' => 'edit', 'id' => $edit_id ), $author_url ) ) ?>" title="<?php esc_attr_e( 'Edit Ad', 'adifier' ) ?>">
											<i class="aficon-edit"></i>
										</a>
										<a href="javascript:void(0);" class="profile-delete-advert" data-id="<?php echo esc_attr( get_the_ID() ) ?>" data-confirm="<?php esc_attr_e( 'Are you sure?', 'adifier' ) ?>" title="<?php esc_attr_e( 'Delete Ad', 'adifier' ) ?>">
											<i class="aficon-trash-alt"></i>
										</a>
										<?php if( !adifier_is_expired() && $enable_promotions == 'yes' ): ?>
											<a href="javascript:void(0)" class="profile-promote-advert" data-id="<?php echo esc_attr( get_the_ID() ) ?>" title="<?php esc_attr_e( 'Promote Ad', 'adifier' ) ?>">
												<i class="aficon-bullhorn"></i>
											</a>
										<?php endif; ?>
									<?php elseif( $screen == 'favorites' ): ?>
										<a href="javascript:void(0)" class="remove-favorites" data-id="<?php echo esc_attr( get_the_ID() ) ?>" title="<?php esc_attr_e( 'Remove From Favorites', 'adifier' ) ?>" data-confirm="<?php esc_attr_e( 'Are you sure?', 'adifier' ) ?>">
											<i class="aficon-heart-o"></i>
										</a>										
									<?php endif; ?>
								</div>
							</div>
							<?php
						}
					?>
					</div>
				<?php
				}
				else{
					if( $screen == 'ads' ){
						?>
						<div class="text-center author-no-listing">
							<a href="<?php echo esc_url( add_query_arg( 'screen', 'new', $author_url ) ) ?>">
								<i class="aficon-plus-circle"></i>
							</a>
							<h5><?php esc_html_e( 'Offer your product/service by submitting your first advert', 'adifier' ) ?></h5>
						</div>
						<?php
					}
					else{
						?>
						<div class="text-center author-no-listing">
							<a href="<?php echo home_url('/') ?>">
								<i class="aficon-plus-circle"></i>
							</a>
							<h5><?php $screen == 'auctions' ? esc_html_e( 'Currently you do not participate in any auction.', 'adifier' ) : esc_html_e( 'Currently you do not have any favorite ads, add some.', 'adifier' ) ?></h5>
						</div>						
						<?php
					}
				}
				wp_reset_postdata();
			}
			else{
				?>
				<div class="text-center author-no-listing">
					<a href="<?php echo home_url('/') ?>">
						<i class="aficon-plus-circle"></i>
					</a>
					<h5><?php $screen == 'auctions' ? esc_html_e( 'Currently you do not participate in any auction.', 'adifier' ) : esc_html_e( 'Currently you do not have any favorite ads, add some.', 'adifier' ) ?></h5>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php if( !empty( $page_links ) ): ?>
		<div class="pagination">
			<?php echo $page_links ?>
		</div>
	<?php endif; ?>
</div>