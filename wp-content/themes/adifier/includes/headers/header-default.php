<header class="header-1 sticky-header header-default">
	<div class="container">
		<div class="flex-wrap">
			<a href="<?php echo esc_url( home_url( '/' ) ) ?>" class="logo">
				<h2><?php echo get_bloginfo( 'name' ) ?></h2>
			</a>
			<div class="flex-right">
				<?php include( get_theme_file_path( 'includes/headers/navigation.php' ) ); ?>
				<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
			</div>
		</div>
	</div>
</header>