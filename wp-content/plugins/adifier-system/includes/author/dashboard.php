<div class="author-panel dashboard-wrap">
	<div class="row">
		<div class="col-sm-3">
			<div class="dashboard-box dashboard-box-1">
				<div class="white-block-content">
					<img src="<?php echo esc_url( get_theme_file_uri( '/images/dashboard/dash-1.png' ) ) ?>">
					<div class="dashboard-body">
						<?php
						$account_payment = adifier_get_option( 'account_payment' );
						if( $account_payment == 'packages' ){
							echo '<h5>'.esc_html__( 'Ads Remaining', 'adifier' ).'</h5><h5 class="dash-value">'.Adifier_Order::get_user_package_adverts( get_current_user_id() ).'</h5>';
						}
						else if( $account_payment == 'subscriptions' ){
							$subscribe_time = Adifier_Order::get_user_package_subscription( get_current_user_id() );
							echo '<h5>'.esc_html__( 'Subscription Expires', 'adifier' ).'</h5><h5 class="dash-value">'.date_i18n( get_option( 'date_format' ), $subscribe_time == 0 ? current_time( 'timestamp' ) : $subscribe_time ).'</h5>';
						}
						else if ( $account_payment == 'hybrids' ){
							$subscribe_time = Adifier_Order::get_user_package_subscription( get_current_user_id() );
							if( $subscribe_time > current_time( 'timestamp' ) ){
								$adverts_text = Adifier_Order::get_user_package_adverts( get_current_user_id() ).' '.esc_html__( 'Ads Remaining', 'adifier' );
							}
							else{
								$adverts_text = esc_html__( 'Subscription Expired', 'adifier' );
							}
							echo '<h5>'.$adverts_text.'</h5><h5 class="dash-value">'.date_i18n( get_option( 'date_format' ), $subscribe_time == 0 ? current_time( 'timestamp' ) : $subscribe_time ).'</h5>';
						}
						else{
							echo '<h5>'.esc_html__( 'Free Submission', 'adifier' ).'</h5><h5 class="dash-value infinity"><i class="aficon-repeat"></i></h5>';
						}
						?>
					</div>

					<h5 class="dash-footer">
						<?php esc_html_e( 'Account submission type', 'adifier' ) ?>
					</h5>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="dashboard-box dashboard-box-2">
				<div class="white-block-content">
					<img src="<?php echo esc_url( get_theme_file_uri( '/images/dashboard/dash-2.png' ) ) ?>">
					<div class="dashboard-body">
						<h5><?php esc_html_e( 'Submitted Ads', 'adifier' ) ?></h5>
						<h5 class="dash-value"><?php echo count_user_posts( get_current_user_id() , 'advert' ) ?></h5>
					</div>
					<h5 class="dash-footer">
						<?php esc_html_e( 'Number of submitted ads', 'adifier' ) ?>
					</h5>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="dashboard-box dashboard-box-3">
				<div class="white-block-content">
					<img src="<?php echo esc_url( get_theme_file_uri( '/images/dashboard/dash-3.png' ) ) ?>">
					<div class="dashboard-body">
						<h5><?php esc_html_e( 'Your Rating', 'adifier' ) ?></h5>
						<h5 class="dash-value"><?php echo number_format( (float)get_user_meta( get_current_user_id(), 'af_rating_average', true ), 2 ) ?> / 5.00</h5>
					</div>
					<h5 class="dash-footer">
						<?php esc_html_e( 'Based on all your ads', 'adifier' ) ?>
					</h5>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="dashboard-box dashboard-box-4">
				<div class="white-block-content">
					<img src="<?php echo esc_url( get_theme_file_uri( '/images/dashboard/dash-4.png' ) ) ?>">
					<div class="dashboard-body">
						<h5><?php esc_html_e( 'Favorite Ads', 'adifier' ) ?></h5>
						<h5 class="dash-value"><?php echo adifier_count_favorited() ?></h5>
					</div>
					<h5 class="dash-footer">
						<?php esc_html_e( 'Number of ads you like', 'adifier' ) ?>
					</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="white-block dasboard-chart">
		<div class="white-block-title">
			<div class="flex-wrap flex-center">
				<h5><?php esc_html_e( 'Visits Chart', 'adifier' ) ?></h5>
				<div class="styled-select margin-above">
					<select class="advert-chart" disabled="disabled">
						<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
						<?php
						$adverts = adifier_id_title_ad_chart();
						if( !empty( $adverts ) ){
							foreach( $adverts as $ID => $post_title ){
								?>
								<option value="<?php echo esc_attr( $ID )?>"><?php echo esc_html( $post_title ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="white-block-content">
			<div class="alert-info to-remove no-margin"><?php esc_html_e( 'Select an ad to show data', 'adifier' ) ?></div>
			<canvas id="dashboard-chart"></canvas>
			<div class="alert-info hidden no-data no-margin"><?php esc_html_e( 'No data to show for this ad yet', 'adifier' ) ?></div>
		</div>
	</div>
</div>