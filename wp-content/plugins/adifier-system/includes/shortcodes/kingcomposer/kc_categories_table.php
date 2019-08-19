<?php
extract( shortcode_atts( array(
	'category_ids' 		=> '',
	'show_count'		=> 'no',
	'columns'			=> '3',
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
	<div class="element-categories-table element-categories-table-<?php echo esc_attr( $columns ) ?> flex-wrap flex-center">
		<?php
		foreach( $terms as $term ){
			$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
			?>
			<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="hover-shadow">
				<?php
					$advert_cat_icon = get_term_meta( $term->term_id, 'advert_cat_icon', true );
					if( !empty( $advert_cat_icon ) ){
						echo adifier_get_category_icon_img( $advert_cat_icon );
					}
				?>
				<h6 class="animation">
					<?php echo esc_html( $term->name ); ?>
				</h6>					
				<?php
				if( $show_count == 'yes' ){
					?>
					<div class="category-item-count"><?php echo esc_html( $term_count ).' '._n( 'ad', 'ads', $term_count, 'adifier' ); ?></div>
					<?php
				}
				?>
			</a>
			<?php
		}
		?>
	</div>
	<?php
}

?>