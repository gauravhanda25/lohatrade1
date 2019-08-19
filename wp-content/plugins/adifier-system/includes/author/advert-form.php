<div class="author-panel">
	<?php
	if( Adifier_Twilio::is_phone_verification_needed() ){
		Adifier_Twilio::phone_verification_form();
	}
	else if( !adifier_can_post_adverts() ){
		?>
		<div class="white-block white-block-extra-padding">
			<div class="white-block-content">
				<div class="text-center author-no-listing">
					<a href="<?php echo esc_url( add_query_arg( 'screen', 'acc_pay', $author_url ) ) ?>">
						<i class="aficon-plus-circle"></i>
					</a>
					<h5>
						<?php 
						$account_payment = adifier_get_option( 'account_payment' );
						if( $account_payment == 'packages' ){
							esc_html_e( 'You do not have any credits left, please purchase a package', 'adifier' );
						}
						else if( $account_payment == 'subscriptions' ){
							esc_html_e( 'Your subscription has expired, please renew it', 'adifier' );
						}
						else if( $account_payment == 'hybrids' ){
							esc_html_e( 'Your subscription has expired or you have run out of adverts.', 'adifier' );
						}
						?>
					</h5>
				</div>
			</div>
		</div>
		<?php
	}
	else{
		$use_google_location = adifier_get_option( 'use_google_location' );
		$use_predefined_locations = adifier_get_option( 'use_predefined_locations' );
		$show_decimals = adifier_get_option( 'show_decimals' );

		$user_profile_has_location = false;
		$user_profile_has_phone = false;
		if( $use_google_location == 'yes' ){
			$user_location = get_user_meta( get_current_user_id(), 'location', true );
			if( !empty( $user_location['lat'] ) ){
				$user_profile_has_location = true;
			}
		}
		else if( $use_predefined_locations == 'yes' ){
			$location_ids = get_user_meta( get_current_user_id(), 'af_location_id', true );
			if( !empty( $location_ids ) ){
				$user_profile_has_location = true;
			}
		}

		$user_phone = get_user_meta( get_current_user_id(), 'phone', true );
		if( !empty( $user_phone ) ){
			$user_profile_has_phone = true;
		}

		$approval_method = adifier_get_option( 'approval_method' );
		$ad_types = adifier_get_option( 'ad_types' );
		$id = !empty( $_GET['id'] ) ? $_GET['id'] : '';
		$advert_data = array(
			'title'				=> '',
			'parent_id'			=> 0,
			'description'		=> '',
			'featured_image'	=> '',
			'images'			=> '',
			'videos'			=> '',
			'is_sold'			=> 0,
			'is_negotiable'		=> 0,
			'type'				=> 1,
			'thickness'			=> 1,
			'material_grade'	=> '',
			'cond'				=> 0,
			'brand'				=> '',
			'iso_cert'			=> '',
			'length'			=> '',
			'width'				=> '',
			'feature'			=> '',
			'shape'				=> '',
			'surface_treatment'	=> '',
			'price'				=> '',
			'sale_price'		=> '',
			'start_price'		=> '',
			'reserved_price'	=> '',
			'currency'			=> '',
			'location'			=> array(
				'lat' 				=> '',
				'long'				=> '',
				'country'			=> '',
				'state'				=> '',
				'city'				=> '',
				'street'			=> ''
			),
			'phone'				=> '',
			'category'			=> '',
			'category_ids'		=> array(),
			'location_ids'		=> array(),
			'rent_period'		=> ''
		);
		if( !empty( $id ) ){
			$advert = get_post( $id );
			if( !empty( $advert ) ){
				$advert_data['title'] 				= $advert->post_title;
				$advert_data['parent_id'] 			= $advert->post_parent;
				$advert_data['description'] 		= $advert->post_content;
				$advert_data['featured_image'] 		= get_post_thumbnail_id( $id );
				$advert_data['images'] 				= get_post_meta( $id, 'advert_gallery' );
				$advert_data['videos'] 				= get_post_meta( $id, 'advert_videos' );
				$advert_data['is_sold'] 			= adifier_get_advert_meta( $id, 'sold', true );
				$advert_data['is_negotiable'] 		= get_post_meta( $id, 'advert_negotiable', true );
				$advert_data['type'] 				= adifier_get_advert_meta( $id, 'type', true );
				$advert_data['thickness'] 			= adifier_get_advert_meta( $id, 'thickness', true );
				$advert_data['brand'] 				= adifier_get_advert_meta( $id, 'brand', true );
				$advert_data['material_grade'] 		= adifier_get_advert_meta( $id, 'material_grade', true );
				$advert_data['iso_cert'] 			= adifier_get_advert_meta( $id, 'iso_cert', true );
				$advert_data['length'] 				= adifier_get_advert_meta( $id, 'length', true );
				$advert_data['width'] 				= adifier_get_advert_meta( $id, 'width', true );
				$advert_data['feature'] 			= adifier_get_advert_meta( $id, 'feature', true );
				$advert_data['shape'] 				= adifier_get_advert_meta( $id, 'shape', true );
				$advert_data['surface_treatment'] 	= adifier_get_advert_meta( $id, 'surface_treatment', true );
				$advert_data['cond'] 				= adifier_get_advert_meta( $id, 'cond', true );
				$advert_data['price'] 				= adifier_get_advert_meta( $id, 'price', true );
				$advert_data['sale_price'] 			= adifier_get_advert_meta( $id, 'sale_price', true );
				$advert_data['start_price'] 		= adifier_get_advert_meta( $id, 'start_price', true ); /*  this colulmn is used as max salary for job offer */
				$advert_data['reserved_price'] 		= get_post_meta( $id, 'advert_reserved_price', true );
				$advert_data['expire'] 				= adifier_get_advert_meta( $id, 'expire', true );
				$advert_data['currency'] 			= adifier_get_advert_meta( $id, 'currency', true );
				$advert_data['phone'] 				= get_post_meta( $id, 'advert_phone', true );
				$advert_data['category']			= get_the_terms( $id, 'advert-category' );
				$advert_data['location_id']			= get_the_terms( $id, 'advert-location' );
				$advert_data['rent_period']			= get_post_meta( $id, 'advert_rent_period', true );

				$advert_data['price'] = !empty( $advert_data['price'] ) && $advert_data['price'] > 0 ? $advert_data['price'] : '';
				$advert_data['sale_price'] = !empty( $advert_data['sale_price'] ) && $advert_data['sale_price'] > 0 ? $advert_data['sale_price'] : '';
				$advert_data['start_price'] = !empty( $advert_data['start_price'] ) && $advert_data['start_price'] > 0 ? $advert_data['start_price'] : '';

				$advert_location = get_post_meta( $id, 'advert_location', true );
				if( !empty( $advert_location ) ){
					$advert_data['location'] = $advert_location;
				}

				if( !empty( $advert_data['category'] ) ){
					$advert_data['category_ids'] = wp_list_pluck( $advert_data['category'], 'term_id' );
				}

				if( !empty( $advert_data['location_id'] ) && !is_wp_error( $advert_data['location_id'] ) ){
					$advert_data['location_ids'] = wp_list_pluck( $advert_data['location_id'], 'term_id' );
				}

				if( !empty( $advert_data['featured_image'] ) ){
					if( is_array( $advert_data['images'] ) ){
						array_unshift( $advert_data['images'], $advert_data['featured_image'] );
					}
					else{
						$advert_data['images'][] = $advert_data['featured_image'];
					}
				}
			}
		}
		extract( $advert_data );
		$currencies = adifier_get_currencies_raw_list();
		?>
		<form class="ajax-save-advert" autocomplete="off">
			<input type="hidden" name="action" value="adifier_save_advert" />
			<input type="hidden" name="advert_id" value="<?php echo esc_attr( $id ) ?>" />
			<?php if( $approval_method == 'manual' ):  ?>
				<input type="hidden" name="advert_parent_id" value="<?php echo esc_attr( $parent_id ) ?>" />
			<?php endif; ?>
			<input type="hidden" name="author_url" value="<?php echo esc_url( $author_url ) ?>" />
			<div class="row">
				<div class="col-sm-7">
					<?php if( $type == '2' ): ?>
						<?php
						$data = adifier_print_bidding_history( $id, 1, adifier_is_expired( $id ) ? true : false, false );
						if( !empty( $data['message'] ) ):
						?>
							<div class="white-block white-block-extra-padding">
								<div class="white-block-title">
									<h5><?php echo esc_html__( 'Bid History', 'adifier' ) ?></h5>
								</div>
								<div class="white-block-content">
									<div class="bidding-history-results"><?php echo $data['message']; ?></div>
									<?php if( !empty( $data['next_page'] ) ): ?>
										<a href="javascript:void(0);" class="bidding-history af-button" data-advertid="<?php echo esc_attr( $id ) ?>" data-bidpage="2">
											<?php esc_html_e( 'Load More', 'adifier' ) ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endif;?>
					<?php endif;?>
					<div class="white-block white-block-extra-padding">
						<div class="white-block-title">
							<div class="flex-wrap flex-center">
								<h5><?php esc_html_e( 'Ad Details', 'adifier' ) ?></h5>
								<?php if( !adifier_is_expired( $id ) && $screen !== 'new' && $type !== '2' ): ?>
									<div class="styled-checkbox">
										<input type="checkbox" id="is_sold" name="is_sold" value="1" <?php checked( 1, $is_sold ) ?>>
										<label for="is_sold"><?php esc_html_e( 'Mark As Sold', 'adifier' ) ?></label>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="white-block-content">
							<div class="form-group">
								<label for="title"><?php esc_html_e( 'Title *', 'adifier' ) ?></label>
								<input type="text" id="title" name="title" value="<?php echo esc_attr( $title ) ?>" placeholder="<?php esc_html_e( 'Precise title is always better', 'adifier' ); ?>" />
							</div>
							<div class="form-group">
								<label for="thickness"><?php esc_html_e( 'Thickness', 'adifier' ) ?></label>
								<input type="text" id="thickness" name="thickness" value="<?php echo esc_attr( $thickness ) ?>" placeholder="<?php esc_html_e( 'Thickness of product', 'adifier' ); ?>" />
							</div>

							<div class="form-group">
									<label for="brand"><?php esc_html_e( 'Brand', 'adifier' ) ?></label>
									<div class="styled-select">
										<select name="brand" id="brand">
											<option value=" " <?php selected( $cond, 0 ) ?>><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
											<option value="Tata" <?php selected( $cond, 'Tata') ?>><?php esc_html_e( 'Tata', 'adifier' ) ?></option>
											<option value="JSW" <?php selected( $cond, 'JSW') ?>><?php esc_html_e( 'JSW', 'adifier' ) ?></option>
											<option value="Apollo" <?php selected( $cond, 'Apollo' ) ?>><?php esc_html_e( 'Apollo', 'adifier' ) ?></option>
											<option value="Metal junction" <?php selected( $cond, 'Metal junction' ) ?>><?php esc_html_e( 'Metal junction', 'adifier' ) ?></option>
											<option value="SAIL" <?php selected( $cond, 'SAIL' ) ?>><?php esc_html_e( 'SAIL', 'adifier' ) ?></option>
											<option value="ESSAR" <?php selected( $cond, 'ESSAR' ) ?>><?php esc_html_e( 'ESSAR', 'adifier' ) ?></option>
											<option value="Jindal" <?php selected( $cond, 'Jindal' ) ?>><?php esc_html_e( 'Jindal', 'adifier' ) ?></option>
											<option value="Imported" <?php selected( $cond, 'Imported' ) ?>><?php esc_html_e( 'Imported', 'adifier' ) ?></option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="material_grade"><?php esc_html_e( 'Material Grade', 'adifier' ) ?></label>
									<input type="text" id="material_grade" name="material_grade" value="<?php echo esc_attr( $material_grade ) ?>" placeholder="<?php esc_html_e( 'Material Grade', 'adifier' ); ?>" />
								</div>
								<div class="form-group">
									<label for="iso_cert"><?php esc_html_e( 'Is It ISO Certified', 'adifier' ) ?></label>
									<div class="styled-select">
										<select name="iso_cert" id="iso_cert">
											<option value="" <?php selected( $iso_cert, '' ) ?>><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
											<option value="Yes" <?php selected( $iso_cert, 'Yes' ) ?>><?php esc_html_e( 'Yes', 'adifier' ) ?></option>
											<option value="No" <?php selected( $iso_cert, 'No' ) ?>><?php esc_html_e( 'No', 'adifier' ) ?></option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="surface_treatment"><?php esc_html_e( 'Surface Treatment', 'adifier' ) ?></label>
									<input type="text" id="surface_treatment" name="surface_treatment" value="<?php echo esc_attr( $surface_treatment ) ?>" placeholder="<?php esc_html_e( 'Surface Treatment', 'adifier' ); ?>" />
								</div>
								<div class="form-group">
									<label for="length"><?php esc_html_e( 'Length in MM/ in feet', 'adifier' ) ?></label>
									<input type="text" id="length" name="length" value="<?php echo esc_attr( $length ) ?>" placeholder="<?php esc_html_e( 'Length in MM/ in feet', 'adifier' ); ?>" />
								</div>
								<div class="form-group">
									<label for="width"><?php esc_html_e( 'Width in MM/ in feet', 'adifier' ) ?></label>
									<input type="text" id="width" name="width" value="<?php echo esc_attr( $width ) ?>" placeholder="<?php esc_html_e( 'Width in MM/ in feet', 'adifier' ); ?>" />
								</div>
								<div class="form-group">
									<label for="feature"><?php esc_html_e( 'Feature', 'adifier' ) ?></label>
									<input type="text" id="feature" name="feature" value="<?php echo esc_attr( $feature ) ?>" placeholder="<?php esc_html_e( 'Feature', 'adifier' ); ?>" />
								</div>
								<div class="form-group">
									<label for="shape"><?php esc_html_e( 'Shape', 'adifier' ) ?></label>
									<input type="text" id="shape" name="shape" value="<?php echo esc_attr( $shape ) ?>" placeholder="<?php esc_html_e( 'Shape', 'adifier' ); ?>" />
								</div>
								
								

							<?php if( $screen == 'new' ): ?>
								<div class="form-group remove-after-initial-save">
									<?php if( adifier_is_single_ad_type() ): ?>
										<?php adifier_print_single_ad_type(); ?>
									<?php else: ?>
										<label for="type"><?php esc_html_e( 'Ad Type', 'adifier' ) ?></label>
										<div class="styled-select">
											<select name="type" id="type">
												<?php if( adifier_is_allowed_ad_type(1) ): ?>
													<option value="1"><?php echo sprintf( esc_html__( 'Sell (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(2) ): ?>
													<option value="2"><?php echo sprintf( esc_html__( 'Auction (Expires in %s days)', 'adifier' ), adifier_get_option( 'auction_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(3) ): ?>
													<option value="3"><?php echo sprintf( esc_html__( 'Buy (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(4) ): ?>
													<option value="4"><?php echo sprintf( esc_html__( 'Exchange (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(5) ): ?>
													<option value="5"><?php echo sprintf( esc_html__( 'Gift (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(6) ): ?>
													<option value="6"><?php echo sprintf( esc_html__( 'Rent (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(7) ): ?>
													<option value="7"><?php echo sprintf( esc_html__( 'Job - Offer (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>
												<?php if( adifier_is_allowed_ad_type(8) ): ?>
													<option value="8"><?php echo sprintf( esc_html__( 'Job - Wanted (Expires in %s days)', 'adifier' ), adifier_get_option( 'regular_expires' ) ) ?></option>
												<?php endif; ?>												
											</select>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if( adifier_is_allowed_ad_type(1) && ( $screen == 'new' || $type == '1' )  ): ?>
								<div class="row advert-type-1">
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<div class="flex-wrap">
												<label for="price"><?php esc_html_e( 'Price', 'adifier' ) ?></label>
												<div class="styled-checkbox negotiable-checkbox">
													<input type="checkbox" id="is_negotiable" name="is_negotiable" value="1" <?php checked( 1, $is_negotiable ) ?>>
													<label for="is_negotiable"><?php esc_html_e( 'Is negotiable?', 'adifier' ) ?></label>
												</div>
											</div>
											<input type="text" id="price" name="price" value="<?php echo !empty( $price ) ? esc_attr( adifier_price_format_value( $price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<label for="sale_price"><?php esc_html_e( 'Sale Price', 'adifier' ) ?></label>
											<input type="text" id="sale_price" name="sale_price" value="<?php echo !empty( $sale_price ) ? esc_attr( adifier_price_format_value( $sale_price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<?php
									if( count( $currencies ) > 1 ){
										?>
										<div class="col-sm-4">
											<?php adifier_currency_select( $currencies, $currency, true ); ?>
										</div>
										<?php
									}
									?>
								</div>
							<?php endif; ?>

							<?php if( $screen == 'new' && adifier_is_allowed_ad_type(2) ): ?>
								<div class="row advert-type-2 <?php echo adifier_is_allowed_ad_type(1) ? esc_attr( 'hidden' ) : esc_attr( '' ) ?> remove-after-initial-save">
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">											
											<label for="start_price"><?php esc_html_e( 'Start Price *', 'adifier' ) ?></label>
											<input type="text" id="start_price" name="start_price" value="" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<label for="reserved_price"><?php esc_html_e( 'Reserved Price', 'adifier' ) ?></label>
											<input type="text" id="reserved_price" name="reserved_price" value="<?php echo !empty( $reserved_price ) ? esc_attr( adifier_price_format_value( $reserved_price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>									
									<?php
									if( count( $currencies ) > 1 ){
										?>
										<div class="col-sm-4">
											<?php adifier_currency_select( $currencies, $currency, adifier_is_allowed_ad_type(1) ? false : true ); ?>
										</div>
										<?php
									}
									?>
								</div>
							<?php endif; ?>

							<?php if( adifier_is_allowed_ad_type(3) && ( $screen == 'new' || $type == '3' )  ): ?>
								<div class="row advert-type-3 <?php echo ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) ) && $type !== '3' ? esc_attr( 'hidden' ) : esc_attr( '' ) ?>">
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '6' ) : esc_attr( '12' ) ?>">
										<div class="form-group">
											<div class="flex-wrap">
												<label for="buy_price"><?php esc_html_e( 'Max Price', 'adifier' ) ?></label>
												<div class="styled-checkbox negotiable-checkbox">
													<input type="checkbox" id="is_negotiable_buy" name="is_negotiable_buy" value="1" <?php checked( 1, $is_negotiable ) ?>>
													<label for="is_negotiable_buy"><?php esc_html_e( 'Is negotiable?', 'adifier' ) ?></label>
												</div>
											</div>
											<input type="text" id="buy_price" name="buy_price" value="<?php echo !empty( $price ) ? esc_attr( adifier_price_format_value( $price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<?php
									if( count( $currencies ) > 1 ){
										?>
										<div class="col-sm-6">
											<?php adifier_currency_select( $currencies, $currency, ( $screen == 'new' && ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) ) ) ? false : true ); ?>
										</div>
										<?php
									}
									?>
								</div>
							<?php endif; ?>							

							<?php if( adifier_is_allowed_ad_type(6) && ( $screen == 'new' || $type == '6' )  ): ?>
								<div class="row advert-type-6 <?php echo ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) || adifier_is_allowed_ad_type(3) ) && $type !== '6' ? esc_attr( 'hidden' ) : esc_attr( '' ) ?>">
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">											
											<div class="flex-wrap">
												<label for="rent_price"><?php esc_html_e( 'Rent Price', 'adifier' ) ?></label>
												<div class="styled-checkbox negotiable-checkbox">
													<input type="checkbox" id="is_negotiable_rent" name="is_negotiable_rent" value="1" <?php checked( 1, $is_negotiable ) ?>>
													<label for="is_negotiable_rent"><?php esc_html_e( 'Is negotiable?', 'adifier' ) ?></label>
												</div>
											</div>
											<input type="text" id="rent_price" name="rent_price" value="<?php echo !empty( $price ) ? esc_attr( adifier_price_format_value( $price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<label for="rent_period"><?php esc_html_e( 'Rent Period *', 'adifier' ) ?></label>
											<div class="styled-select">
												<select name="rent_period" id="rent_period">
													<option value="1" <?php selected( $rent_period, 1 ) ?>><?php esc_html_e( 'Month', 'adifier' ) ?></option>
													<option value="2" <?php selected( $rent_period, 2 ) ?>><?php esc_html_e( 'Week', 'adifier' ) ?></option>
													<option value="3" <?php selected( $rent_period, 3 ) ?>><?php esc_html_e( 'Day', 'adifier' ) ?></option>
													<option value="4" <?php selected( $rent_period, 4 ) ?>><?php esc_html_e( 'Hour', 'adifier' ) ?></option>
												</select>
											</div>
										</div>
									</div>									
									<?php
									if( count( $currencies ) > 1 ){
										?>
										<div class="col-sm-4">
											<?php adifier_currency_select( $currencies, $currency, ( $screen == 'new' && ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) || adifier_is_allowed_ad_type(3) ) ) ? false : true ); ?>
										</div>
										<?php
									}
									?>
								</div>
							<?php endif; ?>

							<?php if( adifier_is_allowed_ad_type(7) && ( $screen == 'new' || $type == '7' )  ): ?>
								<div class="row advert-type-7 <?php echo ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) || adifier_is_allowed_ad_type(3) || adifier_is_allowed_ad_type(6) ) && $type !== '7' ? esc_attr( 'hidden' ) : esc_attr( '' ) ?>">
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<div class="flex-wrap">
												<label for="salary"><?php esc_html_e( 'Salary', 'adifier' ) ?></label>
												<div class="styled-checkbox negotiable-checkbox">
													<input type="checkbox" id="is_negotiable_salary" name="is_negotiable" value="1" <?php checked( 1, $is_negotiable ) ?>>
													<label for="is_negotiable_salary"><?php esc_html_e( 'Is negotiable?', 'adifier' ) ?></label>
												</div>
											</div>
											<input type="text" id="salary" name="salary" value="<?php echo !empty( $price ) ? esc_attr( adifier_price_format_value( $price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<div class="col-sm-<?php echo count( $currencies ) > 1 ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
										<div class="form-group">
											<label for="max_salary"><?php esc_html_e( 'Max Salary', 'adifier' ) ?></label>
											<input type="text" id="max_salary" name="max_salary" value="<?php echo !empty( $start_price ) ? esc_attr( adifier_price_format_value( $start_price, true, $currency ) ) : '' ?>" placeholder="<?php echo esc_attr( adifier_price_format_value( 0, true, $currency ) ) ?>"/>
										</div>
									</div>
									<?php
									if( count( $currencies ) > 1 ){
										?>
										<div class="col-sm-4">
											<?php adifier_currency_select( $currencies, $currency, ( $screen == 'new' && ( adifier_is_allowed_ad_type(1) || adifier_is_allowed_ad_type(2) || adifier_is_allowed_ad_type(3) || adifier_is_allowed_ad_type(6) ) ) ? false : true ); ?>
										</div>
										<?php
									}
									?>
								</div>
							<?php endif; ?>							

							<?php if( adifier_get_option( 'enable_conditions' ) == 'yes' ): ?>
								<div class="form-group">
									<label for="cond"><?php esc_html_e( 'Condition', 'adifier' ) ?></label>
									<div class="styled-select">
										<select name="cond" id="cond">
											<option value="0" <?php selected( $cond, 0 ) ?>><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
											<option value="1" <?php selected( $cond, 1 ) ?>><?php esc_html_e( 'New', 'adifier' ) ?></option>
											<option value="2" <?php selected( $cond, 2 ) ?>><?php esc_html_e( 'Manufacturer Refurbished', 'adifier' ) ?></option>
											<option value="3" <?php selected( $cond, 3 ) ?>><?php esc_html_e( 'Used', 'adifier' ) ?></option>
											<option value="4" <?php selected( $cond, 4 ) ?>><?php esc_html_e( 'For Parts Or Not Working', 'adifier' ) ?></option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<div class="form-group">
								<label for="images"><?php esc_html_e( 'Images', 'adifier' ) ?></label>
								<div class="images-uploader">
									<span class="aficon-cloud-upload"></span>
									<h5><?php esc_html_e( 'Drag & Drop files here', 'adifier' ); ?></h5>
									<p><?php esc_html_e( 'or', 'adifier' ); ?></p>
									<a href="javascript:;" class="uploader-browse af-button"><?php esc_html_e( 'Browse Files', 'adifier' ) ?></a>
									<?php $max_images = adifier_get_option( 'max_images' ); ?>
									<?php $max_image_size = adifier_get_option( 'max_image_size' ); ?>
									<p class="description">
										<?php esc_html_e( 'Prepare images before uploading. Upload images larger than 355px x 250px.', 'adifier' ); ?>
										<?php
										if( !empty( $max_images ) ){
											echo sprintf( esc_html__( ' Max number of images is %s.', 'adifier' ), $max_images );
										}
										if( !empty( $max_image_size ) ){
											echo  sprintf( esc_html__( ' Max image size is %sMB.', 'adifier' ), $max_image_size );
										}
										?>
									</p>									
									<div class="images-uploader-wrap clearfix"></div>
								</div>							
								<div class="images-holder clearfix">
									<?php
									if( !empty( $images ) ){
										foreach( $images as $image ){
											adifier_profile_advert_image( $image, $featured_image );
										}
									}
									?>
								</div>
							</div>

							<div class="form-group">
								<label for="videos">
									<?php esc_html_e( 'Videos', 'adifier' ) ?>
									<?php 
										$max_videos = adifier_get_option( 'max_videos' );
										if( !empty( $max_videos ) ){
											echo sprintf( esc_html__( ' (%s max)', 'adifier' ), $max_videos );
										}

										?>
								</label>
								<?php
								if( !empty( $videos ) ){
									foreach( $videos as $video ){
										?>
										<div class="video-input-wrap">
											<a href="javascript:void(0);" class="remove-video"><i class="aficon-times"></i></a>
											<input type="text" id="videos" name="videos[]" value="<?php echo esc_attr( $video ) ?>" placeholder="<?php esc_attr_e( 'Link to YouTube or Vimeo video', 'adifier' ) ?>"/>		
										</div>
										<?php
									}
								}
								else{
									?>
									<div class="video-input-wrap">
										<a href="javascript:void(0);" class="remove-video"><i class="aficon-times"></i></a>
										<input type="text" id="videos" name="videos[]" value="" placeholder="<?php esc_attr_e( 'Link to YouTube or Vimeo video', 'adifier' ) ?>"/>
									</div>
									<?php
								}
								?>
								<div class="flex-wrap flex-center">
									<span></span>
									<a href="javascript:void(0);" class="another-video"><?php esc_html_e( '+ Add Video', 'adifier' ) ?></a>
								</div>
							</div>

							<div class="form-group no-margin">
								<label for="description" class="label-bottom-margin"><?php esc_html_e( 'Description *', 'adifier' ) ?></label>
								<?php 
								add_filter( 'mce_buttons', 'adifier_remove_buttons_from_tinymce');
								wp_editor( $description, 'description', array( 'media_buttons' => false, 'quicktags' => false ) );
								remove_filter( 'mce_buttons', 'adifier_remove_buttons_from_tinymce');
								?>
							</div>

						</div>
					</div>
				</div>
				<div class="col-sm-5">
					<div class="white-block white-block-extra-padding">
						<div class="white-block-title">
							<h5><?php esc_html_e( 'Ad Category', 'adifier' ) ?></h5>
						</div>
						<div class="white-block-content">
							<?php
							$categories = adifier_get_taxonomy_hierarchy( 'advert-category' );
							if( !empty( $categories ) ){
								?>
								<div class="form-group no-margin">
									<label for="category"><?php esc_html_e( 'Category *', 'adifier' ) ?></label>
									<select name="category" id="category" class="select2-enabled">
										<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
										<?php
										if( !empty($categories) ){
											addifier_hierarchy_select_taxonomy( $categories, 0, $category_ids );
										}
										?>
									</select>
								</div>						
								<?php
							}
							?>
							<div class="category-custom-fields form-group no-margin">
								<?php
								if( !empty( $category_ids ) ){
									Adifier_Custom_Fields_Advert::get_cf( $category_ids, $id );
								}
								?>
							</div>						
						</div>
					</div>	

					<div class="white-block white-block-extra-padding">
						<div class="white-block-title">
							<?php if( $use_google_location !== 'no' || $use_predefined_locations !== 'no' ): ?>
								<h5><?php esc_html_e( 'Ad Location & Contact', 'adifier' ) ?></h5>
							<?php else: ?>
								<h5><?php esc_html_e( 'Ad Contact', 'adifier' ) ?></h5>
							<?php endif ?>
						</div>
						<div class="white-block-content">
							<?php $enable_phone_verification = adifier_get_option( 'enable_phone_verification' ); ?>
							<?php if( $use_google_location !== 'no' || $use_predefined_locations !== 'no' ): ?>
								<div class="form-group <?php echo $enable_phone_verification == 'yes' ? esc_attr( 'no-margin' ) : '' ?>">
									<?php if( $use_google_location == 'yes' || empty( $id ) ): ?>
										<div class="styled-checkbox">
											<input type="checkbox" <?php echo empty( $location['lat'] ) && $user_profile_has_location == true ? esc_attr( 'checked="checked"' ) : '' ?> name="user_address" id="user_address" value="1"/>
											<label for="user_address"><?php esc_html_e( 'Use address set in profile section', 'adifier' ) ?></label>
										</div>
									<?php endif; ?>
									<div class="advert-location-wrap <?php echo ( ( $use_google_location == 'yes' && empty( $location['lat'] ) ) || empty( $id ) ) && $user_profile_has_location == true ? esc_attr( 'hidden reveal-after' ) : '' ?>">
										<?php
										if( $use_predefined_locations == 'yes' ){
											$locations = adifier_get_taxonomy_hierarchy( 'advert-location' );
											if( !empty( $locations ) ){
												?>
												<div class="form-group <?php echo ( $use_google_location == 'no' && !empty( $id ) ) ? esc_attr( '' ) : esc_attr( 'margin-top-15' ) ?>">
													<label for="location_id"><?php esc_html_e( 'Location *', 'adifier' ) ?></label>
													<select name="location_id" id="location_id" class="select2-enabled">
														<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
														<?php
														if( !empty( $locations ) ){
															addifier_hierarchy_select_taxonomy( $locations, 0, $location_ids );
														}
														?>
													</select>
													<p class="description"><?php esc_html_e( 'Select location', 'adifier' ); ?></p>	
												</div>						
												<?php
											}
										}
										?>

										<?php if( $use_google_location == 'yes' ): ?>
											<div class="adifier-map advert-location <?php echo $use_predefined_locations == 'yes' ? '' : 'margin-top-15' ?>">

												<label><?php esc_html_e( 'Precise Location *', 'adifier' ) ?></label>

												<div class="form-group">
													<?php adifier_get_map_autocomplete_html() ?>
													<div class="map-holder"></div>
												</div>

												<input type="hidden" name="lat" value="<?php echo esc_attr( $location['lat'] ) ?>">
												<input type="hidden" name="long" value="<?php echo esc_attr( $location['long'] ) ?>">

												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label for="country"><?php esc_html_e( 'Country', 'adifier' ) ?></label>
															<input type="text" name="country" id="country" value="<?php echo esc_attr( $location['country'] ) ?>" placeholder="<?php esc_attr_e( 'Also populated on place select', 'adifier' ); ?>">
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label for="state"><?php esc_html_e( 'State', 'adifier' ) ?></label>
															<input type="text" name="state" id="state" value="<?php echo esc_attr( $location['state'] ) ?>" placeholder="<?php esc_attr_e( 'Also populated on place select', 'adifier' ); ?>">
														</div>												
													</div>
												</div>

												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label for="city"><?php esc_html_e( 'City', 'adifier' ) ?></label>
															<input type="text" name="city" id="city" value="<?php echo esc_attr( $location['city'] ) ?>" placeholder="<?php esc_attr_e( 'Also populated on place select', 'adifier' ); ?>">
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label for="street"><?php esc_html_e( 'Street', 'adifier' ) ?></label>
															<input type="text" name="street" id="street" value="<?php echo esc_attr( $location['street'] ) ?>" placeholder="<?php esc_attr_e( 'Also populated on place select', 'adifier' ); ?>">
														</div>
													</div>
												</div>
											</div>
										<?php  endif; ?>
									</div>
								</div>
							<?php endif; ?>

							<?php
							if( $enable_phone_verification !== 'yes' ):
								?>
								<div class="form-group no-margin">
									<div class="styled-checkbox">
										<input type="checkbox" <?php echo empty( $phone ) && $user_profile_has_phone == true ? esc_attr( 'checked="checked"' ) : '' ?> name="user_contact" id="user_contact" value="1" />
										<label for="user_contact"><?php esc_html_e( 'Use contact set in profile section', 'adifier' ) ?></label>
									</div>
									<div class="new-contact <?php echo empty( $phone ) && $user_profile_has_phone == true ? esc_attr( 'hidden' ) : '' ?>">
										<label for="phone" class="margin-top-15"><?php esc_html_e( 'Phone', 'adifier' ) ?></label>
										<input type="text" name="phone" id="phone" value="<?php echo esc_attr( $phone ) ?>" placeholder="<?php esc_attr_e( 'Protected from spam', 'adifier' ); ?>">
									</div>
								</div>
								<?php
							endif;
							?>

						</div>
					</div>
					<?php if( $type !== '2' || ( $type == '2' && !adifier_is_expired( $id ) ) ): ?>
						<div class="white-block white-block-extra-padding">
							<div class="white-block-content">
								<div class="ajax-form-result"></div>
								<div class="ajax-form-images hidden">
									<div class="alert-info"><?php esc_html_e( 'Uploading images...', 'adifier' ) ?></div>
								</div>
								<div class="flex-wrap terms-wrap flex-center">
									<div class="flex-left">
										<?php adifier_terms_checkbox( 'submit_terms' ) ?>
									</div>
									<?php if( adifier_is_expired( $id ) ): ?>
										<input type="hidden" name="renew_advert" value="1">
									<?php endif; ?>
									<a href="javascript:void(0);" class="af-button dropzone-uploader submit-ajax-form"><?php adifier_is_expired( $id ) ? esc_html_e( 'Renew Ad', 'adifier' ) : esc_html_e( 'Save Ad', 'adifier' ) ?></a>
								</div>

								<div class="col-sm-3">
					<div class="white-block price-table">
						<div class="pt-title">
							<h5><?php echo esc_html( 'Please Pay' ) ?></h5>
						</div>
						<div class="pt-price">
							<?php echo adifier_price_format( 23 ); ?>
							<?php adifier_tax_included(); ?>
						</div>						
						<div class="pt-content">
							<?php //$account_payment = adifier_get_option( 'account_payment' );
							 //$pack['adverts'] = array('adverts'=>);
							 //adifier_packs_message( 'hybrids', $pack ); ?>
						</div>
						<a href="javascript:void(0);" class="af-button purchase-pack" data-pack="<?php echo esc_attr( $key ) ?>"><?php esc_html_e( 'Purchase Now', 'adifier' ) ?></a>
					</div>
				</div>
							</div>
						</div>
					<?php endif;?>
				</div>
			</div>
		</form>
		<?php
	}
	?>
</div>