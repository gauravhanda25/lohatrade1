<div class="special-nav flex-wrap">
	<a href="javascript:void(0);" class="account-btn small-sidebar-open search-trigger show-on-414" data-target=".search-sidebar">
		<i class="aficon-search"></i>
	</a>	
	<?php if( function_exists('adifier_create_post_types') ): ?>
		<a href="<?php echo is_user_logged_in() ? esc_url( get_author_posts_url( get_current_user_id() ) ) : esc_attr('#') ?>" <?php echo !is_user_logged_in() ? 'data-toggle="modal" data-target="#login"' : esc_attr( '' ) ?> class="account-btn header-user-icon" title="<?php is_user_logged_in() ? esc_attr_e( 'Visit Account', 'adifier' ) : esc_attr_e( 'Login / Register', 'adifier' ) ?>">
			<i class="aficon-<?php echo is_user_logged_in() ? 'user' : 'login' ?>"></i>
		</a>
		<?php if( is_user_logged_in() ): ?>
			<a href="<?php echo esc_url( add_query_arg( 'screen', 'messages', get_author_posts_url( get_current_user_id() ) ) ) ?>" class="account-btn header-messages-icon" title="<?php esc_html_e( 'Messages', 'adifier' ) ?>">
				<i class="aficon-messages"></i>
				<div class="messages-unread-count"></div>
			</a>
		<?php endif; ?>
		<?php if( is_singular( 'advert' ) ): ?>
			<a href="javascript:void(0)" class="account-btn scroll-to show-on-414" data-target=".contact-scroll-details" title="<?php esc_attr_e( 'Call / Message', 'adifier' ) ?>">
				<i class="aficon-ad-contact"></i>
			</a>
			<div class="show-on-414">
				<?php adifier_get_favorites_html() ?>
			</div>
		<?php endif; ?>			
		<?php if( adifier_get_option( 'enable_compare' ) == 'yes' ): ?>
			<a href="javascript:void(0);" class="account-btn compare-open" title="<?php esc_attr_e( 'Compare', 'adifier' ) ?>">
				<i class="aficon-repeat"></i>
			</a>
		<?php endif; ?>
		<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
	<?php endif; ?>
	<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
</div>