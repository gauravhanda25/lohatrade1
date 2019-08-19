<?php
extract( shortcode_atts( array(
	'heading' 		=> '',
	'align' 		=> 'center',
	'title' 		=> '',
	'subtitle' 		=> '',
), $atts ) );
?>
<div class="af-title text-<?php echo esc_attr( $align ) ?>">
	<h<?php echo esc_attr( $heading ) ?> class="af-heading"><?php echo wp_specialchars_decode( $title, ENT_QUOTES ) ?></h<?php echo esc_attr( $heading ) ?>>
	<?php echo !empty( $subtitle ) ? '<p>'.wp_specialchars_decode( $subtitle, ENT_QUOTES ).'</p>' : '' ?>
</div>