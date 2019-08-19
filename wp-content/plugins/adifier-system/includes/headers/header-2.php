<header class="header-2 top-header">
	<div class="container">
		<div class="flex-wrap">
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
			</div>			
			<?php include( get_theme_file_path( 'includes/headers/logo.php' ) ); ?>
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
			</div>
			<?php
			$header_banner = adifier_get_option( 'header_banner' );
			if( !empty( $header_banner ) ){
				?>
				<div class="header-banner">
					<?php echo $header_banner; ?>
				</div>
				<?php
			}
			?>
			<?php include( get_theme_file_path( 'includes/headers/special.php' ) ); ?>
		</div>
	</div>
</header>
<header class="header-2 sticky-header">
	<div class="container">
		<div class="flex-wrap">
			<?php include( get_theme_file_path( 'includes/headers/navigation.php' ) ); ?>
			<?php include( get_theme_file_path( 'includes/headers/special.php' ) ); ?>
		</div>
	</div>
</header>