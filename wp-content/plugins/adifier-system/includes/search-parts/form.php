<div class="white-block filters-toggle">
	<div class="white-block-content">
		<h6>
			<a href="javascript:void(0);" class="toggle-filters"><?php esc_html_e( 'Toggle Filters', 'adifier' ) ?></a>
		</h6>
	</div>
</div>
<form method="post" class="search-form" action="<?php echo adifier_get_search_link(); ?>">
	<div class="white-block no-margin">
		<div class="white-block-title">
			<h5><?php esc_html_e( 'Filter Ads', 'adifier' ) ?></h5>
			<a href="javascript:void(0);" class="reset-search" title="<?php esc_attr_e( 'Reset Search', 'adifier' ) ?>"><i class="aficon-undo"></i></a>
		</div>

		<div class="white-block-content">
			<div class="form-group">
				<label for="keyword"><?php esc_html_e( 'Keyword', 'adifier' ) ?></label>
				<input type="text" class="keyword" name="keyword" id="keyword" value="<?php echo esc_attr( $keyword ) ?>" placeholder="<?php esc_html_e( 'Search for...', 'adifier' ) ?>">
			</div>

			<div class="form-group">
				<?php Adifier_Custom_Fields_Search::taxonomy_filter( esc_html__( 'Category', 'adifier' ), 'category', 'advert-category', $category ); ?>
			</div>

			<div class="category-custom-fields">
				<?php
				if( !empty( $category ) ){
					Adifier_Custom_Fields_Search::get_cf_filter( $category );
				}
				?>
			</div>

			<?php
			$use_google_location = adifier_get_option( 'use_google_location' );
			$use_predefined_locations = adifier_get_option( 'use_predefined_locations' );
			$map_source = adifier_get_option( 'map_source' );
			?>
			<?php if( $use_google_location !== 'no' || $use_predefined_locations !== 'no' ): ?>
				<div class="form-group">
					<?php
					$location_search = adifier_get_option( 'location_search' );
					?>
					<?php if( $location_search == 'geo' ): ?>
						<label for="location"><?php esc_html_e( 'Location', 'adifier' ) ?></label>
						<input type="text" class="location" name="location" id="location" value="<?php echo esc_attr( $location ) ?>" placeholder="<?php esc_attr_e( 'Start typing...', 'adifier' ) ?>">
						<?php if( $map_source == 'google' ): ?>
							<input type="hidden" name="latitude" class="latitude" value="<?php echo esc_attr( $latitude ) ?>">
							<input type="hidden" name="longitude" class="longitude" value="<?php echo esc_attr( $longitude ) ?>">
						<?php endif; ?>

						<div class="radius-slider <?php echo ( !empty( $location ) || $map_source == 'mapbox'  ) ? '' : 'hidden' ?>">
							<label class="margin-above label-bottom-margin"><?php esc_html_e( 'Radius', 'adifier' ) ?></label>
							<div class="slider-wrap">
								<div class="filter-slider" data-min="0" data-max="<?php echo esc_attr( $radius_max ) ?>" data-default="<?php echo esc_attr( $radius_max ) ?>" data-sufix="<?php echo adifier_get_option( 'radius_units' ) ?>"></div>
								<input type="hidden" name="radius" class="radius" value="<?php echo esc_attr( $radius ) ?>">
								<div class="slider-value"></div>
							</div>
						</div>
					<?php else: ?>
						<?php Adifier_Custom_Fields_Search::taxonomy_filter( esc_html__( 'Location', 'adifier' ), 'location_id', 'advert-location', $location_id ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			$max_price = Adifier_Custom_Fields_Search::get_max_price();
			if( !empty( $max_price ) ):
				$currencies = adifier_get_currencies_raw_list();
				$price_slider_data = '';
				if( count( $currencies ) <= 1 ){
					$price_slider_data = 'data-decimal="'.esc_attr( adifier_get_option( 'decimal_separator' ) ).'" data-thousands="'.esc_attr( adifier_get_option( 'show_decimals' ) ).'" data-thousands="'.esc_attr( adifier_get_option( 'thousands_separator' ) ).'." '.$currency_location == 'front' ? 'data-prefix="'.esc_attr( $currency_symbol ).'"' : 'data-sufix="'.esc_attr( $currency_symbol ).'"';
				}
				?>
				<div class="form-group">
					<label class="label-bottom-margin"><?php esc_html_e( 'Price', 'adifier' ) ?></label>
					<?php if( adifier_get_option( 'price_filter_type' ) == 'slider' ): ?>
						<div class="slider-wrap slider-range">
							<div class="filter-slider price-filter-slider" data-range="true" data-min="0" data-max="<?php echo esc_attr( (int)$max_price ) ?>" <?php echo $price_slider_data ?>></div>
							<input type="hidden" name="price" class="price" value="<?php echo esc_attr( $price ) ?>">
							<div class="slider-value"></div>
						</div>
					<?php else: ?>
						<div class="date-range">
							<input type="text" name="price[0]" value="<?php echo !empty( $price[0] ) ? esc_attr( $price[0] ) : '' ?>" placeholder="<?php esc_attr_e( 'min', 'adifier' ) ?>" />
							<input type="text" name="price[1]" value="<?php echo !empty( $price[1] ) ? esc_attr( $price[1] ) : '' ?>" placeholder="<?php esc_attr_e( 'max', 'adifier' ) ?>" />
						</div>
					<?php endif; ?>
				</div>
				<div class="form-group">
					<?php
					if( count( $currencies ) > 1 ){
						adifier_currency_select( $currencies, $currency, true );
					}
					?>
				</div>
			<?php endif; ?>

			<?php if( !adifier_is_single_ad_type() ): ?>
				<div class="form-group">
					<label class="label-bottom-margin"><?php esc_html_e( 'Type', 'adifier' ) ?></label>
					<ul class="list-unstyled">
						<li>
							<div class="styled-radio">
								<input type="radio" name="type" value="" id="type-0" <?php echo  $type == '' ? 'checked="checked"' : '' ?>>
								<label for="type-0"><?php esc_html_e( 'All', 'adifier' ) ?></label>
							</div>
						</li>
						<?php if( adifier_is_allowed_ad_type(1) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="1" id="type-1" <?php echo  $type == '1' ? 'checked="checked"' : '' ?>>
									<label for="type-1"><?php esc_html_e( 'Sell', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(2) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="2" id="type-2" <?php echo  $type == '2' ? 'checked="checked"' : '' ?>>
									<label for="type-2"><?php esc_html_e( 'Auction', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(3) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="3" id="type-3" <?php echo  $type == '3' ? 'checked="checked"' : '' ?>>
									<label for="type-3"><?php esc_html_e( 'Buy', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(4) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="4" id="type-4" <?php echo  $type == '4' ? 'checked="checked"' : '' ?>>
									<label for="type-4"><?php esc_html_e( 'Exchange', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(5) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="5" id="type-5" <?php echo  $type == '5' ? 'checked="checked"' : '' ?>>
									<label for="type-5"><?php esc_html_e( 'Gift', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(6) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="6" id="type-6" <?php echo  $type == '6' ? 'checked="checked"' : '' ?>>
									<label for="type-6"><?php esc_html_e( 'Rent', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(7) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="7" id="type-7" <?php echo  $type == '7' ? 'checked="checked"' : '' ?>>
									<label for="type-7"><?php esc_html_e( 'Job - Offer', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
						<?php if( adifier_is_allowed_ad_type(8) ): ?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="type" value="8" id="type-8" <?php echo  $type == '8' ? 'checked="checked"' : '' ?>>
									<label for="type-8"><?php esc_html_e( 'Job - Wanted', 'adifier' ) ?></label>
								</div>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if( adifier_get_option( 'enable_conditions' ) == 'yes' ): ?>
				<div class="form-group">
					<label class="label-bottom-margin"><?php esc_html_e( 'Condition', 'adifier' ) ?></label>
					<ul class="list-unstyled">
						<li>
							<div class="styled-radio">
								<input type="radio" name="cond" value="" id="cond-0" <?php echo  $cond == '' ? 'checked="checked"' : '' ?>>
								<label for="cond-0"><?php esc_html_e( 'All', 'adifier' ) ?></label>
							</div>
						</li>
						<li>
							<div class="styled-radio">
								<input type="radio" name="cond" value="1" id="cond-1" <?php echo  $cond == '1' ? 'checked="checked"' : '' ?>>
								<label for="cond-1"><?php esc_html_e( 'New', 'adifier' ) ?></label>
							</div>
						</li>
						<li>
							<div class="styled-radio">
								<input type="radio" name="cond" value="2" id="cond-2" <?php echo  $cond == '2' ? 'checked="checked"' : '' ?>>
								<label for="cond-2"><?php esc_html_e( 'Manufacturer Refurbished', 'adifier' ) ?></label>
							</div>
						</li>
						<li>
							<div class="styled-radio">
								<input type="radio" name="cond" value="3" id="cond-3" <?php echo  $cond == '3' ? 'checked="checked"' : '' ?>>
								<label for="cond-3"><?php esc_html_e( 'Used', 'adifier' ) ?></label>
							</div>
						</li>
						<li>
							<div class="styled-radio">
								<input type="radio" name="cond" value="4" id="cond-4" <?php echo  $cond == '4' ? 'checked="checked"' : '' ?>>
								<label for="cond-4"><?php esc_html_e( 'For Parts Or Not Working', 'adifier' ) ?></label>
							</div>
						</li>
					</ul>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<div class="styled-checkbox">
					<input type="checkbox" name="image-only" value="1" id="image-only" <?php echo  $image_only ? 'checked="checked"' : '' ?>>
					<label for="image-only"><?php esc_html_e( 'Show ads with image(s) only', 'adifier' ) ?></label>
				</div>
				<?php if( !empty( str_replace( '|', '', adifier_get_option( 'promo_urgent' ) ) ) ):  ?>
					<div class="styled-checkbox">
						<input type="checkbox" name="urgent-only" value="1" id="urgent-only" <?php echo  $urgent_only ? 'checked="checked"' : '' ?>>
						<label for="urgent-only"><?php esc_html_e( 'Show only urgent ads', 'adifier' ) ?></label>
					</div>
				<?php endif; ?>
			</div>
			<div class="submit-search-form">
				<a href="javascript:void(0);" class="af-button filter-adverts"><?php esc_html_e( 'Apply Filters', 'adifier' ) ?></a>
			</div>
		</div>
	</div>
</form>