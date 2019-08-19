<?php
extract( shortcode_atts( array(
	'style' 			=> '',
	'grouped_terms' 	=> '',
), $atts ) );

if( !empty( $grouped_terms ) ){
	$term_ids = wp_list_pluck( $grouped_terms, 'term_id' );
	$term_ids = array_map ('intval', $term_ids);
	$term_counts = adifier_get_advert_taxonomy_counts( $term_ids );
	?>
	<div class="element-locations-wrap <?php echo esc_attr( $style ) ?> clearfix">
		<?php
		foreach( $grouped_terms as $term_pack ){
			$term = get_term_by( 'id', $term_pack->term_id, 'advert-location' );
			if( !empty( $term ) && !is_wp_error( $term ) ){
				$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
				$image = wp_get_attachment_image_src( $term_pack->image, 'full' );
				?>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="elem-location-item" <?php echo !empty( $image[0] ) ?  'style="background-image:url(\''.esc_url( $image[0] ).'\')"' : '' ?>>
					<div class="location-title animation">
						<h6><?php echo esc_html( $term->name ) ?></h6>
						<p><?php echo esc_html( $term_count ).' '._n( 'ad posted', 'ads posted', $term_count, 'adifier' ); ?></p>
					</div>
				</a>
				<?php
			}
		}
		?>
	</div>
	<?php
}
?>