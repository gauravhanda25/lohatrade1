<?php
extract( shortcode_atts( array(
	'visible_items'		=> '7',
	'autoplay_speed' 	=> '',
	'grouped_terms' 	=> '',
), $atts ) );

if( !empty( $grouped_terms ) ){
	?>
	<div class="element-categories-transparent-wrap categories-slider owl-carousel" data-visibleitems="<?php echo esc_attr( $visible_items ) ?>" data-stagepadding="0" data-autoplay="<?php echo esc_attr( $autoplay_speed ) ?>">
		<?php
		foreach( $grouped_terms as $term_pack ){
			$term = get_term_by( 'id', $term_pack->term_id, 'advert-category' );
			if( !empty( $term ) && !is_wp_error( $term ) ){
				if( !empty( $term_pack->image ) ){
					$image_id = $term_pack->image;
				}
				else{
					$advert_cat_image = get_term_meta( $term->term_id, 'advert_cat_icon', true );
					$image_id = $advert_cat_image;
				}
				?>
				<a class="animation categories-transparent-item" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<?php echo adifier_get_category_icon_img( $image_id ); ?>
					<h5 class="animation text-overflow"><?php echo  esc_html( $term->name ); ?></h5>
				</a>
				<?php
			}
		}
		?>
	</div>
	<?php
}
?>