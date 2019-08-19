<?php
$google_adsense = adifier_get_option( 'google_adsense' );
if( !empty( $google_adsense) && function_exists('adifier_output') ):
	?>
	<div class="gads">
		<?php adifier_output( $google_adsense ); ?>
	</div>
	<?php
endif;
?>