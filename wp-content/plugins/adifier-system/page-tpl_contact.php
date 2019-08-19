<?php
/*
	Template Name: Page Contact
*/
get_header();
the_post();

include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/header-search.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );
?>

<main>
	<div class="container">

		<?php
		$markers = adifier_get_option( 'markers' );
		if( !empty( $markers ) && !empty( $markers[0] ) ):
		?>
		<div class="contact-map location-map hidden">
			<?php
			echo json_encode( $markers );
			?>
		</div>
		<?php endif; ?>

		<div class="white-block">
			<div class="white-block-title">
				<h5><?php esc_html_e( 'Send Us A Message', 'adifier' ) ?></h5>
			</div>
			<div class="white-block-content">
				<form class="ajax-form">
					<div class="row">
						<div class="col-sm-4">
							<label for="name"><?php esc_html_e( 'Your Name *', 'adifier' ) ?></label>
							<input type="text" id="name" name="name" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="email"><?php esc_html_e( 'Your Email *', 'adifier' ) ?></label>
							<input type="text" id="email" name="email" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="subject"><?php esc_html_e( 'Message Subject *', 'adifier' ) ?></label>
							<input type="text" id="subject" name="subject" class="form-control" />
						</div>
					</div>
					<label for="message"><?php esc_html_e( 'Your Message *', 'adifier' ) ?></label>
					<textarea rows="10" cols="100" id="message" name="message" class="form-control"></textarea>

					<?php adifier_gdpr_checkbox() ?>

					<input type="hidden" name="action" value="send_contact">

					<div class="ajax-form-result"></div>

					<p class="form-submit">
						<a href="javascript:;" class="submit-ajax-form af-button"><?php esc_html_e( 'Send Message', 'adifier' ) ?> </a>
					</p>

				</form>					
			</div>
		</div>

	</div>
</main>
<?php get_footer(); ?>