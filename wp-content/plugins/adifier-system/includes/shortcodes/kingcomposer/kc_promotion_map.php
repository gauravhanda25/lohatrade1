<?php
extract( shortcode_atts( array(
	'height' 		=> '',
), $atts ) );
?>
<div class="search-map" style="<?php echo !empty( $height ) ? esc_attr( 'height: '.$height ) : esc_attr(''); ?>">
<?php
$ids = adifier_get_homemap_ads_list();
if( !empty( $ids ) ){
	$args = array(
		'posts_per_page' => -1,
		'post__in' => array_keys( $ids )
	);

	$adverts = new Adifier_Advert_Query( $args );
	if( $adverts->have_posts() ){
		$map_data = array();
		while( $adverts->have_posts() ){
			$adverts->the_post();
			$item_data = adifier_get_advert_map_data();
			$item_data['iconwidth'] = $item_data['width'];
			$item_data['iconheight'] = $item_data['height'];
			ob_start();
			?>
				<a href="<?php the_permalink() ?>" class="advert-media text-overflow" target="_blank">
					<?php the_post_thumbnail( 'adifier-grid' ) ?>
				</a>
				<div class="flex-right">
					<h5 class="adv-title"> 
						<a href="<?php the_permalink() ?>" class="text-overflow" target="_blank"> 
							<?php the_title() ?>
						</a>
					</h5>
					<div class="bottom-advert-meta flex-wrap">
						<?php echo adifier_get_advert_price() ?>
					</div>
				</div>			
			<?php
			$item_data['content'] = str_replace(array("\n", "\t", "\r", "\n\r"), '', ob_get_contents());
			ob_end_clean();
			$map_data[] = $item_data;
		}
		?>
		<script class="search-map-item">
			<?php echo json_encode( $map_data ) ?>
		</script>
		<?php		
	}
	wp_reset_postdata();
}
?>
</div>