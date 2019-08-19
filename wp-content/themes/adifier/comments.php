<?php if ( comments_open() ) :?>
	<?php if( get_comments_number() > 0 ): ?>
		<div class="white-block">
			<div class="white-block-title">
				<h5>
					<?php comments_number( esc_html__('0 Comments', 'adifier'), esc_html__('1 Comment', 'adifier'), esc_html__('% Comments', 'adifier') ) ?>
				</h5>
			</div>
			<div class="white-block-content">
				<div class="comment-content comments">
					<?php 
					if( have_comments() ){
						wp_list_comments(array(
							'callback'	=> 'adifier_comments',
							'style' 	=> 'div'
						));
					}
					?>
				</div>
			</div>
		</div>	
		<?php
		$comment_links = paginate_comments_links( 
			array(
				'echo' 		=> false,
				'prev_next' => false,
				'separator' => ' ',
			) 
		);
		if( !empty( $comment_links ) ):
		?>
			<div class="pagination comments-pagination">
				<?php echo wp_kses_post( $comment_links ); ?>
			</div>
		<?php endif; ?>		
	<?php endif; ?>

	<div class="white-block">
		<div class="white-block-title">
			<h5><?php esc_html_e( 'Leave A Comment', 'adifier' ) ?></h5>
		</div>
		<div class="white-block-content">
			<?php
				$comments_args = array(
					'label_submit'				=>	esc_html__( 'Send Comment', 'adifier' ),
					'title_reply'				=>	'',
					'fields'					=>	apply_filters( 'comment_form_default_fields', array(
														'author' 	=> '<div class="row"><div class="col-sm-4">
																			<label for="name">'.esc_html__( 'Name *', 'adifier' ).'</label>
																			<input type="text" id="name" name="author" class="form-control required" placeholder="'.esc_attr__( 'Write your name', 'adifier' ).'">
																		</div>',
														'email'	 	=> '<div class="col-sm-4">
																			<label for="email">'.esc_html__( 'Email *', 'adifier' ).'</label>
																			<input type="text" id="email" name="email" class="form-control required" placeholder="'.esc_attr__( 'Write your email', 'adifier' ).'">
																		</div>',
														'url' 		=> '<div class="col-sm-4">
																			<label for="url">'.esc_html__( 'URL', 'adifier' ).'</label>
																			<input type="text" id="url" name="url" class="form-control required" placeholder="'.esc_attr__( 'Add your URL', 'adifier' ).'">
														  				</div></div>',
													)),
					'comment_field'				=>	'<label for="comment">'.esc_html__( 'Comment *', 'adifier' ).'</label><textarea rows="10" cols="100" id="comment" name="comment" class="form-control required" placeholder="'.esc_attr__( 'Your comment goes here...', 'adifier' ).'"></textarea>',
					'cancel_reply_link' 		=> esc_html__( 'or cancel reply', 'adifier' ),
					'logged_in_as'				=> '',
					'title_reply_before'		=> '<p id="reply-title" class="comment-reply-title">',
					'title_reply_after'			=> '</p>',
					'cancel_reply_before'		=> ' ',
					'cancel_reply_after'		=> '',
					'comment_notes_after' 		=> '<div class="alert-error hidden comment-required-fields">'.esc_html__( 'Fields marked with * are required', 'adifier' ).'</div><div class="alert-error hidden comment-required-email">'.esc_html__( 'Email is invalid', 'adifier' ).'</div>',
					'comment_notes_before' 		=> '',
					'must_log_in'				=> '<p class="must-log-in">'.esc_html__( 'You must', 'adifier' ).' <a href="#register" class="af-modal">'.esc_html__( 'Register', 'adifier' ).'</a> '.esc_html__( 'or', 'adifier' ).' <a href="#login" class="af-modal">'.esc_html__( 'Login', 'adifier' ).'</a> '.esc_html__( 'to post a comment', 'adifier' )
				);
				comment_form( $comments_args );	
			?>			
		</div>
	</div>

<?php endif; ?>