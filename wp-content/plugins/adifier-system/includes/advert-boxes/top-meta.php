<div class="top-advert-meta flex-wrap">
	<div class="advert-cat text-overflow">
		<?php 
		$ad_category = adifier_get_advert_category();
		if( !empty( $ad_category ) ):
			?>
			<i class="aficon-dot-circle-o"></i>
			<?php echo $ad_category; ?>			
		<?php endif; ?>
	</div>			
	<div class="advert-city text-overflow">
		<?php
		$location = adifier_get_advert_location();
		if( !empty( $location ) ):
			?>
			<i class="aficon-map-marker-alt-o"></i>
			<?php echo $location; ?>
		<?php endif; ?>
	</div>
</div>