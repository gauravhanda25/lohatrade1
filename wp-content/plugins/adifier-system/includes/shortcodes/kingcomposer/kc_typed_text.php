<?php
extract( shortcode_atts( array(
	'tag' 			=> 'h1',
	'align' 		=> 'center',
	'smart' 		=> 'yes',
	'speed' 		=> '40',
	'back_speed' 	=> '20',
	'color' 		=> '',
	'texts' 		=> '',
), $atts ) );
if( !empty( $texts ) ){
	echo '<'.esc_attr( $tag ).' '.( !empty( $color ) ? 'style="color: '.$color.'"' : '' ).' class="af-typed-text text-'.esc_attr( $align ).' hidden" data-smart="'.esc_attr( $smart ).'" data-speed="'.esc_attr( $speed ).'" data-back="'.esc_attr( $back_speed ).'">';
		echo '<span>';
			$list = array();
			foreach( $texts as $item ){
				$list[] = $item->text;
			}	
			echo implode( '|', $list );
		echo '</span>';
	echo '</'.esc_attr( $tag ).'>';
}
?>