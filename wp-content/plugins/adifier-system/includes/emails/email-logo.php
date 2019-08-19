<?php
$email_logo = adifier_get_option( 'email_logo' );
if( !empty( $email_logo['url'] ) ){
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ) ?>" class="logo">
		<img src="<?php echo esc_url( $email_logo['url'] ) ?>" alt="email-logo" width="<?php echo esc_attr( $email_logo['width'] ) ?>" height="<?php echo esc_attr( $email_logo['height'] ) ?>"/>
	</a>
	<?php
}
else{
	adifier_logo_html_wrapper( 'site_logo' );
}
?>