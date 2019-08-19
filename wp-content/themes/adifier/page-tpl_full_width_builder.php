<?php
/*
Template Name: Page Full Width - Builder
*/
get_header();
the_post();

do_action( 'adifier_page_header' );
?>

<main>
	<div class="container">
		<?php the_content(); ?>
	</div>
</main>
<?php get_footer(); ?>