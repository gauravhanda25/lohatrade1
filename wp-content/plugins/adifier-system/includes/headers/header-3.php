<?php if( is_front_page() || ( !empty( $_GET['header_style'] ) && $_GET['header_style'] == 'header-3' ) ): ?>
<header class="header-2 header-3 sticky-header">
	<div class="container">
		<div class="flex-wrap">
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
			</div>
			<?php adifier_logo_html_wrapper( 'dark_nav_logo', true ); ?>
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
			</div>
			<?php include( get_theme_file_path( 'includes/headers/navigation.php' ) ); ?>
			<?php include( get_theme_file_path( 'includes/headers/special.php' ) ); ?>
		</div>
	</div>
</header>
<header class="header-1 sticky-header show-on-414">
	<div class="container">
		<div class="flex-wrap">
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
			</div>
			<?php include( get_theme_file_path( 'includes/headers/logo.php' ) ); ?>
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
			</div>
		</div>
	</div>
</header>
<?php else: ?>
	<?php include( get_theme_file_path( 'includes/headers/header-1.php' ) ); ?>
<?php endif; ?>