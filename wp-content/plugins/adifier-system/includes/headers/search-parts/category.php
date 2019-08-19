<div class="styled-select">
	<?php $categories = adifier_get_taxonomy_hierarchy( 'advert-category', 0, true ); ?>
	<select name="category">
		<option value=""><?php esc_html_e( 'In Category', 'adifier' ) ?></option>
		<?php
		if( !empty($categories) ){
			addifier_hierarchy_select_taxonomy( $categories, 0, array(), true );
		}
		?>
	</select>
</div>