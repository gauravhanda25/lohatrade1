<?php
/*=============================
	DEFAULT BLOG LISTING PAGE
=============================*/
get_header();

global $wp_query;

/* 
* set to adverts variable since we use that on results listing and since we are on taxononmy page where query is already performed
* same goes for pagination
*/
$adverts = $wp_query;

$page_links_total =  $wp_query->max_num_pages;
$pagination = paginate_links( 
	array(
		'end_size' => 2,
		'mid_size' => 2,
		'prev_next' => false,
	)
);

$result_listing = 3;
include( get_theme_file_path( 'includes/search-parts/query.php' ) );
include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );

if( is_tax( 'advert-category' ) ){
	$category = get_queried_object_id();
}
else if( is_tax( 'advert-location' ) ){
	$location_id = get_queried_object_id();
}

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

<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>