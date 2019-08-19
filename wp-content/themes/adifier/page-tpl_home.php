<?php
/*
	Template Name: Home Page
*/
get_header();
the_post();
?>
<main class="clearfix">
	<?php the_content(); ?>
</main>
<?php get_footer(); ?>