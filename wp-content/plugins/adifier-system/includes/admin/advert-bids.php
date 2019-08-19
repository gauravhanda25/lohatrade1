<?php
/*
Class for adding bid history on advert edit screen in admin
*/
if( !class_exists('Adifier_Advert_Bids_Meta_Box') ){
class Adifier_Advert_Bids_Meta_Box{

	public static function launch(){
		add_action( 'adifier_amb_action', 'Adifier_Advert_Bids_Meta_Box::bid_meta_box', 10, 2 );
	}

	public static function bid_meta_box( $post_type, $post ){
		$type = adifier_get_advert_meta( $post->ID, 'type', true );
		if( $type == 2 ){
			adifier_amb(
				'adifier_advert_bids_meta-box',
				esc_html__( 'Bid History', 'adifier' ),
				'Adifier_Advert_Bids_Meta_Box::advert_bids',
				'advert',
				'side'
			);
		}
	}

	public static function advert_bids( $post  ){
		?>
		<div class="white-block-content">
			<div class="bidding-history-results"></div>
			<a href="javascript:void(0);" class="bidding-history button" data-advertid="<?php echo esc_attr( $post->ID ) ?>" data-page="1">
				<?php esc_html_e( 'See Bidding History', 'adifier' ) ?>
			</a>
		</div>		
		<?php
	}
}
Adifier_Advert_Bids_Meta_Box::launch();
}
?>