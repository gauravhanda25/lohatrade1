<?php

/* Custom Meta For Taxonomies */

/*
* Image upload wrapper
*/
if( !function_exists('adifier_cat_image_uploader') ){
function adifier_cat_image_uploader( $image_id, $name, $description ){
	return '
	<div class="af-image-selection">
		<div class="af-image-holder">
			'.( !empty( $image_id ) ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '' ).'
		</div>
		<input type="hidden" name="'.esc_attr( $name ).'" value="'.( !empty( $image_id ) ? esc_attr( $image_id ) : '' ).'">
		<a href="javascript:void(0);" class="af-image-select button">'.esc_html__( 'Select', 'adifier' ).'</a>
		<a href="javascript:void(0);" class="af-image-remove button">'.esc_html__( 'Remove', 'adifier' ).'</a>
	</div>
	<p class="description">'.$description.'</p>';
}
}

/*
* Print form on adding new
*/
if( !function_exists('adifier_ext_term_add') ){
function adifier_ext_term_add() {
	echo '
	<div class="form-field">
		'.esc_html_e( 'Category Image', 'adifier' ).'
		'.adifier_cat_image_uploader( '', 'advert_cat_image', esc_html__( 'Select representative image of category', 'adifier' ) ).'
	</div>
	<div class="form-field">
		'.esc_html_e( 'Category Marker', 'adifier' ).'
		'.adifier_cat_image_uploader( '', 'advert_cat_marker', esc_html__( 'Select representative marker of category', 'adifier' ) ).'
	</div>
	<div class="form-field">
		'.esc_html_e( 'Category Icon', 'adifier' ).'
		'.adifier_cat_image_uploader( '', 'advert_cat_icon', esc_html__( 'Select representative icon of category', 'adifier' ) ).'
	</div>
	';
}
add_action( 'advert-category_add_form_fields', 'adifier_ext_term_add', 10, 2 );
}

/*
* Print form on category edit screen
*/
if( !function_exists('adifier_ext_term_edit') ){
function adifier_ext_term_edit( $term ) {
	$advert_cat_image = get_term_meta( $term->term_id, 'advert_cat_image', true);
	$advert_cat_marker = get_term_meta( $term->term_id, 'advert_cat_marker', true);
	$advert_cat_icon = get_term_meta( $term->term_id, 'advert_cat_icon', true);
	?>
	<tr class="form-field form-required">
		<th scope="row">
			<?php esc_html_e( 'Category Image', 'adifier' ) ?>
		</th>
		<td>
			<?php echo adifier_cat_image_uploader( $advert_cat_image, 'advert_cat_image', esc_html__( 'Select representative image of category', 'adifier' ) ) ?>
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row">
			<?php esc_html_e( 'Category Marker', 'adifier' ) ?>
		</th>
		<td>
			<?php echo adifier_cat_image_uploader( $advert_cat_marker, 'advert_cat_marker', esc_html__( 'Select representative marker of category', 'adifier' ) ) ?>
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row">
			<?php esc_html_e( 'Category Icon', 'adifier' ) ?>
		</th>
		<td>
			<?php echo adifier_cat_image_uploader( $advert_cat_icon, 'advert_cat_icon', esc_html__( 'Select representative icon of category', 'adifier' ) ) ?>
		</td>
	</tr>
	<?php
}
add_action( 'advert-category_edit_form_fields', 'adifier_ext_term_edit', 10, 2 );
}

/*
* Save selected values
*/

if( !function_exists('adifier_ext_term_save') ){
function adifier_ext_term_save( $term_id ) {
	if( isset( $_POST['advert_cat_image'] ) ){
		update_term_meta( $term_id, 'advert_cat_image', $_POST['advert_cat_image'] );
	}
	else{
		delete_term_meta( $term_id, 'advert_cat_image' );	
	}
	if( isset( $_POST['advert_cat_marker'] ) ){
		update_term_meta( $term_id, 'advert_cat_marker', $_POST['advert_cat_marker'] );
	}
	else{
		delete_term_meta( $term_id, 'advert_cat_marker' );	
	}
	if( isset( $_POST['advert_cat_icon'] ) ){
		update_term_meta( $term_id, 'advert_cat_icon', $_POST['advert_cat_icon'] );
	}
	else{
		delete_term_meta( $term_id, 'advert_cat_icon' );	
	}
}  
add_action( 'edited_advert-category', 'adifier_ext_term_save', 10, 2 );  
add_action( 'create_advert-category', 'adifier_ext_term_save', 10, 2 );
}

/*
* Delete meta on category deletion
*/
if( !function_exists('adifier_ext_term_delete') ){
function adifier_ext_term_delete( $term_id ) {
	delete_term_meta( $term_id, 'advert_cat_image' );
	delete_term_meta( $term_id, 'advert_cat_marker' );	
	delete_term_meta( $term_id, 'advert_cat_icon' );	
}  
add_action( 'delete_advert-category', 'adifier_ext_term_delete', 10, 2 );
}

/*
* Add columns to category listing
*/

if( !function_exists('adifier_category_column') ){
function adifier_category_column( $columns ) {
	$columns['image'] = esc_html__( 'Image', 'adifier' );
	$columns['marker'] = esc_html__( 'Marker', 'adifier' );
	$columns['icon'] = esc_html__( 'Icon', 'adifier' );
	$columns['af_id'] = esc_html__( 'ID', 'adifier' );
    return $columns;
}
add_filter( 'manage_edit-advert-category_columns', 'adifier_category_column'); 
}

/*
* Populate columns on category listing
*/

if( !function_exists('adifier_populate_category_column') ){
function adifier_populate_category_column( $out, $column_name, $term_id ){
    if( $column_name == 'image' ){
		$advert_cat_image = get_term_meta( $term_id, 'advert_cat_image', true);
		if( !empty( $advert_cat_image ) ){
			$out .= wp_get_attachment_image( $advert_cat_image, 'thumbnail' );
		} 	
    }
    else if( $column_name == 'marker' ){
		$advert_cat_marker = get_term_meta( $term_id, 'advert_cat_marker', true);
		if( !empty( $advert_cat_marker ) ){
			$out .= wp_get_attachment_image( $advert_cat_marker, 'thumbnail' );
		} 	
    }
    else if( $column_name == 'icon' ){
		$advert_cat_icon = get_term_meta( $term_id, 'advert_cat_icon', true);
		if( !empty( $advert_cat_icon ) ){
			$out .= wp_get_attachment_image( $advert_cat_icon, 'full' );
		} 	
    }
    else if( $column_name == 'af_id' ){
    	$out .= $term_id;
    }
    return $out; 
}
add_filter( 'manage_advert-category_custom_column', 'adifier_populate_category_column', 10, 3);	
}


/*
* Add columns to location listing
*/

if( !function_exists('adifier_location_column') ){
function adifier_location_column( $columns ) {
	$columns['af_id'] = esc_html__( 'ID', 'adifier' );
    return $columns;
}
add_filter( 'manage_edit-advert-location_columns', 'adifier_location_column'); 
}

/*
* Populate columns on category listing
*/

if( !function_exists('adifier_populate_location_column') ){
function adifier_populate_location_column( $out, $column_name, $term_id ){
    if( $column_name == 'af_id' ){
    	$out .= $term_id;
    }
    return $out; 
}
add_filter( 'manage_advert-location_custom_column', 'adifier_populate_location_column', 10, 3);	
}
?>