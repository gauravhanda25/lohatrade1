<?php
/*
Template Name: Page Full Width
*/
get_header();
the_post();

do_action( 'adifier_page_header' );
?>

<main>
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-push-2">

				<?php if( has_post_thumbnail() ): ?>
					<div class="article-media">
						<?php the_post_thumbnail( 'post-thumbnail' ); ?>
					</div>
				<?php endif; ?>

				<div class="white-block">
					<div class="white-block-content">
						<div class="post-content clearfix">
							<?php the_content(); ?>
						</div>
					</div>
				</div>

				<?php 
				if ( comments_open() ){
					comments_template( '', true );
				}
				?>

			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>