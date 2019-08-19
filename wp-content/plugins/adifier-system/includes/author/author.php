<?php
get_header();
$author = adifier_get_author();
if( is_user_logged_in() ){
	$author_url = get_author_posts_url( $author->ID );
	if( adifier_is_own_account( $author->ID ) ){
		$screen = isset( $_GET['screen'] ) ? $_GET['screen'] : '';
		?>
		<header class="sticky-header author-header">
			<div class="flex-wrap">
				<a href="javascript:void(0);" class="small-sidebar-open" data-target=".author-sidebar">
					<i class="aficon-align-justify"></i>
				</a>
				<a href="<?php echo esc_url( home_url('/') ) ?>" class="account-btn header-home-icon">
					<i class="aficon-home"></i>
				</a>
				<div class="show-on-414">
					<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
				</div>					
				<?php include( get_theme_file_path( 'includes/headers/navigation.php' ) ) ?>
				<?php include( get_theme_file_path( 'includes/headers/special.php' ) ) ?>
			</div>
		</header>
		<div class="flex-wrap">
			<?php include( get_theme_file_path( 'includes/author/sidebar.php' ) ) ?>
			<main>

				<?php
				switch( $screen ){
					case 'profile' 		:  include( get_theme_file_path('includes/author/profile.php') ); break;
					case 'ads' 			:  include( get_theme_file_path('includes/author/adverts.php') ); break;
					case 'new' 			:  include( get_theme_file_path('includes/author/advert-form.php') ); break;
					case 'edit' 		:  include( get_theme_file_path('includes/author/advert-form.php') ); break;
					case 'messages' 	:  include( get_theme_file_path('includes/author/messages.php') ); break;
					case 'reviews' 		:  include( get_theme_file_path('includes/author/reviews.php') ); break;
					case 'favorites' 	:  include( get_theme_file_path('includes/author/adverts.php') ); break;
					case 'auctions' 	:  include( get_theme_file_path('includes/author/adverts.php') ); break;
					case 'acc_pay' 		:  include( get_theme_file_path('includes/author/account_payment.php') ); break;
					case 'invoices' 	:  include( get_theme_file_path('includes/author/invoices.php') ); break;
					default 			:  include( get_theme_file_path('includes/author/dashboard.php') ); break;
				}				
				?>
			</main>
		</div>
		<?php
	}
	else{
		include( get_theme_file_path('includes/author/public.php') );
	}
}
else{
	include( get_theme_file_path('includes/author/public.php') );
}

get_footer();
?>