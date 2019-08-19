<div class="author-panel">
	<div class="white-block">
		<div class="messages-wrap flex-wrap">
			<div class="messages-left animation">

				<div class="adverts-filter conversation-filters-wrap">

					<form action="<?php echo esc_url( $author_url ) ?>" class="conversation-filter">
						<div class="flex-wrap flex-center">
							<input type="text" name="keyword" placeholder="<?php esc_attr_e( 'Filter by advert / user...', 'adifier' ) ?>" />
							<i class="aficon-search"></i>
						</div>
						<input type="hidden" name="action" value="adifier_ajax_conversations" />
					</form>

				</div>
				
				<div class="conversations-window-wrap">
					<div class="conversations-listing-wrap">
						<div class="ajax-conversations">
							<?php 
								$conversations = new Adifier_Conversation_Query();
								$conversations->display_frontend_conversations();
							?>
						</div>
					</div>
				</div>
				<div class="ajax-conversations-pagination">
					<?php $conversations->display_pagination(); ?>
				</div>
				<div class="adverts-filter conversation-filters-wrap flex-wrap flex-center">
					<ul class="list-inline list-unstyled">
						<li>
							<a href="javascript:void(0);" class="toggle-list">
								<?php esc_html_e( 'Check All', 'adifier' ) ?>
							</a>
						</li>
					</ul>					
					<ul class="list-inline list-unstyled">
						<li>
							<a href="javascript:void(0);" class="delete-conversations" data-confirm="<?php esc_attr_e( 'Are you sure?', 'adifier' ) ?>">
								<?php esc_html_e( 'Delete Selected', 'adifier' ) ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="messages-right">
				<div class="messages-header flex-wrap flex-center">
					<h5 class="no-margin flex-wrap flex-center">
						<a href="javascript:;" class="toggle-conversations">
							<i class="aficon-comments"></i>
						</a>
						<span class="paste-advert-title">
							<?php
							if( !empty( $_GET['con_id'] ) ){
								$conversation = Adifier_Conversations::get_conversation_by_id( $_GET['con_id'] );
								echo esc_html( $conversation->advert_title );
							}
							else{
								echo '<span class="small-display"><span class="con-arrow-hide">&larr;</span>'.esc_html__( 'Click to see conversation list', 'adifier' ).'</span>';
							}
							?>
						</span>
					</h5>
					<div class="conversation-review">
						<?php 
						if( !empty( $_GET['con_id'] ) ){
							adifier_advert_review_action( $conversation );
						}
						?>
					</div>
				</div>
				<div class="messages-window-wrap">
					<div class="message-listing-wrap">
						<div class="message-listing">
							<?php 
							if( !empty( $_GET['con_id'] ) ){
								$messages = new Adifier_Messages_Query(array(
									'con_id' => $_GET['con_id']
								));
								$messages->display_messages();
							}
							else{
								?>
								<div class="text-center author-no-listing messages-no">
									<i class="aficon-reply"></i>
									<h5><?php esc_html_e( 'Select conversation from the list', 'adifier' ) ?></h5>
								</div>								
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<div class="messages-footer">
					<form class="message-form flex-wrap flex-center <?php echo empty( $_GET['con_id'] ) ? esc_attr('disabled') : '' ?>">
						<textarea name="message" rows="1" class="messages-textarea" placeholder="<?php esc_html_e( 'Type a message here', 'adifier' ) ?>" ></textarea>
						<a href="javascript:void(0)" class="af-button send-message"><?php esc_html_e( 'Send', 'adifier' ) ?></a>
						<input type="hidden" name="con_id" value="<?php echo !empty( $_GET['con_id'] ) ? esc_attr( $_GET['con_id'] ) : '' ?>">
						<input type="hidden" name="action" value="adifier_send_message">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>