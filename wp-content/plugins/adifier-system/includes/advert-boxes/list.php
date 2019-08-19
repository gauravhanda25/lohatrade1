<div class="white-block hover-shadow advert-item advert-list <?php echo adifier_is_highlighted() ? esc_attr( 'advert-hightlight' ) : esc_attr( '' ) ?>">

	<?php include( get_theme_file_path( 'includes/advert-boxes/ribbons.php' ) ) ?>

	<div class="flex-wrap flex-start-h">
		<div class="flex-left">
			<a href="<?php the_permalink() ?>" class="advert-media">
				<?php adifier_get_advert_image('adifier-list') ?>
			</a>
		</div>
		<div class="flex-right">
			<div class="white-block-content">
				
				<?php include( get_theme_file_path( 'includes/advert-boxes/ribbons.php' ) ) ?>

				<?php include( get_theme_file_path( 'includes/advert-boxes/top-meta.php' ) ) ?>

				<?php include( get_theme_file_path( 'includes/advert-boxes/title.php' ) ) ?>


				<p class="excerpt">
					<?php
					$limit = !empty( $limit ) ? $limit : 120;
					echo adifier_limit_string( get_the_excerpt(), $limit ); ?>
				</p>
				
				<?php include( get_theme_file_path( 'includes/advert-boxes/bottom-meta.php' ) ) ?>
				
			</div>
		</div>
	</div>
	<?php adifier_get_map_lat_long(); ?>
</div>