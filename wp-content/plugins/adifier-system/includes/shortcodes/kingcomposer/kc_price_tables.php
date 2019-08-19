<?php
extract( shortcode_atts( array(
	'package' 		=> '',
	'is_active' 	=> 'no',
), $atts ) );

$account_payment = adifier_get_option( 'account_payment' );
$packs = adifier_get_packs( $account_payment, true );
$link = 'href="#" data-target="#login" data-toggle="modal"';
if( is_user_logged_in() ){
	$link = 'href="'.esc_url( add_query_arg( 'screen', 'acc_pay', get_author_posts_url( get_current_user_id() ) ) ).'"';
}
if( !empty( $packs[$package] ) ){
	?>
	<div class="white-block price-table-element hover-shadow <?php echo  $is_active == 'yes' ? esc_attr( 'active-price-table' ) : esc_attr( '' ) ?>">
		<div class="price-table-price">
			<?php
				echo adifier_price_format( $packs[$package]['price'] == '-' ? 0 : $packs[$package]['price'] );
				adifier_tax_included();
			?>
		</div>
		<div class="price-table-title">
			<h5><?php echo esc_html( $packs[$package]['name'] ) ?></h5>
		</div>		
		<div class="price-table-content">
			<?php adifier_packs_message( $account_payment, $packs[$package] ); ?>
		</div>
		<a <?php echo $link ?> class="af-button purchase-pack" ><?php $packs[$package]['price'] == '-' ? esc_html_e( 'Register Now', 'adifier' ) : esc_html_e( 'Purchase Now', 'adifier' ) ?></a>
	</div>		
	<?php
}

?>