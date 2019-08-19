<div class="bottom-advert-meta flex-wrap">
	<?php echo adifier_get_advert_price() ?>
	<div class="flex-right">
		<?php if( adifier_get_option( 'enable_compare' ) == 'yes' ): ?>
			<a href="javascript:void(0);" class="compare-add <?php echo adifier_is_in_compare( get_the_ID() ) ? esc_attr( 'active' ) : esc_attr(''); ?>" data-id="<?php echo esc_attr( get_the_ID() ) ?>" title="<?php esc_attr_e( 'Add This To Compare', 'adifier' ) ?>">
				<i class="aficon-repeat"></i>
			</a>			
		<?php endif; ?>	
		<?php adifier_get_favorites_html() ?>
	</div>
</div>