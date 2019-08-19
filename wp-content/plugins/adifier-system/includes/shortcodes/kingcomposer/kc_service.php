<?php
extract( shortcode_atts( array(
	'style' 			=> '',
	'icon' 				=> '',
	'icon_bg_color' 	=> '',
	'icon_font_color' 	=> '',
	'title' 			=> '',
	'subtitle' 			=> '',
), $atts ) );

?>
<div class="white-block service hover-shadow <?php echo esc_attr( $style ) ?>">
	<div class="white-block-content">
		<?php 
		if( !empty( $icon ) ){
			$style = '';
			if( !empty( $icon_bg_color ) || !empty( $icon_font_color ) ){
				$style = 'style="background: '.esc_attr( $icon_bg_color ).'; color: '.esc_attr( $icon_font_color ).'"';
			}
			echo '<div class="service-icon" '.$style.'><i class="'.esc_attr( $icon ).'"></i></div>';
		}
		?>
		<div class="service-content">
			<?php echo !empty( $title ) ? '<h5>'.$title.'</h5>' : ''; ?>
			<?php echo !empty( $subtitle ) ? '<p>'.$subtitle.'</p>' : ''; ?>
		</div>
	</div>
</div>