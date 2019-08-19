<?php
/*
Template Name: Search
*/

get_header();
the_post();

$result_listing = 3;
include( get_theme_file_path( 'includes/search-parts/query.php' ) );
include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );
?>

<main>
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<?php include( get_theme_file_path( 'includes/search-parts/form.php' ) ); ?>
			</div>
			<div class="col-sm-9">
				<?php include( get_theme_file_path( 'includes/search-parts/results.php' ) ); ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>