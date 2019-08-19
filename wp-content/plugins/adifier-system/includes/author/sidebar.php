<div class="author-sidebar">
	<div>
		<div class="user-details text-center">
			<?php echo get_avatar( $author->ID, 60 ); ?>
			<div class="author-details">
				<h5><?php echo adifier_author_name( $author )  ?></h5>
				<a href="<?php echo esc_url( add_query_arg( 'preview', '1', $author_url ) ) ?>" target="_blank"><?php esc_html_e( 'View Profile', 'adifier' ) ?></a>
			</div>
			<div class="text-center user-details-list">
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'profile', $author_url ) ) ?>">
					<i class="aficon-cog"></i>
				</a>
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'messages', $author_url ) ) ?>">
					<i class="aficon-messages"></i>
					<div class="messages-unread-count">
						<?php echo Adifier_Messages::has_unread_messages(); ?>
					</div>
				</a>
				<a href="<?php echo esc_url( wp_logout_url( home_url('/') ) ) ?>">
					<i class="aficon-logout"></i>
				</a>
			</div>
		</div>
		<ul class="list-unstyled author-sidebar-list">
			<li class="author-sidebar-title">
				<?php esc_html_e( 'main', 'adifier' ) ?>
			</li>
			<li class="<?php echo empty( $screen ) ?  esc_attr( 'active' ) : esc_attr( '' ) ?>">
				<a href="<?php echo esc_url( $author_url ) ?>">
					<i class="aficon-tachometer-alt"></i>
					<span><?php esc_html_e( 'Dashboard', 'adifier' ) ?></span>
				</a>
			</li>
			<li class="author-sidebar-title">
				<?php esc_html_e( 'ads', 'adifier' ) ?>
			</li>
			<li class="<?php echo in_array( $screen, array( 'ads', 'new', 'edit' ) ) ?  esc_attr( 'active' ) : esc_attr( '' ) ?>">
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'ads', $author_url ) ) ?>">
					<i class="aficon-clone"></i>
					<span><?php esc_html_e( 'Your Ads', 'adifier' ) ?></span>
				</a>
			</li>
			<li class="<?php echo  $screen == 'favorites' ? esc_attr( 'active' ) : esc_attr( '' ) ?>">
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'favorites', $author_url ) ) ?>">
					<i class="aficon-heart-o"></i>
					<span><?php esc_html_e( 'Favorite Ads', 'adifier' ) ?></span>
				</a>
			</li>
			<?php if( adifier_is_allowed_ad_type(2) ): ?>
				<li class="<?php echo  $screen == 'auctions' ? esc_attr( 'active' ) : esc_attr( '' ) ?>">
					<a href="<?php echo esc_url( add_query_arg( 'screen', 'auctions', $author_url ) ) ?>">
						<i class="aficon-stopwatch"></i>
						<span><?php esc_html_e( 'Auctions', 'adifier' ) ?></span>
					</a>
				</li>
			<?php endif; ?>
			<li class="author-sidebar-title">
				<?php esc_html_e( 'feedback', 'adifier' ) ?>
			</li>
			<li class="<?php echo  $screen == 'reviews' ? esc_attr( 'active' ) : esc_attr( '' ) ?>">
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'reviews', $author_url ) ) ?>">
					<i class="aficon-star-o"></i>
					<span><?php esc_html_e( 'Reviews', 'adifier' ) ?></span>
				</a>
			</li>
			<li class="author-sidebar-title">
				<?php esc_html_e( 'transactions', 'adifier' ) ?>
			</li>
			<?php 
				$account_payment = adifier_get_option( 'account_payment' );
				if( in_array( $account_payment, array( 'packages', 'subscriptions', 'hybrids' ) ) ): ?>
				<li class="<?php echo  $screen == 'acc_pay' ? esc_attr( 'active' ) : esc_attr( '' ) ?>">
					<a href="<?php echo esc_url( add_query_arg( 'screen', 'acc_pay', $author_url ) ) ?>">
						<i class="aficon-plus-circle"></i>
						<span><?php $account_payment == 'packages' ? esc_html_e( 'Packages', 'adifier' ) : esc_html_e( 'Subscription', 'adifier' ) ?></span>
					</a>
				</li>
			<?php endif; ?>
			<li class="<?php echo  $screen == 'invoices' ? esc_attr( 'active' ) : esc_attr( '' ) ?>">
				<a href="<?php echo esc_url( add_query_arg( 'screen', 'invoices', $author_url ) ) ?>">
					<i class="aficon-list-alt"></i>
					<span><?php esc_html_e( 'Invoices', 'adifier' ) ?></span>
				</a>
			</li>
			<?php
			$deactivate_account = adifier_get_option( 'deactivate_account' );
			if( $deactivate_account == 'yes' ):
			?>
			<li class="author-sidebar-title">
				<?php esc_html_e( 'account', 'adifier' ) ?>
			</li>
			<li>
				<a href="javascript:;" class="delete-acc" data-confirm="<?php esc_attr_e( 'Are you sure that you want to delete your account and everything associated with it?', 'adifier' ) ?>" data-url="<?php echo esc_url( add_query_arg( 'screen', 'delete-acc', $author_url ) ) ?>">
					<i class="aficon-times-octagon"></i>
					<span><?php esc_html_e( 'Delete Account', 'adifier' ) ?></span>
				</a>
			</li>			
			<?php endif; ?>
		</ul>
	</div>
</div>