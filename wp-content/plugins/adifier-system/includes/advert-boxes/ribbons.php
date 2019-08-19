<?php
if( adifier_is_urgent() || adifier_is_topad() || adifier_is_negotiable() ){
	?>
	<div class="advert-tags">
		<?php
		if( adifier_is_urgent() ){
			?>
			<div class="ribbon urgent">
				<?php esc_html_e( 'Urgent', 'adifier' ) ?>
			</div>
			<?php
		}
		if( adifier_is_topad() ){
			?>
			<div class="ribbon featured">
				<?php esc_html_e( 'Featured', 'adifier' ) ?>
			</div>
			<?php
		}
		if( adifier_is_negotiable() ){
			?>
			<div class="ribbon negotiable">
				<?php esc_html_e( 'Negotiable', 'adifier' ) ?>
			</div>
			<?php
		}
	?>
	</div>
	<?php
}
?>