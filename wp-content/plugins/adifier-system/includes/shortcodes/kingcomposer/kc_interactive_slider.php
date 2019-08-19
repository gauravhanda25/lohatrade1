<?php
extract( shortcode_atts( array(
	'dots_color' 		=> '',
	'speed' 			=> '',
	'slider_height' 	=> '450px;',
	'grouped_slides' 	=> '',
), $atts ) );

if( !empty( $grouped_slides ) ){
	?>
	<div class="af-text-slider af-interactive-slider owl-carousel" data-color="<?php echo esc_attr( adifier_validate_hex_color( $dots_color ) ); ?>" data-speed="<?php echo esc_attr( $speed ) ?>">
		<?php foreach( $grouped_slides as $slide ){ ?>
			<div class="flex-wrap af-interactive-item" style="height:<?php echo esc_attr( $slider_height ) ?>">
				<div class="flex-left">
					<?php echo !empty( $slide->big_text ) ? '<h2 class="h1-size" style="'.( !empty( $slide->big_text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $slide->big_text_color ).';') : '' ).'">'.wp_specialchars_decode( $slide->big_text, ENT_QUOTES ).'</h2>' : '' ?>					
					<?php echo !empty( $slide->small_text ) ? '<p style="'.( !empty( $slide->small_text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $slide->small_text_color ).';') : '' ).'">'.wp_specialchars_decode( $slide->small_text, ENT_QUOTES ).'</p>' : '' ?>
					<?php echo !empty( $slide->button_text ) ? '<a class="header-alike" href="'.esc_url( $slide->button_link ).'">'.wp_specialchars_decode( $slide->button_text, ENT_QUOTES ).'</a>' : '' ?>

				</div>
				<div class="flex-right">
					<?php echo !empty( $slide->image ) ? wp_get_attachment_image( $slide->image, 'full' ) : ''; ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>