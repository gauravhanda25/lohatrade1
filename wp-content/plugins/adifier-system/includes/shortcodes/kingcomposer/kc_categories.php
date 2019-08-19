<?php
extract( shortcode_atts( array(
	'category_ids' 		=> '',
	'style' 			=> 'top_icon',
	'show_count'		=> 'no',
	'slider'			=> 'no',
	'visible_items'		=> '7',
	'autoplay_speed' 	=> '',
	'double_row'		=> 'no',
	'items_in_row'		=> '4',
	'icon_max_width'	=> '',
	'name_font_size'	=> '',
	'count_font_size'	=> '',
	'icon_margin'		=> '',
	'box_padding'		=> '',
	'show_empty'		=> 'no'
), $atts ) );

$args = array(
	'taxonomy' 		=> 'advert-category',
	'hide_empty'	=> $show_empty == 'yes' ? false : true
);

if( !empty( $category_ids ) ){
	$args['include'] = explode( ',', $category_ids );
	$args['orderby'] = 'include';
}
else{
	$args['parent'] = 0;
}

$terms = get_terms( $args );

if( !empty( $terms ) ){
	if( $show_count == 'yes' ){
		$term_ids = wp_list_pluck( $terms, 'term_id' );
		$term_counts = adifier_get_advert_taxonomy_counts( $term_ids );
	}
	?>
	<div class="element-categories-list <?php echo  $slider == 'yes' ? esc_attr( 'categories-slider owl-carousel' ) : esc_attr( 'af-items-'.$items_in_row.' af-items-close-10' ) ?>" data-visibleitems="<?php echo esc_attr( $visible_items ) ?>"  data-autoplay="<?php echo esc_attr( $autoplay_speed ) ?>">
		<?php
		$max = $double_row == 'yes' ? 2 : 1;
		$counter = 0;
		if( $slider == 'yes' ){
			echo '<div>';
		}
		foreach( $terms as $term ){
			$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
			$bg_image = '';
			if( $style == 'side_icon_bg' || $style == 'top_icon_bg' ){
				$advert_cat_image = get_term_meta( $term->term_id, 'advert_cat_image', true );
				$image = wp_get_attachment_image_src( $advert_cat_image, 'adifier-grid' );
				if( !empty( $image[0] ) ){
					$bg_image = 'background-image:url('.esc_url( $image[0] ).')';
				}
			}
			if( $slider == 'yes' && $counter == $max  ){
				echo '</div><div>';
				$counter = 0;
			}
			$counter++;
			?>
			<div class="af-item-wrap hover-shadow <?php echo esc_attr( $style ) ?>" style="<?php echo  $bg_image ?>">
				<div class="category-item <?php echo  $style == 'side_icon_bg' || $style == 'side_icon' ? esc_attr( 'flex-wrap flex-start-h' ) : esc_attr('') ?>" style="<?php echo !empty( $box_padding ) ? esc_attr( 'padding: '.$box_padding ) : esc_attr( '' ) ?>">
					<div class="flex-left">
						<?php
							$advert_cat_icon = get_term_meta( $term->term_id, 'advert_cat_icon', true );
							if( !empty( $advert_cat_icon ) ){
								?>
								<a href="<?php echo esc_url( get_term_link( $term ) ) ?>">
									<div class="category-icon" style="<?php echo !empty( $icon_margin ) ? esc_attr( 'margin-bottom: '.$icon_margin.';' ) : esc_attr(''); ?><?php echo !empty( $icon_max_width ) ? esc_attr( 'width: '.$icon_max_width ) : esc_attr( '' ); ?>"><?php echo adifier_get_category_icon_img( $advert_cat_icon ); ?></div>
								</a>
								<?php
							}
						?>
					</div>
					<div class="flex-right">
						<h5 style="<?php echo !empty( $name_font_size ) ? esc_attr( 'font-size: '.$name_font_size ) : esc_attr( '' ); ?>">
							<a href="<?php echo esc_url( get_term_link( $term ) ) ?>">
								<?php echo esc_html( $term->name ); ?>
							</a>
						</h5>
						<?php
						if( $show_count == 'yes' ){
							?>
							<div class="category-item-count" style="<?php echo !empty( $count_font_size ) ? esc_attr( 'font-size: '.$count_font_size ) : esc_attr( '' ); ?>"><?php echo  esc_html( $term_count ).' '._n( 'ad posted', 'ads posted', $term_count, 'adifier' ); ?></div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		if( $slider == 'yes' ){
			echo '</div>';
		}		
		?>
	</div>
	<?php
}

?>