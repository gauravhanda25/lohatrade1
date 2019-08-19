<?php
extract( shortcode_atts( array(
	'category_ids' 		=> '',
	'subs'				=> '5',
	'slider'			=> 'no',
	'visible_items'		=> '3',
	'autoplay_speed' 	=> '',
	'double_row'		=> 'no',
	'items_in_row'		=> '3',
	'show_empty'		=> 'no'
), $atts ) );

$args = array(
	'taxonomy' 		=> 'advert-category',
	'hide_empty'	=> $show_empty == 'yes' ? false : true
);

if( !empty( $category_ids ) ){
	$args['include'] = explode( ',', $category_ids );
	$args['orderby'] = 'include';
	$args['parent'] = 0;
}
else{
	$args['parent'] = 0;
}

$terms = get_terms( $args );

if( !empty( $terms ) ){
	?>
	<div class="element-categories-tree <?php echo  $slider == 'yes' ? esc_attr( 'categories-slider owl-carousel' ) : esc_attr( 'af-items-'.$items_in_row ) ?>" data-visibleitems="<?php echo esc_attr( $visible_items ) ?>" data-margin="30" data-autoplay="<?php echo esc_attr( $autoplay_speed ) ?>">
		<?php
		$max = $double_row == 'yes' ? 2 : 1;
		$counter = 0;
		if( $slider == 'yes' ){
			echo '<div>';
		}

		foreach( $terms as $term ){
			$bg_image = '';
			$advert_cat_image = get_term_meta( $term->term_id, 'advert_cat_image', true );
			$image = wp_get_attachment_image_src( $advert_cat_image, 'adifier-grid' );
			if( !empty( $image[0] ) ){
				$bg_image = 'background-image:url('.esc_url( $image[0] ).')';
			}
			if( $slider == 'yes' && $counter == $max  ){
				echo '</div><div>';
				$counter = 0;
			}
			$counter++;

			?>
			<div class="af-item-wrap white-block hover-shadow" style="<?php echo  $bg_image ?>">
				<div class="white-block-content">
					<h5>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<?php echo $term->name ?>
						</a>
					</h5>
					<?php
					$subterms = get_terms(array(
						'taxonomy' 		=> 'advert-category',
						'parent'		=> $term->term_id,
						'number'		=> $subs,
						'hide_empty'	=> $show_empty == 'yes' ? false : true
					));
					if( !empty( $subterms ) ){
						?>
						<ul class="categories-subterms list-unstyled">
							<?php
							foreach( $subterms as $subterm ){
								?>
								<li>
									<a href="<?php echo esc_url( get_term_link( $subterm ) ); ?>">
										<?php echo $subterm->name; ?>
									</a>
								</li>
								<?php
							}
							?>
						</ul>
						<?php
					}					
					?>
					<div class="view-more">
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<?php esc_html_e( 'View All', 'adifier' ) ?> <i class="aficon-caret-right-0"></i>
						</a>
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