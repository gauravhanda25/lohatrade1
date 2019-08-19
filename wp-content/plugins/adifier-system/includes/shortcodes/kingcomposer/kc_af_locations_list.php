<?php
extract( shortcode_atts( array(
	'list_color' 		=> '',
	'columns' 			=> '',
	'location_ids'		=> ''
), $atts ) );

$args = array(
	'taxonomy' => 'advert-location'
);

if( !empty( $location_ids ) ){
	$args['include'] = explode( ',', $location_ids );
	$args['orderby'] = 'include';
}
else{
	$args['parent'] = 0;
}

$terms = get_terms( $args );

if( !empty( $terms ) && !is_wp_error( $terms ) ){
	?>
	<ul class="list-unstyled element-locations-list location-columns-<?php echo esc_attr( $columns ) ?>">
		<?php
		foreach( $terms as $term ){
			?>
			<li>
				<a href="<?php echo esc_url( get_term_link( $term ) ) ?>" class="elem-location-item" <?php echo !empty( $list_color ) ? 'style="color: '.$list_color.';"' : '' ?>>
					<?php echo esc_html( $term->name ) ?>
				</a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>