<?php
$header_submit = adifier_get_option( 'header_submit' );
if( !empty( $_GET['header_submit'] ) ){
	$header_submit = $_GET['header_submit'];
}
?>
<a href="<?php echo is_user_logged_in() ? esc_url( add_query_arg( 'screen', 'new', get_author_posts_url( get_current_user_id() ) )) : esc_attr( '#' ) ?>" <?php echo !is_user_logged_in() ? 'data-toggle="modal" data-target="#login"' : esc_attr( '' ) ?> class="submit-btn-wrap <?php echo $header_submit == 'icon' ? esc_attr( 'account-btn' ) : esc_attr( 'submit-btn' ) ?> <?php echo !is_user_logged_in() ? esc_attr( 'submit-redirect' ) : '' ?>">
	<i class="aficon-add-ad"></i>
	<?php
	if( $header_submit !== 'icon' ){
		?>
		<span><?php esc_html_e( 'Submit Ad', 'adifier' ) ?></span>			
		<?php		
	}
	?>
</a>