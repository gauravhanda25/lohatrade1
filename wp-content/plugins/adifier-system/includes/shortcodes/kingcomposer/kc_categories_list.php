<?php
extract( shortcode_atts( array(
	'category_ids' 		=> '',
	'show_count'		=> 'no',
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

if( !empty( $terms ) && !is_wp_error( $terms ) ){
	if( $show_count == 'yes' ){
		$term_ids = wp_list_pluck( $terms, 'term_id' );
		$term_counts = adifier_get_advert_taxonomy_counts( $term_ids );
	}
	?>
	<ul class="element-categories-v-list list-unstyled">
		<?php
		foreach( $terms as $term ){
			$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
			?>
			<li class="white-block hover-shadow">				
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="flex-wrap">
					<h5 class="flex-wrap flex-start-h animation">
						<?php
						$advert_cat_icon = get_term_meta( $term->term_id, 'advert_cat_icon', true );
						if( !empty( $advert_cat_icon ) ){
							echo adifier_get_category_icon_img( $advert_cat_icon );
						}
						echo esc_html( $term->name ); 
						?>
					</h5>
					<?php
					if( $show_count == 'yes' ){
						?>
						<div class="category-item-count"><?php echo esc_html( $term_count ).' '._n( 'ad', 'ads', $term_count, 'adifier' ); ?></div>
						<?php
					}
					?>						
				</a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}

?>