<?php
get_header();
the_post();

do_action( 'adifier_page_header' );
?>

<main>
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<div class="white-block">
					<?php if( has_post_thumbnail() ): ?>
						<div class="white-block-media">
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
				</div>

				<?php comments_template( '', true ); ?>
			</div>
			<div class="col-sm-4">
				<?php get_sidebar('right') ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>