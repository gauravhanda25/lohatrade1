<?php if( function_exists('adifier_create_post_types') ): ?>

	<form action="<?php echo adifier_get_search_link() ?>" class="header-search flex-wrap">
		<?php include( get_theme_file_path( 'includes/headers/search-parts/keyword.php' ) ); ?>
		<?php include( get_theme_file_path( 'includes/headers/search-parts/location.php' ) ); ?>
		<?php include( get_theme_file_path( 'includes/headers/search-parts/category.php' ) ); ?>
		<?php include( get_theme_file_path( 'includes/headers/search-parts/submit.php' ) ); ?>
	</form>

<?php endif; ?>