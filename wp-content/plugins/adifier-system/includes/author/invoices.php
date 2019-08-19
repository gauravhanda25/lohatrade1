<div class="author-panel">
	<div class="white-block white-block-extra-padding">
		<div class="white-block-title">
			<h5>
				<?php esc_html_e( 'Your Invoices', 'adifier' ); ?>
			</h5>			
		</div>
		<div class="white-block-content">
			<?php
			$invoices = new WP_Query(array(
				'post_type' 		=> 'ad-order',
				'post_status' 		=> 'publish',
				'posts_per_page'	=> -1,
				'author'			=> get_current_user_id()
			));
			if( $invoices->have_posts() ){
				?>
				<ul class="invoice-list list-unstyled">
					<li class="flex-wrap flex-center profile-advert-listing-titles profile-advert">
						<span><?php esc_html_e( 'Invoice', 'adifier' ) ?></span>
						<span><?php esc_html_e( 'Action', 'adifier' ) ?></span>	
					</li>
					<?php
					while( $invoices->have_posts() ){
						$invoices->the_post();
						$order_paid = get_post_meta( get_the_ID(), 'order_paid', true );
						$is_refunded = get_post_meta( get_the_ID(), 'order_refunded', true );
						$order_payment_type = get_post_meta( get_the_ID(), 'order_payment_type', true );
						?>
						<li class="flex-wrap flex-center">
							<span><?php the_title(); ?></span>
							<?php if( $order_paid == 'yes' || !empty( $is_refunded ) ): ?>
								<a href="<?php the_permalink() ?>" class="af-button" target="_blank">
									<?php esc_html_e( 'View Invoice', 'adifier' ) ?>
								</a>
							<?php else: ?>
								<?php echo apply_filters( 'adifier_invoices_waiting_payment_'.$order_payment_type, esc_html__( 'Waiting for payment', 'adifier' ), get_the_ID() ) ?>
							<?php endif; ?>
						</li>
						<?php
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<div class="text-center author-no-listing">
					<i class="aficon-question-circle"></i>
					<h5><?php esc_html_e( 'You have not made any orders yet', 'adifier' ) ?></h5>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>