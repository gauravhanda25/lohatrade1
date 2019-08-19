<?php
extract( shortcode_atts( array(
	'post_ids' 			=> '',
	'post_number' 		=> '4',
	'slider' 			=> 'no',
	'visible_items' 	=> '',
	'autoplay_speed' 	=> '',
	'double_row' 		=> '',
	'items_in_row' 		=> '3'
), $atts ) );

$args = array(
	'post_type'				=> 'post',
	'post_status'			=> 'publish',
	'ignore_sticky_posts'	=> true
);

if( !empty( $post_ids ) ){
	$args['post__in'] 	= explode( ',', $post_ids );
	$args['orderby'] 	= 'post__in';
}
elseif( !empty( $post_number ) ){
	$args['posts_per_page'] = $post_number;
}

$posts = new WP_Query( $args );
if( $posts->have_posts() ){
	$max = $double_row == 'yes' ? 2 : 1;
	$counter = 0;
	?>
	<div class="adverts-list element-articles-wrap clearfix <?php echo  $slider == 'yes' ? esc_attr( 'adverts-slider owl-carousel' ) : esc_attr( 'af-items-'.$items_in_row ) ?>" data-visibleitems="<?php echo esc_attr( $visible_items ) ?>"  data-autoplay="<?php echo esc_attr( $autoplay_speed ) ?>">
		<div class="af-item-wrap">
		<?php
		while( $posts->have_posts() ){
			$posts->the_post();
			if( $max == $counter ){
				?>
				</div><div class="af-item-wrap">
				<?php
				$counter = 0;
			}
			$counter++;
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('hover-shadow white-block blogs-element'); ?>>					

				<?php if( has_post_thumbnail() ): ?>
					<a href="<?php the_permalink() ?>" class="article-media">
						<?php the_post_thumbnail( 'adifier-grid' ) ?>
					</a>
				<?php endif; ?>

				<div class="white-block-content">
					<h5>
						<a href="<?php the_permalink() ?>" class="text-overflow" title="<?php echo esc_attr( get_the_title() ) ?>">
							<?php echo adifier_limit_string( get_the_title(), 50 ); ?>
						</a>
					</h5>

					<ul class="list-inline list-unstyled top-advert-meta">
						<li>
							<span><?php esc_html_e('In: ', 'adifier') ?></span>
							<?php echo adifier_the_category( 1 ); ?>
						</li>
					</ul>

					<div class="article-excerpt">
						<?php the_excerpt() ?>
					</div>

				</div>

			</article>			
			<?php
		}
		?>
		</div>
	</div>
<?php
}
wp_reset_postdata();
?>