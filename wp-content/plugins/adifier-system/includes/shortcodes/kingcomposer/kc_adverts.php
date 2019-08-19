<?php
extract( shortcode_atts( array(
	'ads_source' 		=> 'by_choice',
	'topads' 			=> 'no',
	'post_ids' 			=> '',
	'post_number' 		=> '6',
	'category_ids' 		=> '',
	'location_ids' 		=> '',
	'type' 				=> '',
	'orderby' 			=> '',
	'order' 			=> '',
	'style' 			=> 'grid',
	'slider' 			=> 'no',
	'visible_items' 	=> '',
	'autoplay_speed' 	=> '',
	'double_row' 		=> '',
	'items_in_row' 		=> '4'
), $atts ) );

if( $ads_source == 'by_choice' ){
	$args = array(
		'post__in' => explode( ',', $post_ids ),
		'orderby'  => 'post__in'
	);
}
else{
	$args = array(
		'posts_per_page' => $post_number,
		'orderby'		 => $orderby,
		'order'		 	 => $order,
	);

	if( $topads == 'yes' ){
		$args['post__in'] = adifier_topads_ids_list();
	}

	if( !empty( $category_ids ) ){
		$args['tax_query'] = array(
			array(
				'taxonomy' 	=> 'advert-category',
				'terms'		=> explode( ',', $category_ids )
			)
		);
	}

	if( !empty( $location_ids ) ){
		$args['tax_query'] = array(
			array(
				'taxonomy' 	=> 'advert-location',
				'terms'		=> explode( ',', $location_ids )
			)
		);
	}	
}
if( !empty( $type ) ){
	$args['type'] = $type;
}

$args['post_status'] = 'publish';

$adverts = new Adifier_Advert_Query( $args );
if( $adverts->have_posts() ){
	if( $style == 'big_slider' ){
		?>
		<div class="adverts-big-slider owl-carousel">
			<?php
			while( $adverts->have_posts() ){
				$adverts->the_post();
				?>
				<div class="white-block hover-shadow">
					<a href="<?php the_permalink() ?>" class="advert-media">
						<?php the_post_thumbnail( 'adifier-single-slider' ) ?>
					</a>
					<div class="white-block-content">
						<?php include( get_theme_file_path( 'includes/advert-boxes/top-meta.php' ) ) ?>

						<?php include( get_theme_file_path( 'includes/advert-boxes/title.php' ) ) ?>

						<?php include( get_theme_file_path( 'includes/advert-boxes/bottom-meta.php' ) ) ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
	else{
		$max = $double_row == 'yes' ? 2 : 1;
		$counter = 0;
		?>
		<div class="adverts-list clearfix <?php echo  $slider == 'yes' ? esc_attr( 'adverts-slider owl-carousel' ) : esc_attr( 'af-items-'.$items_in_row ) ?>" data-visibleitems="<?php echo esc_attr( $visible_items ) ?>" data-autoplay="<?php echo esc_attr( $autoplay_speed ) ?>">
			<div class="af-item-wrap">
			<?php
			while( $adverts->have_posts() ){
				$adverts->the_post();
				if( $max == $counter ){
					?>
					</div><div class="af-item-wrap">
					<?php
					$counter = 0;
				}
				$counter++;
				if( $style == 'list' ){
					$limit = 80;
				}
				include( get_theme_file_path( 'includes/advert-boxes/'.$style.'.php' ) );
			}
			?>
			</div>
		</div>
	<?php 
	}
	?>
<?php
}
wp_reset_postdata();
?>