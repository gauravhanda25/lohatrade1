<?php
/*=============================
	DEFAULT SINGLE
=============================*/
get_header();
the_post();

do_action( 'adifier_page_header' );

$post_pages = wp_link_pages( 
	array(
		'before' => '',
		'after' => '',
		'link_before'      => '<span>',
		'link_after'       => '</span>',
		'next_or_number'   => 'number',
		'nextpagelink'     => esc_html__( '&raquo;', 'adifier' ),
		'previouspagelink' => esc_html__( '&laquo;', 'adifier' ),			
		'separator'        => ' ',
		'echo'			   => 0
	) 
);

$has_sidebar = is_active_sidebar( 'single-blog' );

?>

<main>
	<div class="container">
		<div class="row">
			<div class="col-sm-<?php echo $has_sidebar ? esc_attr( '8' ) : esc_attr( '8 col-sm-push-2' ) ?>">
				<div class="white-block">
					<?php if( has_post_thumbnail() ): ?>
						<div class="white-block-media">
							<?php the_post_thumbnail(); ?>
						</div>
					<?php endif; ?>
					<div class="white-block-content">
						<h1 class="blog-item-title h4-size"><?php the_title() ?></h1>
						<div class="post-content clearfix">
							<?php the_content(); ?>
						</div>
						<ul class="list-inline list-unstyled single-meta top-advert-meta">
							<li>
								<i class="aficon-user-alt"></i>
								<?php the_author(); ?>
							</li>
							<li>
								<i class="aficon-alarm-clock"></i>
								<?php echo get_the_time( get_option( 'date_format' ) ).esc_html__( ' at ', 'adifier' ).get_the_time( get_option( 'time_format' ) ); ?>
							</li>
							<li>
								<i class="aficon-dot-circle-o"></i>
								<?php echo adifier_the_category( 1 ); ?>
							</li>
						</ul>						
					</div>
				</div>
				<?php if( !empty( $post_pages ) ): ?>	
					<div class="pagination">
						<?php echo wp_kses_post( $post_pages ); ?>
					</div>
				<?php endif; ?>

				<?php
				if( has_tag() ):
				?>
					<div class="white-block">
						<div class="white-block-content">
							<div class="tag-section">
								<i class="icon-tag"></i> <?php echo adifier_the_tags(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php comments_template( '', true ) ?>				
			</div>

			<?php if( $has_sidebar ): ?>
				<div class="col-sm-4">
					<?php dynamic_sidebar('single-blog'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>