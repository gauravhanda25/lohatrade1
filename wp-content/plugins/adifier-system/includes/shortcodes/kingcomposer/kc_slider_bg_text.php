<?php
extract( shortcode_atts( array(
	'text_color' 		=> '',
	'speed' 			=> '',
	'grouped_slides' 	=> '',
), $atts ) );

if( !empty( $grouped_slides ) ){
	?>
	<div class="af-slider-bg-text owl-carousel text-center" data-speed="<?php echo esc_attr( $speed ) ?>">
		<?php foreach( $grouped_slides as $slide ){ ?>
			<?php
			$image_data = wp_get_attachment_image_src( $slide->image, 'full' );
			?>
			<div class="af-slider-bg-text-item" style="background-image:url(<?php echo !empty( $image_data ) ? esc_url( $image_data[0] ) : esc_attr('') ?>)">
				<div class="af-title af-slider-bg-text-caption">
					<?php echo !empty( $slide->big_text ) ? '<h2 class="h1-size" style="'.( !empty( $text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $text_color ).';') : '' ).'">'.$slide->big_text.'</h2>' : '' ?>
					<?php echo !empty( $slide->small_text ) ? '<p style="'.( !empty( $text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $text_color ).';') : '' ).'">'.$slide->small_text.'</p>' : '' ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>