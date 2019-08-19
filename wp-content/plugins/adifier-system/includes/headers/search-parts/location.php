<?php 
$location_search = adifier_get_option( 'location_search' );
$use_google_location = adifier_get_option( 'use_google_location' );
$use_predefined_locations = adifier_get_option( 'use_predefined_locations' );
?>

<?php if( $use_google_location !== 'no' || $use_predefined_locations !== 'no' ): ?>

	<?php if( !empty( $is_labeled ) ): ?>
		<div>
			<label for="location"><?php esc_html_e( 'Location', 'adifier' ) ?></label>		
	<?php endif; ?>

	<?php if( $location_search == 'geo' ): ?>
		<div class="header-location">
			<input type="text" class="form-control place-lookup location" name="location" placeholder="<?php esc_attr_e( 'Located in...', 'adifier' ) ?>">
			<?php
			$map_source = adifier_get_option( 'map_source' );
			if( $map_source == 'google' ):
			?>
				<input type="hidden" name="latitude" class="latitude">
				<input type="hidden" name="longitude" class="longitude">
			<?php
			endif;
			?>
		</div>
	<?php else: ?>
		<div class="styled-select header-location">
			<?php $location_ids = adifier_get_taxonomy_hierarchy( 'advert-location', 0, true ); ?>
			<select name="location_id">
				<option value=""><?php esc_html_e( 'Where', 'adifier' ) ?></option>
				<?php
				if( !empty( $location_ids ) ){
					addifier_hierarchy_select_taxonomy( $location_ids, 0, array(), true );
				}
				?>
			</select>
		</div>		
	<?php endif; ?>

	<?php if( !empty( $is_labeled ) ): ?>
		</div>
	<?php endif; ?>

<?php endif; ?>