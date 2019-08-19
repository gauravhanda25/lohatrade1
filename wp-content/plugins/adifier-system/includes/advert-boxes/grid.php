<div class="white-block hover-shadow advert-item advert-grid <?php echo adifier_is_highlighted() ? esc_attr( 'advert-hightlight' ) : esc_attr( '' ) ?>">

	<?php include( get_theme_file_path( 'includes/advert-boxes/ribbons.php' ) ) ?>

	<a href="<?php the_permalink() ?>" class="advert-media">
		<?php adifier_get_advert_image('adifier-grid') ?>
	</a>

	<div class="white-block-content">

		<?php include( get_theme_file_path( 'includes/advert-boxes/top-meta.php' ) ) ?>

		<?php include( get_theme_file_path( 'includes/advert-boxes/title.php' ) ) ?>

		<?php include( get_theme_file_path( 'includes/advert-boxes/bottom-meta.php' ) ) ?>

	</div>

	<?php adifier_get_map_lat_long(); ?>
</div>