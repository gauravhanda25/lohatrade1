<?php
/*
Template Name: Search With Map
*/
get_header();
the_post();

$result_listing = 2;
include( get_theme_file_path( 'includes/search-parts/query.php' ) );
?>

<main class="search-with-map flex-wrap flex-start-v">
	<div class="search-map-form">
		<?php include( get_theme_file_path( 'includes/search-parts/form.php' ) ); ?>
	</div>
	<div class="search-map-results">
		<div class="search-map-results-content">
			<?php include( get_theme_file_path( 'includes/search-parts/results.php' ) ); ?>	
		</div>
	</div>
	<div class="search-map">
	</div>
</main>
<?php get_footer(); ?>