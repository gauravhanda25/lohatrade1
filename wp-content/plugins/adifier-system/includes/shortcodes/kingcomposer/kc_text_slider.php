<?php
extract( shortcode_atts( array(
	'text_color' 		=> '',
	'speed' 			=> '',
	'align'				=> 'center',
	'grouped_slides' 	=> '',
), $atts ) );

if( !empty( $grouped_slides ) ){
	?>
	<div class="af-text-slider owl-carousel <?php echo esc_attr( 'text-'.$align ) ?>" data-color="<?php echo esc_attr( adifier_validate_hex_color( $text_color ) ); ?>" data-speed="<?php echo esc_attr( $speed ) ?>">
		<?php foreach( $grouped_slides as $slide ){ ?>
			<div class="af-title">
				<?php echo !empty( $slide->big_text ) ? '<h2 class="h1-size" style="'.( !empty( $text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $text_color ).';') : '' ).'">'.wp_specialchars_decode( $slide->big_text, ENT_QUOTES ).'</h2>' : '' ?>
				<?php echo !empty( $slide->small_text ) ? '<p style="'.( !empty( $text_color ) ? esc_attr('color:'.adifier_validate_hex_color( $text_color ).';') : '' ).'">'.wp_specialchars_decode( $slide->small_text, ENT_QUOTES ).'</p>' : '' ?>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>