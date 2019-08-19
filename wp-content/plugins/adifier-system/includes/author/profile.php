<?php
$author_meta = array(
	'phone'			=> get_user_meta( $author->ID, 'phone', true ),
	'facebook'		=> get_user_meta( $author->ID, 'facebook', true ),
	'twitter'		=> get_user_meta( $author->ID, 'twitter', true ),
	'youtube'		=> get_user_meta( $author->ID, 'youtube', true ),
	'linkedin'		=> get_user_meta( $author->ID, 'linkedin', true ),
	'instagram'		=> get_user_meta( $author->ID, 'instagram', true ),
	'gst'			=> get_user_meta( $author->ID, 'gst', true ),
	'firm_name'		=> get_user_meta( $author->ID, 'firm_name', true ),	
	'description'	=> get_user_meta( $author->ID, 'description', true ),	
	'af_location_id'=> get_user_meta( $author->ID, 'af_location_id', true ),	
);
$location = get_user_meta( $author->ID, 'location', true );
if( empty( $location ) ){
	$location = array(
		'lat' 		=> '',
		'long' 		=> '',
		'country' 	=> '',
		'state' 	=> '',
		'city' 		=> '',
		'street' 	=> '',
	);
}

extract( $author_meta );

$mandatory_fields = adifier_get_option( 'mandatory_fields' );
?>
<div class="author-panel">
	<div class="row">
		<div class="col-sm-7">
			<div class="white-block white-block-extra-padding">
				<div class="white-block-title">
					<h5><?php esc_html_e( 'Account Details', 'adifier' ) ?></h5>
				</div>
				<div class="white-block-content">
					<form class="ajax-form">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="avatar"><?php esc_html_e( 'Select new avatar', 'adifier' ) ?></label>
									<input type="file" name="avatar" id="avatar">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="email"><?php esc_html_e( 'Email *', 'adifier' ) ?></label>
									<input type="text" name="email" id="email" value="<?php echo esc_attr( $author->user_email ) ?>" placeholder="<?php esc_attr_e( 'Your registered email', 'adifier' ); ?>">
								</div>								
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="first_name"><?php esc_html_e( 'First Name *', 'adifier' ) ?></label>
									<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $author->first_name ) ?>" placeholder="<?php esc_attr_e( 'Or your company name', 'adifier' ); ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="last_name"><?php esc_html_e( 'Last Name', 'adifier' ) ?></label>
									<input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $author->last_name ) ?>" placeholder="<?php esc_attr_e( 'Or your company name', 'adifier' ); ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="firm_name"><?php esc_html_e( 'Firm Name', 'adifier' ) ?></label>
									<input type="text" name="firm_name" id="firm_name" value="<?php echo esc_attr( $firm_name ); ?>" placeholder="<?php esc_attr_e( 'Firm Name', 'adifier' ); ?>">
								</div>								
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="gst"><?php esc_html_e( 'GST No.', 'adifier' ) ?></label>
									<input type="text" name="gst" id="gst" value="<?php echo esc_attr( $gst ); ?>" placeholder="<?php esc_attr_e( 'GST No.', 'adifier' ); ?>">
								</div>								
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="phone"><?php $mandatory_fields['phone'] == 1 ? esc_html_e( 'Phone *', 'adifier' ) : esc_html_e( 'Phone', 'adifier' ) ?></label>
									<input type="text" name="phone" id="phone" value="<?php echo esc_attr( $phone ); ?>" placeholder="<?php esc_attr_e( 'It is revealed on click so it is safe from spam', 'adifier' ); ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="facebook"><?php esc_html_e( 'Facebook', 'adifier' ) ?></label>
									<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( $facebook ); ?>" placeholder="<?php esc_attr_e( 'https://www.facebook.com/...', 'adifier' ); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="twitter"><?php esc_html_e( 'Twitter', 'adifier' ) ?></label>
									<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( $twitter ); ?>" placeholder="<?php esc_attr_e( 'https://twitter.com/...', 'adifier' ); ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="youtube"><?php esc_html_e( 'YouTube', 'adifier' ) ?></label>
									<input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( $youtube ); ?>" placeholder="<?php esc_attr_e( 'https://www.youtube.com/channel/...', 'adifier' ); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="linkedin"><?php esc_html_e( 'LinkedIn', 'adifier' ) ?></label>
									<input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( $linkedin ); ?>" placeholder="<?php esc_attr_e( 'https://linkedin.com/in/...', 'adifier' ); ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="instagram"><?php esc_html_e( 'Instagram', 'adifier' ) ?></label>
									<input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="<?php esc_attr_e( 'https://www.instagram.com/...', 'adifier' ); ?>">
								</div>								
							</div>
							
						</div>

						<div class="form-group">
							<label for="description"><?php esc_html_e( 'Description', 'adifier' ) ?></label>
							<textarea class="form-control" name="description" id="description" placeholder="<?php esc_attr_e( 'Write something about yourself', 'adifier' ); ?>"><?php echo $description ?></textarea>
						</div>	

                        <?php
                        if( adifier_get_option( 'use_predefined_locations' ) == 'yes' ){
	                        $locations = adifier_get_taxonomy_hierarchy( 'advert-location' );
	                        if( !empty( $locations ) ){
	                            ?>
	                            <div class="form-group">
	                                <label for="location_id"><?php esc_html_e( 'Location *', 'adifier' ) ?></label>
                                    <select name="location_id" id="location_id" class="select2-enabled">
                                        <option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
                                        <?php
                                        if( !empty( $locations ) ){
                                            addifier_hierarchy_select_taxonomy( $locations, 0, $af_location_id );
                                        }
                                        ?>
                                    </select>
	                            </div>                      
	                            <?php
	                        }
	                    }
                        ?>						

                        <?php if( adifier_get_option( 'use_google_location' ) == 'yes' ): ?>
							<div class="adifier-map">
								<label><?php esc_html_e( 'Precise Location *', 'adifier' ) ?></label>
								<?php adifier_get_map_autocomplete_html() ?>
								<div class="map-holder"></div>

								<input type="hidden" name="lat" value="<?php echo esc_attr( $location['lat'] ) ?>">
								<input type="hidden" name="long" value="<?php echo esc_attr( $location['long'] ) ?>">

								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="country"><?php esc_html_e( 'Country', 'adifier' ) ?></label>
											<input type="text" name="country" id="country" value="<?php echo esc_attr( $location['country'] ) ?>" placeholder="<?php esc_attr_e( 'Populated on Google place select', 'adifier' ); ?>">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="state"><?php esc_html_e( 'State', 'adifier' ) ?></label>
											<input type="text" name="state" id="state" value="<?php echo esc_attr( $location['state'] ) ?>" placeholder="<?php esc_attr_e( 'Populated on Google place select', 'adifier' ); ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="city"><?php esc_html_e( 'City', 'adifier' ) ?></label>
											<input type="text" name="city" id="city" value="<?php echo esc_attr( $location['city'] ) ?>" placeholder="<?php esc_attr_e( 'Populated on Google place select', 'adifier' ); ?>">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="street"><?php esc_html_e( 'Street', 'adifier' ) ?></label>
											<input type="text" name="street" id="street" value="<?php echo esc_attr( $location['street'] ) ?>" placeholder="<?php esc_attr_e( 'Populated on Google place select', 'adifier' ); ?>">
										</div>
									</div>
								</div>

							</div>
						<?php  endif; ?>

						<input type="hidden" name="action" value="adifier_update_account">
						
						<div class="ajax-form-result af-button-align-margin"></div>
						<div class="text-right">
							<a href="javascript:void(0);" class="submit-ajax-form af-button"><?php esc_html_e( 'Update Account', 'adifier' ) ?></a>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-sm-5">
			<div class="white-block white-block-extra-padding">
				<div class="white-block-title">
					<h5><?php esc_html_e( 'Change Password', 'adifier' ) ?></h5>
				</div>
				<div class="white-block-content">
					<form class="ajax-form">

						<div class="form-group relative-wrap">
							<label for="old_password"><?php esc_html_e( 'Old Password *', 'adifier' ) ?></label>
							<input type="password" name="old_password" class="reveal-password" id="old_password" value="" placeholder="<?php esc_attr_e( 'Your current password', 'adifier' ); ?>">
							<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
						</div>

						<div class="form-group relative-wrap">
							<label for="new_password"><?php esc_html_e( 'New Password *', 'adifier' ) ?></label>
							<input type="password" name="new_password" id="new_password" class="reveal-password" value="" placeholder="<?php esc_attr_e( 'Your desired new password', 'adifier' ); ?>">
							<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
						</div>

						<div class="form-group relative-wrap">
							<label for="new_password_repeat"><?php esc_html_e( 'Confirm New Password *', 'adifier' ) ?></label>
							<input type="password" name="new_password_repeat" class="reveal-password" id="new_password_repeat" value="" placeholder="<?php esc_attr_e( 'Your desired new password', 'adifier' ); ?>">
							<a href="javascript:;" title="<?php esc_attr_e( 'View Password', 'adifier' ) ?>" class="toggle-password"><i class="aficon-eye"></i></a>
						</div>

						<input type="hidden" name="action" value="adifier_change_password">

						<div class="ajax-form-result af-button-align-margin">&nbsp;</div>
						<div class="text-right">
							<a href="javascript:void(0);" class="submit-ajax-form af-button"><?php esc_html_e( 'Change Password', 'adifier' ) ?></a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>