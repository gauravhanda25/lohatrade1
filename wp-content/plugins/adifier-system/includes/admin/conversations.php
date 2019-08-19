<div class="wrap">
	<h2><?php esc_html_e( 'Conversations', 'adifier' ) ?> </h2>

	<?php echo !empty( $message ) ? $message : ''; ?>
	
	<form id="posts-filter" method="post" action="<?php echo esc_url( add_query_arg(null, null) ); ?>">

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'adifier' ) ?></label>
				<select name="action" id="bulk-action-selector-top">
					<option value="" selected="selected"><?php _e( 'Bulk Actions', 'adifier' ) ?></option>
					<option value="delete"><?php _e( 'Delete', 'adifier' ) ?></option>
				</select>
				<input type="submit" id="doaction" class="button action" value="Apply">
			</div>
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Conversations:', 'adifier' ) ?></label>
				<input id="post-search-input" name="s" value="" type="search">
				<input id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'adifier' ) ?>" type="submit">
			</p>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td scope="col" id="cb" class="manage-column column-cb check-column" style="">
						<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'adifier' ) ?></label>
						<input id="cb-select-all-1" type="checkbox">
					</td>
					<td scope="col" id="title" class="manage-column column-title column-primary" style="">
						<?php esc_html_e( 'Title', 'adifier' ) ?>
					</td>					
				</tr>
			</thead>

			<tbody id="the-list">
				<?php
				if( !empty( $conversations ) ){
					foreach( $conversations as $conversation ){
						?>
						<tr class="hentry alternate">
							<th scope="row" class="check-column">
								<input id="cb-select-<?php echo esc_attr( $conversation->con_id ) ?>" type="checkbox" name="con_ids[]" value="<?php echo esc_attr( $conversation->con_id ) ?>">
								<div class="locked-indicator"></div>
							</th>
							<td class="title column-title column-primary page-title">
								<a href="<?php echo esc_url( add_query_arg( array('con_id' => $conversation->con_id) ) ) ?>">
									<?php  echo esc_html__( 'Conversation between', 'adifier' ).' '.get_the_author_meta( 'display_name', $conversation->sender_id ).' '.esc_html__( 'and', 'adifier' ).' '.get_the_author_meta( 'display_name', $conversation->recipient_id ); ?>
								</a>
							</td>
						</tr>
						<?php
					}
				}
				else{ ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="2">
							<?php _e( 'No conversations found.', 'adifier' ) ?>
						</td>
					</tr>				
				<?php 
				}
				?>
			</tbody>
		</table>
	</form>
	<?php
		if( !empty( $pagination ) ){
			echo '<div class="con-pagination">'.$pagination.'</div>';
		}
	?>	
	<br class="clear">
</div>