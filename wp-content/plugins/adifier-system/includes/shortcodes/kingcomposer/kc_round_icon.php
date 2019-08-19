<?php
extract( shortcode_atts( array(
	'icon' 			=> '',
	'size' 			=> '',
	'color' 		=> '',
	'bg_color' 		=> '',
	'width' 		=> '',
	'height' 		=> ''
), $atts ) );

?>
<div class="text-center">
	<span class="round-icon" style="<?php echo !empty( $color ) ? 'background: '.$bg_color.'; box-shadow:0px 0px 0px 10px '.adifier_hex2rgba( $bg_color, 0.25 ).';' : ''; ?> <?php echo !empty( $width ) ? 'width: '.$width.';' : '' ?> <?php echo !empty( $height ) ? 'height: '.$height.'; line-height: '.$height.'' : '' ?>">
		<i style="<?php echo !empty( $color ) ? 'color: '.$color.';' : '' ?> <?php echo !empty( $size ) ? 'font-size: '.$size.';' : '' ?>" class="<?php echo esc_attr( $icon ) ?>"></i>
	</span>
</div>