<?php if( adifier_get_option( 'show_breadcrumbs' ) == 'yes' && adifier_get_option( 'header_search' ) == 'yes' ): ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-10 col-sm-push-1">
				<?php include( get_theme_file_path( 'includes/headers/search-form.php' ) ) ?>
			</div>
		</div>
	</div>
<?php endif; ?>