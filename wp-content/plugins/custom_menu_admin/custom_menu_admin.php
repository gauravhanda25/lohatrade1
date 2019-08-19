<?php
/**
 * @package Custom_menu_admin
 * @version 1.0
 */
/*
Plugin Name: Custom menu admin
Plugin URI: http://wordpress.org/extend/plugins/#
Description: This is an example plugin
Author: Carlo Daniele
Version: 1.0
Author URI: http://carlodaniele.it/en/
*/
/**
 * Add menu meta box
 *
 * @param object $object The meta box object
 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function custom_add_menu_meta_box( $object ) {
	add_meta_box( 'custom-menu-metabox', __( 'Ads Categories' ), 'custom_menu_meta_box', 'nav-menus', 'side', 'default' );
	return $object;
}
add_filter( 'nav_menu_meta_box_object', 'custom_add_menu_meta_box', 10, 1);
/**
 * Displays a metabox for authors menu item.
 *
 * @global int|string $nav_menu_selected_id (id, name or slug) of the currently-selected menu
 *
 * @link https://core.trac.wordpress.org/browser/tags/4.5/src/wp-admin/includes/nav-menu.php
 * @link https://core.trac.wordpress.org/browser/tags/4.5/src/wp-admin/includes/class-walker-nav-menu-edit.php
 * @link https://core.trac.wordpress.org/browser/tags/4.5/src/wp-admin/includes/class-walker-nav-menu-checklist.php
 */
function custom_menu_meta_box(){
	global $nav_menu_selected_id;
	$walker = new Walker_Nav_Menu_Checklist();
	
	$current_tab = 'all';
	$authors = $terms = get_terms(array('taxonomy'   => 'advert-category','hide_empty' => false,));
	$admins = array();
	if ( isset( $_REQUEST['authorarchive-tab'] ) && 'admins' == $_REQUEST['authorarchive-tab'] ) {
		$current_tab = 'admins';
	}elseif ( isset( $_REQUEST['authorarchive-tab'] ) && 'all' == $_REQUEST['authorarchive-tab'] ) {
		$current_tab = 'all';
	}
	/* set values to required item properties */
	foreach ( $authors as &$author ) {
		$author->classes = array();
		//$author->type = 'custom';
		$author->object_id = $author->ID;
		$author->title = $author->name;
		$author->object = 'custom';
		//$author->url = get_author_posts_url( $author->ID ); 
		$author->attr_title = $author->name;
		
	}
	$removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
	?>
	<div id="authorarchive" class="categorydiv">
		<ul id="authorarchive-tabs" class="authorarchive-tabs add-menu-item-tabs">
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
				<a class="nav-tab-link" data-type="tabs-panel-authorarchive-all" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'authorarchive-tab', 'all', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-authorarchive-all">
					<?php _e( 'View All' ); ?>
				</a>
			</li><!-- /.tabs -->

			<!-- <li <?php echo ( 'admins' == $current_tab ? ' class="tabs"' : '' ); ?>>
				<a class="nav-tab-link" data-type="tabs-panel-authorarchive-admins" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'authorarchive-tab', 'admins', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-authorarchive-admins">
					<?php _e( 'Admins' ); ?>
				</a>
			</li> --><!-- /.tabs -->

		</ul>
		
		<div id="tabs-panel-authorarchive-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
			<ul id="authorarchive-checklist-all" class="categorychecklist form-no-clear">
			<?php
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $authors), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<div id="tabs-panel-authorarchive-admins" class="tabs-panel tabs-panel-view-admins <?php echo ( 'admins' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
			<ul id="authorarchive-checklist-admins" class="categorychecklist form-no-clear">
			<?php
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $admins), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'authorarchive-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#authorarchive" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-authorarchive-menu-item" id="submit-authorarchive" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}