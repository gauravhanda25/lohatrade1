<?php
if( !class_exists('Adifier_Admin_Custom_Fields') ){
class Adifier_Admin_Custom_Fields{
	/*
	List of the forbidden slugs for custom fields
	*/ 
	private static $reserved_terms = array(
		'attachment',
		'attachment_id',
		'author',
		'action',
		'author_name',
		'calendar',
		'cat',
		'category',
		'category__and',
		'category__in',
		'category__not_in',
		'category_name',
		'comments_per_page',
		'comments_popup',
		'cpage',
		'day',
		'debug',
		'error',
		'exact',
		'feed',
		'hour',
		'link_category',
		'm',
		'minute',
		'monthnum',
		'more',
		'name',
		'nav_menu',
		'nopaging',
		'offset',
		'order',
		'orderby',
		'p',
		'page',
		'page_id',
		'paged',
		'pagename',
		'pb',
		'perm',
		'post',
		'post__in',
		'post__not_in',
		'post_format',
		'post_mime_type',
		'post_status',
		'post_tag',
		'post_type',
		'posts',
		'posts_per_archive_page',
		'posts_per_page',
		'preview',
		'robots',
		's',
		'search',
		'second',
		'sentence',
		'showposts',
		'static',
		'subpost',
		'subpost_id',
		'tag',
		'tag__and',
		'tag__in',
		'tag__not_in',
		'tag_id',
		'tag_slug__and',
		'tag_slug__in',
		'taxonomy',
		'tb',
		'term',
		'type',
		'w',
		'withcomments',
		'withoutcomments',
		'year',
	);
	/*
	Page slug
	*/ 
	private static $base_url = '';

	/**
	 * Handles output of the custom fields page in admin.
	 *
	 * Shows the created custom fields and lets you add new ones or edit existing ones.
	 * The added custom fields are stored in the database and can be used for layered navigation.
	 */
	public static function launch(){
		self::$base_url = admin_url('admin.php?page=adifier-cf');
		add_action( 'init', 'Adifier_Admin_Custom_Fields::register_taxonomies' );		
		if( !empty( $_GET['panel'] ) && $_GET['panel'] == 'p_add_cf' ){
			add_action( 'admin_enqueue_scripts', 'Adifier_Admin_Custom_Fields::enqueue_scripts');
		}
		add_action( 'admin_menu', 'Adifier_Admin_Custom_Fields::menu_items', 9 );
		add_action( 'wp_ajax_adifier_save_cf_order', 'Adifier_Admin_Custom_Fields::save_cf_order' );
	}

	/*
	* Start of the output based on input criteia
	*/ 
	public static function output() {

		$result = '';
		$panel = !empty( $_GET['panel'] ) ? $_GET['panel'] : '';

		// Action to perform: add, edit, delete or none
		if ( !empty( $_POST['add_cf'] ) ) {
			$result = self::process_add_custom_field();
		} 
		elseif ( !empty( $_POST['edit_cf'] ) ) {
			$result = self::process_edit_custom_field();
		} 
		elseif ( !empty( $_GET['delete_cf'] ) ) {
			$result = self::process_delete_custom_field();
		}
		elseif ( !empty( $_POST['add_group'] ) ){
			$result = self::process_add_group();
		}
		elseif ( !empty( $_POST['edit_group'] ) ){
			$result = self::process_edit_group();
		}
		elseif ( !empty( $_GET['delete_group'] ) ){
			$result = self::process_delete_group();
		}

		if ( is_wp_error( $result ) ) {
			echo '<div id="adifier_errors" class="error"><p>' . $result->get_error_message() . '</p></div>';
		}


		/* Show admin interface */
		switch ( $panel ){
			case 'p_add_cf' 		: self::add_custom_field(); break;
			case 'p_edit_cf' 		: self::edit_custom_field(); break;
			case 'p_edit_group' 	: self::edit_group(); break;
			default  				: self::add_group(); break;
		}
	}
	/*
	* Add link to custom fields panel in the admin side menu
	*/ 
	public static function menu_items(){
		adifier_menu_page( esc_html__( 'Custom Fields', 'adifier' ), esc_html__( 'Custom Fields', 'adifier' ), 'manage_options', 'adifier-cf', 'Adifier_Admin_Custom_Fields::output' );
	}

	/*
	* Add scripts and styles for the custom fields
	*/
	public static function enqueue_scripts(){
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/*
	* Get all groups which will be listed on add_group panel
	*/ 
	private static function get_groups() {
		global $wpdb;
		$groups = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "adifier_cf_groups ORDER BY name ASC;" );

		return $groups;
	}

	/*
	*Retrieve list of registered custom fields under visited group
	*/
	private static function get_custom_fields_by_group() {
		global $wpdb;

		$group_id = absint( $_GET['group_id'] );
		$fields = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}adifier_cf WHERE cf_slug != '' AND group_id = %d ORDER BY cf_order ASC", $group_id ) );

		return $fields;
	}

	/*
	* Convert hierarchical categories to one dimension array
	*/
	private static function get_ad_categories_onedimension( $categories ){
		$list = array();
		foreach( $categories as $category ){
			$list['id_'.$category->term_id] = $category->name;
			if( !empty( $category->children ) ){
				$list = array_merge( $list, self::get_ad_categories_onedimension( $category->children ) );
			}
		}

		return $list;
	}

	/*
	* Get all categories so they can be added to multiselect on add/edit group panel
	*/ 
	private static function get_ad_categories(){
		return adifier_get_taxonomy_hierarchy( 'advert-category' );		
	}

	/*
	* Display of the landing/add group panel
	*/ 
	private static function add_group(){
		?>
		<div class="wrap adifier">
			<h1><?php echo get_admin_page_title(); ?></h1>
			<?php 
			$categories = self::get_ad_categories(); 
			if( !empty( $categories ) ){
				$categories_list = self::get_ad_categories_onedimension( $categories );
			}
			?>
			<br class="clear" />
			<div id="col-container">
				<div id="col-right">
					<div class="col-wrap">
						<table class="widefat custom-fields-table wp-list-table ui-sortable" style="width:100%">
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Name', 'adifier' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Categories', 'adifier' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( $groups = self::get_groups() ) :
										foreach ( $groups as $group ) :
											$group_categories = explode( ',', $group->categories );
											?><tr>
												<td>
													<strong>
														<a href="<?php echo esc_url( add_query_arg( array( 'panel' => 'p_add_cf', 'group_id' => $group->group_id ), self::$base_url ) ); ?>"><?php echo esc_html( $group->name ); ?></a>
													</strong>

													<div class="row-actions">
														<span class="edit">
															<a href="<?php echo esc_url( add_query_arg( array( 'panel' => 'p_edit_group', 'group_id' => $group->group_id ), self::$base_url ) ); ?>">
																<?php esc_html_e( 'Edit', 'adifier' ); ?>
															</a> | </span>
														<span class="delete">
															<a class="delete" data-confirm="<?php esc_attr_e( "Are you sure you want to delete this group?", "adifier" ); ?>" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'delete_group', $group->group_id, self::$base_url ), 'adifier-delete-group' . $group->group_id ) ); ?>">
																<?php esc_html_e( 'Delete', 'adifier' ); ?>
															</a>
														</span>
													</div>
												</td>
												<td>
													<?php
													foreach( $group_categories as &$group_category ){
														if( !empty( $categories_list['id_'.$group_category] ) ){
															$group_category = $categories_list['id_'.$group_category];
														}
													}

													echo implode(', ', $group_categories);
													?>
												</td>
											</tr><?php
										endforeach;
									else :
										?><tr><td colspan="6"><?php esc_html_e( 'No groups currently exist.', 'adifier' ) ?></td></tr><?php
									endif;
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<h2><?php esc_html_e( 'Add new field group', 'adifier' ); ?></h2>
							<p><?php esc_html_e( 'Custom fields allows more precise search for your visitors', 'adifier' ); ?></p>
							<form action="<?php echo esc_url( add_query_arg( 'action', 'add_group', self::$base_url ) ); ?>" method="post">

								<div class="form-field">
									<label for="name"><?php esc_html_e( 'Name', 'adifier' ); ?></label>
									<input name="name" id="name" type="text" value="" />
									<p class="description"><?php esc_html_e( 'Name for the custom fields group.', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="categories"><?php esc_html_e( 'Categories', 'adifier' ); ?></label>
									<select name="categories[]" id="categories" multiple="multiple" class="widefat height-200">
										<?php self::_multiselect_taxonomy( $categories ); ?>
									</select>
									<p class="description"><?php esc_html_e( 'Select on which categories to show fields from this group. Fields are show only on displayed category so selecting only parent will not show fields on it\'s children', 'adifier' ); ?></p>
								</div>							

								<p class="submit"><input type="submit" name="add_group" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Group', 'adifier' ); ?>"></p>
								<?php wp_nonce_field( 'adifier-add-group' ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Edit group admin panel
	 *
	 */
	public static function edit_group() {
		global $wpdb;

		$group_id = absint( $_GET['group_id'] );

		$group = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}adifier_cf_groups WHERE group_id = %d", $group_id) );

		?>
		<div class="wrap adifier">
			<h1><?php esc_html_e( 'Edit Group', 'adifier' ) ?></h1>

			<?php
				if ( ! $group ) {
					echo '<div id="adifier_errors" class="error"><p>' . esc_html__( 'Error: non-existing roup ID.', 'adifier' ) . '</p></div>';
				} 
				else {
				?>

				<form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
					<table class="form-table">
						<tbody>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="name"><?php esc_html_e( 'Name', 'adifier' ); ?></label>
								</th>
								<td>
									<input name="name" id="name" type="text" value="<?php echo esc_attr( $group->name ); ?>" />
									<p class="description"><?php esc_html_e( 'Name for the custom fields group.', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="categories"><?php esc_html_e( 'Categories', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="categories[]" id="categories" multiple="multiple" class="widefat height-200">
										<?php  self::_multiselect_taxonomy( self::get_ad_categories(), 0, explode( ',', $group->categories ) ); ?>
									</select>
									<p class="description"><?php esc_html_e( 'Select on which categories to show fields from this group. Fields are show only on displayed category so selecting only parent will not show fields on it\'s children', 'adifier' ); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="submit"><input type="submit" name="edit_group" id="submit" class="button-primary" value="<?php esc_attr_e( 'Update', 'adifier' ); ?>"></p>
					<?php wp_nonce_field( 'adifier-edit-group' . $group_id ); ?>
				</form>
			<?php } ?>
		</div>
		<?php
	}

	/*
	* List categories for multiselect hierarchy style
	*/
	static private function _multiselect_taxonomy( $terms, $depth = 0, $selected = array() ){
		foreach( $terms as $term ){
			echo '<option value="'.esc_attr( $term->term_id ).'" '.( in_array( trim( $term->term_id ), $selected ) ? 'selected="selected"' : '' ).'>'.str_repeat('&nbsp;', $depth).$term->name.'</option>';
			if( !empty( $term->children ) ){
				self::_multiselect_taxonomy( $term->children, $depth + 2, $selected );
			}
		}
	}

	/*
	* First delete taxononmy terms, then custom fields and then delete group
	*/ 
	private static function process_delete_group(){
		global $wpdb;

		$group_id = absint( $_GET['delete_group'] );
		check_admin_referer( 'adifier-delete-group' . $group_id );

		$slugs = $wpdb->get_col( $wpdb->prepare( "SELECT cf_slug FROM {$wpdb->prefix}adifier_cf WHERE group_id = %d", $group_id ) );
		if( !empty( $slugs ) ){
			foreach( $slugs as $slug ){
				self::delete_taxonomy( $slug );
			}
		}

		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}adifier_cf WHERE group_id = %d", $group_id ) );
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}adifier_cf_groups WHERE group_id = %d", $group_id ) );
		
		return true;
	}

	/*
	* Check group name and cats selection before adding/editing it
	*/ 
	private static function get_posted_group(){
		if( empty( $_POST['name'] ) ){
			return new WP_Error( 'missing_group_name', esc_html__( 'Please, provide name for the group.', 'adifier' ), array( 'status' => 400 ) );
		}
		else if( empty( $_POST['categories'] ) ){
			return new WP_Error( 'missing_group_cats', esc_html__( 'Please, select categories on whch to show this group of custom fields.', 'adifier' ), array( 'status' => 400 ) );	
		}
		else{
			return array(
				'name' => esc_sql( $_POST['name'] ),
				'categories' => implode( ',', $_POST['categories'] )
			);
		}
	}

	/*
	* Start of the adding group to database
	*/ 
	private static function process_add_group(){
		global $wpdb;

		check_admin_referer( 'adifier-add-group' );

		$group = self::get_posted_group();

		if( is_wp_error( $group ) ){
			return $group;
		}

		$results = $wpdb->insert(
			$wpdb->prefix.'adifier_cf_groups',
			$group,
			array(
				'%s',
				'%s'
			)
		);

		if ( is_wp_error( $results ) ) {
			return new WP_Error( 'cannot_create_group', $results->get_error_message(), array( 'status' => 400 ) );
		}

		return true;
	}

	/*
	* Start of the group updating
	*/ 
	private static function process_edit_group(){
		global $wpdb;

		$group_id = absint( $_GET['group_id'] );
		check_admin_referer( 'adifier-edit-group' . $group_id );

		$group = self::get_posted_group();
		if( is_wp_error( $group ) ){
			return $group;
		}

		$results = $wpdb->update(
			$wpdb->prefix.'adifier_cf_groups',
			$group,
			array(
				'group_id' => $group_id
			),
			array(
				'%s',
				'%s'
			),
			array(
				'%d'
			)
		);

		if ( is_wp_error( $results ) ) {
			return new WP_Error( 'cannot_update_group', $results->get_error_message(), array( 'status' => 400 ) );
		}		

		echo '<div class="updated"><p>' . esc_html__( 'Group updated successfully', 'adifier' ) . '</p><p><a href="' . esc_url( self::$base_url ) . '">' . esc_html__( 'Back to Groups', 'adifier' ) . '</a></p></div>';

		return true;

	}

	/**
	 * Add Custom Field admin panel.
	 *
	 * Shows the interface for adding new custom fields.
	 */
	public static function add_custom_field() {
		?>
		<div class="wrap adifier">
			<h1><?php echo get_admin_page_title(); ?></h1>

			<br class="clear" />
			<div id="col-container">
				<div id="col-right">
					<div class="col-wrap">
						<table class="widefat custom-fields-table wp-list-table ui-sortable" style="width:100%">
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Name', 'adifier' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Slug', 'adifier' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Type', 'adifier' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Values', 'adifier' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Order', 'adifier' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( $fields = self::get_custom_fields_by_group() ) :
										foreach ( $fields as $field ) :
											?><tr>
												<td>
													<strong><a href="edit-tags.php?taxonomy=<?php echo esc_attr( $field->cf_slug ); ?>&amp;post_type=advert"><?php echo esc_html( $field->cf_label ); ?></a></strong>

													<div class="row-actions">
														<span class="edit">
															<a href="<?php echo esc_url( add_query_arg( array( 'panel' => 'p_edit_cf', 'group_id' => absint( $_GET['group_id'] ), 'cf_id' => $field->cf_id ), self::$base_url ) ); ?>">
																<?php esc_html_e( 'Edit', 'adifier' ); ?>
															</a> | </span>
														<span class="delete">
															<a class="delete" data-confirm="<?php esc_attr_e( "Are you sure you want to delete this custom field?", "adifier" ); ?>" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'panel' => 'p_add_cf', 'group_id' => absint( $_GET['group_id'] ), 'delete_cf' => $field->cf_id ), self::$base_url ), 'adifier-delete-cf' . $field->cf_id ) ); ?>">
																<?php esc_html_e( 'Delete', 'adifier' ); ?>
															</a>
														</span>
													</div>
												</td>
												<td><?php echo esc_html( $field->cf_slug ); ?></td>
												<td>
													<?php
													switch( $field->cf_type ){
														case 1: esc_html_e( 'Multiple', 'adifier' ); break;
														case 2: esc_html_e( 'Single', 'adifier' ); break;
														case 3: esc_html_e( 'Date', 'adifier' ); break;
														case 4: esc_html_e( 'Range Slider', 'adifier' ); break;
														case 5: esc_html_e( 'Nested', 'adifier' ); break;
														case 6: esc_html_e( 'Color', 'adifier' ); break;
														case 7: esc_html_e( 'Multiple Colors', 'adifier' ); break;
														case 8: esc_html_e( 'Range Inputs', 'adifier' ); break;
														case 9: esc_html_e( 'Checkboxes', 'adifier' ); break;
														case 10: esc_html_e( 'Radio Buttons', 'adifier' ); break;
													}
													?>
												</td>
												<td class="custom-field-terms">
													<a href="edit-tags.php?taxonomy=<?php echo esc_attr( $field->cf_slug ); ?>&amp;post_type=advert" class="configure-terms"><?php esc_html_e( 'Configure terms', 'adifier' ); ?></a>
												</td>
												<td>
													<div class="cf-order-handle"><i class="dashicons dashicons-move"></i></div>
													<input type="hidden" name="cf_order" value="<?php echo esc_attr( $field->cf_id ) ?>" class="cf_order">
												</td>
											</tr><?php
										endforeach;
									else :
										?><tr><td colspan="6"><?php esc_html_e( 'No custom fields currently exist.', 'adifier' ) ?></td></tr><?php
									endif;
								?>
							</tbody>
						</table>
						<div class="text-right">
							<a href="javascript:void(0);" class="cf-save-order button button-primary"><?php esc_html_e( 'Save Order', 'adifier' ) ?></a>
						</div>
					</div>
				</div>
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<h2><?php esc_html_e( 'Add new custom field', 'adifier' ); ?></h2>
							<p><?php esc_html_e( 'Custom fields allows more precise search for your visitors', 'adifier' ); ?></p>
							<form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">

								<div class="form-field">
									<label for="cf_label"><?php esc_html_e( 'Name', 'adifier' ); ?></label>
									<input name="cf_label" id="cf_label" type="text" value="" />
									<p class="description"><?php esc_html_e( 'Name for the custom field (shown on the front-end).', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_slug"><?php esc_html_e( 'Slug', 'adifier' ); ?></label>
									<input name="cf_slug" id="cf_slug" type="text" value="" maxlength="28" />
									<p class="description"><?php esc_html_e( 'Unique slug/reference for the custom field; must be no more than 28 characters.', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_orderby"><?php esc_html_e( 'Default sort order', 'adifier' ); ?></label>
									<select name="cf_orderby" id="cf_orderby">
										<option value="none"><?php esc_html_e( 'None', 'adifier' ); ?></option>
										<option value="name"><?php esc_html_e( 'Name', 'adifier' ); ?></option>
										<option value="id"><?php esc_html_e( 'Term ID', 'adifier' ); ?></option>
										<option value="name_num"><?php esc_html_e( 'Name (numeric)', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Determines the sort order of the terms on the frontend filter.', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_description"><?php esc_html_e( 'Field description', 'adifier' ); ?></label>
									<input name="cf_description" id="cf_description" type="text" value="" />
									<p class="description"><?php esc_html_e( 'Small description for the field ( max 255 chars ).', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_type"><?php esc_html_e( 'Field Type', 'adifier' ); ?></label>
									<select name="cf_type" id="cf_type">
										<option value="1"><?php esc_html_e( 'Multiple Value', 'adifier' ); ?></option>
										<option value="2"><?php esc_html_e( 'Single Value', 'adifier' ); ?></option>
										<option value="3"><?php esc_html_e( 'Single Value - Date', 'adifier' ); ?></option>
										<option value="4"><?php esc_html_e( 'Single Value - Numeric ( range slider search )', 'adifier' ); ?></option>
										<option value="5"><?php esc_html_e( 'Nested Values', 'adifier' ); ?></option>
										<option value="6"><?php esc_html_e( 'Color', 'adifier' ); ?></option>
										<option value="7"><?php esc_html_e( 'Multiple Colors', 'adifier' ); ?></option>
										<option value="8"><?php esc_html_e( 'Single Value - Numeric ( range input search )', 'adifier' ); ?></option>
										<option value="9"><?php esc_html_e( 'Checkboxes - multiple values', 'adifier' ); ?></option>
										<option value="10"><?php esc_html_e( 'Radio Buttons - single value', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If type is Nested Values make sure that name of the field is in form LEVEL1|LEVEL2.. ( for example Make|Model )', 'adifier' ); ?></p>
								</div>								

								<div class="form-field">
									<label for="cf_fixed"><?php esc_html_e( 'Field Fixed', 'adifier' ); ?></label>
									<select name="cf_fixed" id="cf_fixed">
										<option value="0"><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1"><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is fixed then users can select only among created values and will not be able to add their own', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_is_hidden"><?php esc_html_e( 'Field Hidden', 'adifier' ); ?></label>
									<select name="cf_is_hidden" id="cf_is_hidden">
										<option value="0"><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1"><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is hidden it will not be displayed in the search filters', 'adifier' ); ?></p>
								</div>

								<div class="form-field">
									<label for="cf_is_mandatory"><?php esc_html_e( 'Field Is Mandatory', 'adifier' ); ?></label>
									<select name="cf_is_mandatory" id="cf_is_mandatory">
										<option value="0"><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1"><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is mandatory in order to save the ad', 'adifier' ); ?></p>
								</div>

								<input type="hidden" name="cf_order" value="<?php echo esc_attr( sizeof( $fields ) + 1 ) ?>">

								<p class="submit"><input type="submit" name="add_cf" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Fields', 'adifier' ); ?>"></p>
								<?php wp_nonce_field( 'adifier-add-cf' ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/*
	* retrieve custom field based on its ID
	*/
	public static function get_custom_field_by_id( $cf_id ){
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}adifier_cf WHERE cf_id = %d", $cf_id) );
	}

	/**
	 * Edit Cutsom Field admin panel.
	 *
	 * Shows the interface for changing a custom field type between select and text.
	 */
	public static function edit_custom_field() {
		global $wpdb;

		$cf_id = absint( $_GET['cf_id'] );

		$field = self::get_custom_field_by_id( $cf_id );
		echo '<div class="notice notice-info"><p><a href="' . esc_url( add_query_arg( array( 'panel' => 'p_add_cf', 'group_id' => absint( $_GET['group_id'] ) ), self::$base_url ) ). '">' . esc_html__( 'Back to Custom Fields', 'adifier' ) . '</a></p></div>';

		?>
		<div class="wrap adifier">
			<h1><?php esc_html_e( 'Edit Custom Field', 'adifier' ) ?></h1>

			<?php
			if ( !$field ) {
				echo '<div id="adifier_errors" class="error"><p>' . esc_html__( 'Error: non-existing custom field ID.', 'adifier' ) . '</p></div>';
			}
			else{
			?>
				<form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
					<table class="form-table">
						<tbody>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_label"><?php esc_html_e( 'Name', 'adifier' ); ?></label>
								</th>
								<td>
									<input name="cf_label" id="cf_label" type="text" value="<?php echo esc_attr( $field->cf_label ); ?>" />
									<p class="description"><?php esc_html_e( 'Name for the custom field (shown on the front-end).', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_slug"><?php _e( 'Slug', 'adifier' ); ?></label>
								</th>
								<td>
									<input name="cf_slug" id="cf_slug" type="text" value="<?php echo esc_attr( $field->cf_slug ); ?>" maxlength="28" />
									<p class="description"><?php esc_html_e( 'Unique slug/reference for the custom field; must be no more than 28 characters.', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_orderby"><?php esc_html_e( 'Default sort order', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="cf_orderby" id="cf_orderby">
										<option value="none" <?php selected( $field->cf_orderby, 'none' ); ?>><?php esc_html_e( 'None', 'adifier' ); ?></option>
										<option value="name" <?php selected( $field->cf_orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'adifier' ); ?></option>
										<option value="id" <?php selected( $field->cf_orderby, 'id' ); ?>><?php esc_html_e( 'Term ID', 'adifier' ); ?></option>
										<option value="name_num" <?php selected( $field->cf_orderby, 'name_num' ); ?>><?php esc_html_e( 'Name (numeric)', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Determines the sort order of the terms on the frontend shop. With None you can use some plugin for custom term ordering', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_description"><?php esc_html_e( 'Field Description', 'adifier' ); ?></label>
								</th>
								<td>
									<input name="cf_description" id="cf_description" type="text" value="<?php echo esc_attr( $field->cf_description ); ?>" />
									<p class="description"><?php esc_html_e( 'Small description for the field ( max 255 chars ).', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_type"><?php esc_html_e( 'Field Type', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="cf_type" id="cf_type">
										<option value="1" <?php selected( $field->cf_type, '1' ); ?>><?php esc_html_e( 'Multiple Values', 'adifier' ); ?></option>
										<option value="2" <?php selected( $field->cf_type, '2' ); ?>><?php esc_html_e( 'Single Value', 'adifier' ); ?></option>
										<option value="3" <?php selected( $field->cf_type, '3' ); ?>><?php esc_html_e( 'Single Value - Date', 'adifier' ); ?></option>
										<option value="4" <?php selected( $field->cf_type, '4' ); ?>><?php esc_html_e( 'Single Value - Numeric ( range slider search )', 'adifier' ); ?></option>
										<option value="5" <?php selected( $field->cf_type, '5' ); ?>><?php esc_html_e( 'Nested Values', 'adifier' ); ?></option>
										<option value="6" <?php selected( $field->cf_type, '6' ); ?>><?php esc_html_e( 'Color', 'adifier' ); ?></option>
										<option value="7" <?php selected( $field->cf_type, '7' ); ?>><?php esc_html_e( 'Multiple Colors', 'adifier' ); ?></option>
										<option value="8" <?php selected( $field->cf_type, '8' ); ?>><?php esc_html_e( 'Single Value - Numeric ( range input search )', 'adifier' ); ?></option>
										<option value="9" <?php selected( $field->cf_type, '9' ); ?>><?php esc_html_e( 'Checkboxes - multiple values', 'adifier' ); ?></option>
										<option value="10" <?php selected( $field->cf_type, '10' ); ?>><?php esc_html_e( 'Radio Buttons - single value', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Select type of the field. If type is Nested Values make sure that name of the field is in form LEVEL1|LEVEL2.. ( for example Make|Model )', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_fixed"><?php esc_html_e( 'Field Fixed', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="cf_fixed" id="cf_fixed">
										<option value="0" <?php selected( $field->cf_fixed, '0' ); ?>><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1" <?php selected( $field->cf_fixed, '1' ); ?>><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is fixed then users can select only among created values and will not be able to add their own', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_is_hidden"><?php esc_html_e( 'Field Hidden', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="cf_is_hidden" id="cf_is_hidden">
										<option value="0" <?php selected( $field->cf_is_hidden, '0' ); ?>><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1" <?php selected( $field->cf_is_hidden, '1' ); ?>><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is hidden it will not be displayed in the search filters', 'adifier' ); ?></p>
								</td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row" valign="top">
									<label for="cf_is_mandatory"><?php esc_html_e( 'Field Is Mandatory', 'adifier' ); ?></label>
								</th>
								<td>
									<select name="cf_is_mandatory" id="cf_is_mandatory">
										<option value="0" <?php selected( $field->cf_is_mandatory, '0' ); ?>><?php esc_html_e( 'No', 'adifier' ); ?></option>
										<option value="1" <?php selected( $field->cf_is_mandatory, '1' ); ?>><?php esc_html_e( 'Yes', 'adifier' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'If field is mandatory in order to save the ad', 'adifier' ); ?></p>
								</td>
							</tr>
							<input type="hidden" name="cf_order" value="<?php echo esc_attr( $field->cf_order ) ?>">
						</tbody>
					</table>
					<p class="submit"><input type="submit" name="edit_cf" id="submit" class="button-primary" value="<?php esc_attr_e( 'Update', 'adifier' ); ?>"></p>
					<?php wp_nonce_field( 'adifier-edit-cf' . $cf_id ); ?>
				</form>
			<?php 
			} 
			?>
		</div>
		<?php
	}

	/**
	 * Get and sanitize posted custom field data.
	 * @return array
	 */
	private static function get_posted_custom_field() {
		if ( empty( $_POST['cf_label'] ) ) {
			return new WP_Error( 'missing_custom_field_name', esc_html__( 'Please, provide custom field name.', 'adifier' ), array( 'status' => 400 ) );
		}

		if ( empty( $_POST['cf_slug'] ) ) {
			$slug = sanitize_title( $_POST['cf_label'] );
		} else {
			$slug = sanitize_title( urldecode( $_POST['cf_slug'] ) );
		}

		// Validate slug.
		if ( strlen( $slug ) >= 28 ) {
			return new WP_Error( 'custom_field_slug_too_long', sprintf( esc_html__( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'adifier' ), $slug ), array( 'status' => 400 ) );
		} 
		else if ( in_array( $slug, self::$reserved_terms ) ) {
			return new WP_Error( 'custom_field_slug_reserved_name', sprintf( esc_html__( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'adifier' ), $slug ), array( 'status' => 400 ) );
		} 
		else if ( !isset($_POST['edit_cf']) && taxonomy_exists( $slug ) ) {
			return new WP_Error( 'custom_field_slug_already_exists', sprintf( esc_html__( 'Slug "%s" is already in use. Change it, please.', 'adifier' ), $slug ), array( 'status' => 400 ) );
		}

		// Validate order by.
		if ( empty( $_POST['cf_orderby'] ) || ! in_array( $_POST['cf_orderby'], array( 'name', 'name_num', 'id', 'none' ), true ) ) {
			$_POST['cf_orderby'] = 'name';
		}

		return array(
			'group_id'			=> $_GET['group_id'],
			'cf_label'   		=> stripcslashes( $_POST['cf_label'] ),
			'cf_slug'    		=> $slug,
			'cf_orderby' 		=> $_POST['cf_orderby'],
			'cf_description' 	=> $_POST['cf_description'],
			'cf_type'  			=> $_POST['cf_type'],
			'cf_fixed'  		=> $_POST['cf_fixed'],
			'cf_order'			=> $_POST['cf_order'],
			'cf_is_hidden'		=> $_POST['cf_is_hidden'],
			'cf_is_mandatory'	=> $_POST['cf_is_mandatory'],
		);
	}

	/**
	 * Add an custom field.
	 *
	 * @return bool|WP_Error
	 */
	private static function process_add_custom_field() {
		global $wpdb;
		check_admin_referer( 'adifier-add-cf' );

		$field = self::get_posted_custom_field();

		if ( is_wp_error( $field ) ) {
			return $field;
		}

		$results = $wpdb->insert(
			$wpdb->prefix.'adifier_cf',
			$field,
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d'
			)
		);

		if ( is_wp_error( $results ) ) {
			return new WP_Error( 'cannot_create_custom_field', $results->get_error_message(), array( 'status' => 400 ) );
		}		

		return true;
	}

	/**
	 * Edit an custom field.
	 *
	 * @return bool|WP_Error
	 */
	private static function process_edit_custom_field() {
		global $wpdb;

		$cf_id = absint( $_GET['cf_id'] );
		check_admin_referer( 'adifier-edit-cf'.$cf_id );

		$field = self::get_posted_custom_field();

		if ( is_wp_error( $field ) ) {
			return $field;
		}

		$saved_field = self::get_custom_field_by_id( $cf_id );

		if( $saved_field->cf_slug !== $field['cf_slug'] && taxonomy_exists( $field['cf_slug'] ) ){
			return new WP_Error( 'custom_field_slug_already_exists', sprintf( esc_html__( 'Slug "%s" is already in use. Change it, please.', 'adifier' ), $field['cf_slug'] ), array( 'status' => 400 ) );
		}

		if( !empty( $saved_field ) ){

			$results = $wpdb->update(
				$wpdb->prefix.'adifier_cf',
				$field,
				array(
					'cf_id' => absint($_GET['cf_id'])
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d'
				),
				array(
					'%d'
				)
			);

			if ( is_wp_error( $results ) ) {
				return new WP_Error( 'cannot_update_custom_field', $results->get_error_message(), array( 'status' => 400 ) );
			}		

			if ( $saved_field->cf_slug !== $field['cf_slug'] ) {
				// Update taxonomies in the wp term taxonomy table.
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'taxonomy' => $field['cf_slug'] ),
					array( 'taxonomy' => $saved_field->cf_slug )
				);
			}

			echo '<div class="updated"><p>' . esc_html__( 'Custom field updated successfully', 'adifier' ) . '</p><p><a href="' . esc_url( add_query_arg( array( 'panel' => 'p_add_cf', 'group_id' => absint( $_GET['group_id'] ) ), self::$base_url ) ). '">' . esc_html__( 'Back to Custom Fields', 'adifier' ) . '</a></p></div>';

		}

		return true;
	}

	/**
	 * Delete an custom fields.
	 *
	 * @return bool
	 */
	private static function process_delete_custom_field() {
		global $wpdb;

		$cf_id = absint( $_GET['delete_cf'] );
		check_admin_referer( 'adifier-delete-cf' . $cf_id );		

		$slug = $wpdb->get_var( $wpdb->prepare( "SELECT cf_slug FROM {$wpdb->prefix}adifier_cf WHERE cf_id = %d", $cf_id ) );

		if ( $slug && $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}adifier_cf WHERE cf_id = %d", $cf_id ) ) ) {
			self::delete_taxonomy( $slug );

			return true;
		}

		return false;
	}

	/**
	* Remove all related to custom fields ( this is common for cusotm field and for deleting groups )
	*/ 
	private static function delete_taxonomy( $slug ){
		if ( taxonomy_exists( $slug ) ) {
			$terms = get_terms( $slug, 'orderby=name&hide_empty=0' );
			foreach ( $terms as $term ) {
				wp_delete_term( $term->term_id, $slug );
			}
		}
	}

	/*
	*Retrieve list of registered custom fields
	*/
	public static function get_custom_fields() {
		global $wpdb;

		$fields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}adifier_cf WHERE cf_slug != '' ORDER BY cf_label ASC" );

		return $fields;
	}	

	/*
	Register  taxonomies
	*/
	public static function register_taxonomies(){
		if( is_admin() ){
			global $pagenow;
			if( !empty( $pagenow ) && $pagenow == 'edit.php' && !empty( $_GET['post_type'] ) && $_GET['post_type'] == 'advert' )	{
				return false;
			}
		}
		
		$fields = self::get_custom_fields();
		if( !empty( $fields ) ){
			foreach( $fields as $field ){
				$label                 = ! empty( $field->cf_label ) ?  $field->cf_label :  $field->cf_slug;
				$taxonomy_data         = array(
					'hierarchical'          => $field->cf_type == 5 ? true : false,
					'labels'                => array(
						'name'              => sprintf( _x( 'Ad %s', 'Ad Custom Fields', 'adifier' ), $label ),
						'singular_name'     => $label,
						'search_items'      => sprintf( __( 'Search %s', 'adifier' ), $label ),
						'all_items'         => sprintf( __( 'All %s', 'adifier' ), $label ),
						'parent_item'       => sprintf( __( 'Parent %s', 'adifier' ), $label ),
						'parent_item_colon' => sprintf( __( 'Parent %s:', 'adifier' ), $label ),
						'edit_item'         => sprintf( __( 'Edit %s', 'adifier' ), $label ),
						'update_item'       => sprintf( __( 'Update %s', 'adifier' ), $label ),
						'add_new_item'      => sprintf( __( 'Add new %s', 'adifier' ), $label ),
						'new_item_name'     => sprintf( __( 'New %s', 'adifier' ), $label ),
						'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'adifier' ), $label ),
					),
					'show_ui'            => true,
					'show_in_quick_edit' => false,
					'show_in_menu'       => false,
					'meta_box_cb'        => false,
					'query_var'          => true,
					'rewrite'            => false,
					'sort'               => false,
					'public'             => true,
					'show_in_nav_menus'  => false,
				);

				register_taxonomy( $field->cf_slug, array( 'advert' ), $taxonomy_data );
			}
		}
	}

	/*
	* Save order of the custom fields
	*/
	public static function save_cf_order(){
		global $wpdb;
		$list = $_POST['list'];
		var_dump( $list );
		$counter = 0;
		if( !empty( $list ) ){
			foreach( $list as $cf_id ){
				$counter++;
				$wpdb->update(
					$wpdb->prefix.'adifier_cf',
					array(
						'cf_order'	=> $counter
					),
					array(
						'cf_id' => absint($cf_id)
					),
					array(
						'%d'
					),
					array(
						'%d'
					)
				);
			}
		}
	}

}
Adifier_Admin_Custom_Fields::launch();
}
?>