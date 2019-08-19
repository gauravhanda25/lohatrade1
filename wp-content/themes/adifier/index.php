<?php
/*=============================
	DEFAULT BLOG LISTING PAGE
=============================*/
get_header();

global $wp_query;

$page_links_total =  $wp_query->max_num_pages;
$pagination = paginate_links( 
	array(
		'end_size' => 2,
		'mid_size' => 2,
		'prev_next' => false,
	)
);

if( !is_front_page() ){
	do_action( 'adifier_page_header' );
}

$listing_type = adifier_get_option( 'listing_type' );
if( !empty( $_GET['listing_type'] ) ){
	$listing_type = $_GET['listing_type'];
}
$counter = 0;

switch( $listing_type ){
	case '1'	: $column_width = 8; $items_in_row = 1; break;
	case '2'	: $column_width = '8 col-sm-push-2'; $items_in_row = 1; break;
	case '3'	: $column_width = 8; $items_in_row = 2; break;
	case '4'	: $column_width = 12; $items_in_row = 2; break;
	case '5'	: $column_width = 12; $items_in_row = 3; break;
}

?>
<main>
	<div class="container">
		<div class="row">

			<div class="col-sm-<?php echo esc_attr( $column_width ) ?>">
				<?php if( have_posts() ){
					?>
					<div class="row">
						<?php
						while( have_posts() ){
							the_post();
							if( $counter == $items_in_row ){
								echo '</div><div class="row">';
								$counter = 0;
							}
							$counter++;
							?>
							<div class="col-sm-<?php echo esc_attr( 12 / $items_in_row ) ?>">
								<article id="post-<?php the_ID(); ?>" <?php post_class('hover-shadow white-block'); ?>>

									<?php if( has_post_thumbnail() ): ?>
										<a href="<?php the_permalink() ?>" class="article-media">
											<?php the_post_thumbnail( 'adifier-single-slider' ) ?>
										</a>
									<?php endif; ?>

									<div class="white-block-content">
										<h5>
											<a href="<?php the_permalink() ?>" class="text-overflow" title="<?php echo esc_attr( get_the_title() ) ?>">
												<?php if( is_sticky() ): ?>
													<i class="aficon-thumbtack"></i>
												<?php endif; ?>
												<?php the_title(); ?>
											</a>
										</h5>

										<div class="article-excerpt">
											<?php the_excerpt() ?>
										</div>
											
										<div class="flex-wrap">
											<a href="<?php the_permalink() ?>" class="af-button">
												<?php  esc_html_e( 'Read More', 'adifier' ); ?>
											</a>
											<div class="top-advert-meta">
												<i class="aficon-dot-circle-o"></i>
												<?php echo adifier_the_category( 1 ); ?>
											</div>
										</div>

									</div>

								</article>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				else{
				?>
					<div class="white-block no-advert-found">
						<div class="white-block-content text-center">
							<i class="aficon-question-circle"></i>
							<h5><?php esc_html_e( 'No posts found matched your criteria', 'adifier' ) ?></h5>
						</div>
					</div>
				<?php
				}
				?>

				<?php
				if( !empty( $pagination ) ){
					?>
					<div class="pagination">
						<?php echo wp_kses_post( $pagination ) ?>
					</div>
					<?php
				}
				?>

			</div>

			<?php if( in_array( $listing_type, array( '1', '3' ) ) ): ?>
				<div class="col-sm-4">
					<?php get_sidebar(); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</main>


<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>