<?php
extract( shortcode_atts( array(
	'hiw_item' 	=> '',
), $atts ) );

if( !empty( $hiw_item ) ):
?>
<div class="hiw-wrapper hiw-wrapper-<?php echo esc_attr( count( $hiw_item ) ) ?> flex-wrap flex-start-v">
	<?php foreach( $hiw_item as $item ): 
		$class = 'hiw-'.time();
		extract( (array)$item );
		?>
		<div class="hiw-item service <?php echo esc_attr( $class ) ?>">
			<?php 
			if( !empty( $icon ) ){
				$style = '';
				if( !empty( $icon_bg_color ) || !empty( $icon_font_color ) ){
					$style = 'style="background: '.esc_attr( $icon_bg_color ).'; color: '.esc_attr( $icon_font_color ).'"';
				}
				echo '<div class="service-icon animation" '.$style.'><i class="'.esc_attr( $icon ).'"></i></div>';
			}
			?>
			<div class="service-content">
				<?php echo !empty( $title ) ? '<h5>'.$title.'</h5>' : ''; ?>
				<?php echo !empty( $description ) ? '<p>'.$description.'</p>' : ''; ?>
				<div class="hiw-item-style hidden">
					<?php
						echo '.'.$class.':hover .service-icon{
							color:'.$icon_font_color_hover.'!important;
							background:'.$icon_bg_color_hover.'!important;
						} 
						.'.$class.'.hiw-item:before{
							border-color:'.$icon_bg_color.'!important;
						}';
					?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>