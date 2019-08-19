<?php
extract( shortcode_atts( array(
	'style' 		=> '',
), $atts ) );

if( $style == 'shadow' ){
	include( get_theme_file_path( 'includes/headers/search-form.php' ) );
}
else if( $style == 'labeled' ){
	$is_labeled = true;
	?>
	<form action="<?php echo adifier_get_search_link() ?>" class="labeled-main-search flex-wrap">
		<div>
			<label for="keyword"><?php esc_html_e( 'Keyword', 'adifier' ) ?></label>
			<?php include( get_theme_file_path( 'includes/headers/search-parts/keyword.php' ) ); ?>
		</div>
		<?php include( get_theme_file_path( 'includes/headers/search-parts/location.php' ) ); ?>
		<div>
			<label for="category"><?php esc_html_e( 'Category', 'adifier' ) ?></label>
			<?php include( get_theme_file_path( 'includes/headers/search-parts/category.php' ) ); ?>
		</div>
		<div class="search-submit">
			<?php include( get_theme_file_path( 'includes/headers/search-parts/submit.php' ) ); ?>
		</div>
	</form>
	<?php
}
else{
	?>
	<div class="kc-search <?php echo $style == 'vertical' ? esc_attr( 'kc-search-vertical' ) : '' ?>">
		<?php
		if( $style == 'vertical' ){
			?>
			<h5> <?php esc_html_e( 'I\'m interested in...', 'adifier' ) ?> </h5>
			<?php
		}
		?>
		<?php include( get_theme_file_path( 'includes/headers/search-form.php' ) ); ?>
	</div>	
	<?php
}
?>