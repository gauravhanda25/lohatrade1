<?php 
get_header();

do_action( 'adifier_page_header' );
?>
<main>
	<div class="container">
		<div class="white-block big-no">
			<div class="white-block-content">
				<i class="aficon-question-circle"></i>
				<h2><?php esc_html_e( 'Sorry This Page Isn\'t Available', 'adifier'); ?></h2>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>