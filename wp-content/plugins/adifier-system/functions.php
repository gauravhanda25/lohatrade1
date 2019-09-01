<?php


/* get url by page template */
if( !function_exists('adifier_get_permalink_by_tpl') ){
function adifier_get_permalink_by_tpl( $template_name ){
	$page = get_pages(array(
		'meta_key' => '_wp_page_template',
		'meta_value' => $template_name . '.php'
	));
	if(!empty($page)){
		$page = array_pop( $page );
		return urldecode( get_permalink( $page->ID ) );
	}
	else{
		return "javascript:;";
	}
}
}

/*
* Get value of theme option
*/
if( !function_exists('adifier_get_option') ){
function adifier_get_option($id){
	global $adifier_options;
	if( isset( $adifier_options[$id] ) ){
		$value = $adifier_options[$id];
		if( isset( $value ) ){
			return $value;
		}
		else{
			return '';
		}
	}
	else{
		return '';
	}
}
}


/*
* Send subscription to MailChimp
*/
if( !function_exists('adifier_send_subscription') ){
function adifier_send_subscription( $email = '' ){
	$email = !empty( $email ) ? $email : $_POST["email"];
	$response = array();
	if( !adifier_gdpr_given_consent() ){
		$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
	}	
	else if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
		if( class_exists('MailChimp') ){
			$chimp_api = adifier_get_option("mail_chimp_api");
			$chimp_list_id = adifier_get_option("mail_chimp_list_id");
			if( !empty( $chimp_api ) && !empty( $chimp_list_id ) ){
				$mc = new MailChimp( $chimp_api );
				$result = $mc->call('lists/subscribe', array(
					'id'                => $chimp_list_id,
					'double_optin'		=> adifier_get_option( 'mail_chimp_double_optin' ) == 'no' ? false : true,
					'email'             => array( 'email' => $email )
				));
				
				if( $result === false) {
					$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
				}
				else if( isset($result['status']) && $result['status'] == 'error' ){
					$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
				}
				else{
					$response['icon_response'] = '<i class="icon-response aficon-check-circle"></i>';
				}
				
			}
			else{
				$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
			}
		}
		else{
			$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
		}
	}
	else{
		$response['icon_response'] = '<i class="icon-response aficon-times-circle"></i>';
	}


	echo json_encode( $response );
	die();
}
add_action('wp_ajax_subscribe', 'adifier_send_subscription');
add_action('wp_ajax_nopriv_subscribe', 'adifier_send_subscription');
}

/*
* Cut string if it is too long for titles etc...
*/
if( !function_exists('adifier_limit_string') ){
function adifier_limit_string( $string, $length ){
	if( strlen( $string ) > $length ){
		$string = substr( $string, 0, $length );
		$temp = explode( ' ', $string );
		unset( $temp[sizeof($temp) - 1] );
		$string = implode( ' ', $temp ).'...';
	}

	return $string;
}
}


/*
* Set the direction of the site
*/
if( !function_exists('adifier_set_direction') ){
function adifier_set_direction() {
	global $wp_locale, $wp_styles;

	$_user_id = get_current_user_id();
	$direction = adifier_get_option( 'direction' );
	if( empty( $direction ) ){
		$direction = 'ltr';
	}

	if ( function_exists('icl_object_id') ) {
		if( ICL_LANGUAGE_CODE == 'ar' ){
			$direction = 'rtl';
		}
	}

	if ( $direction ) {
		update_user_meta( $_user_id, 'rtladminbar', $direction );
	} else {
		$direction = get_user_meta( $_user_id, 'rtladminbar', true );
		if ( false === $direction )
			$direction = isset( $wp_locale->text_direction ) ? $wp_locale->text_direction : 'ltr' ;
	}

	$wp_locale->text_direction = $direction;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		$wp_styles = new WP_Styles();
	}
	$wp_styles->text_direction = $direction;
}
add_action( 'init', 'adifier_set_direction' );
}

/*
* Custom fields for advert, posts, pages,...
*/
if( !function_exists('adifier_custom_meta') ){
function adifier_custom_meta(){

	$meta_boxes = array();

	$advert_details = array(
		array(
			'id' 				=> 'advert_gallery',
			'name' 				=> esc_html__( 'Images', 'adifier' ),
			'type' 				=> 'image',
			'repeatable'		=> true,
			'sortable'			=> true,
		),
		array(
			'id' 				=> 'advert_videos',
			'name' 				=> esc_html__( 'Video URLs', 'adifier' ),
			'type' 				=> 'text',
			'repeatable'		=> true,
			'sortable'			=> true,
		),
		array(
			'id' 				=> 'advert_type',
			'name' 				=> esc_html__( 'Ad Type', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'1' => esc_html__( 'Sell', 'adifier' ),
				'2' => esc_html__( 'Auction', 'adifier' ),
				'3' => esc_html__( 'Buy', 'adifier' ),
				'4' => esc_html__( 'Exchange', 'adifier' ),
				'5' => esc_html__( 'Gift', 'adifier' ),
				'6' => esc_html__( 'Rent', 'adifier' ),
				'7' => esc_html__( 'Job - Offer', 'adifier' ),
				'8' => esc_html__( 'Job - Wanted', 'adifier' ),
			),
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_price',
			'name' 				=> esc_html__( 'Price / Salary - No thousands separator and decimal must be . ( dot )', 'adifier' ),
			'type' 				=> 'text',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',
			'desc'				=> esc_html__( 'Leave empty to display "Call for price" / "Call for salary"', 'adifier' )
		),
		array(
			'id' 				=> 'advert_start_price',
			'name' 				=> esc_html__( 'Start Price / Max Salary - No thousands separator and decimal must be . ( dot )', 'adifier' ),
			'type' 				=> 'text',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',
			'desc'				=> esc_html__( 'Start price if ad is an auction or Max Salary if type is Job - Wanted', 'adifier' )
		),		
		array(
			'id' 				=> 'advert_sale_price',
			'name' 				=> esc_html__( 'Sale Price - No thousands separator and decimal must be . ( dot )', 'adifier' ),
			'type' 				=> 'text',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_reserved_price',
			'name' 				=> esc_html__( 'Reserved Price ( For Auction Type ) - No thousands separator and decimal must be . ( dot )', 'adifier' ),
			'type' 				=> 'text',
		),		
		array(
			'id' 				=> 'advert_expire',
			'name' 				=> esc_html__( 'Expires', 'adifier' ),
			'type' 				=> 'datetime_unix',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_rent_period',
			'name' 				=> esc_html__( 'Rent Period', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				''	=> esc_html__( 'None', 'adifier' ),
				'1'	=> esc_html__( 'Month', 'adifier' ),
				'2'	=> esc_html__( 'Week', 'adifier' ),
				'3'	=> esc_html__( 'Day', 'adifier' ),
				'4'	=> esc_html__( 'Hour', 'adifier' ),
			),
			'desc'				=> esc_html__( 'This is for rent type only', 'adifier' )	
		),
		array(
			'id' 				=> 'advert_views',
			'name' 				=> esc_html__( 'Views', 'adifier' ),
			'type' 				=> 'number',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_sold',
			'name' 				=> esc_html__( 'Is Sold', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'0' => esc_html__( 'No', 'adifier' ) ,
				'1' => esc_html__( 'Yes', 'adifier' ) 
			),
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_negotiable',
			'name' 				=> esc_html__( 'Is Negotiable', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'0' => esc_html__( 'No', 'adifier' ) ,
				'1' => esc_html__( 'Yes', 'adifier' ) 
			),
		),
		array(
			'id' 				=> 'advert_cond',
			'name' 				=> esc_html__( 'Condition', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'0'	=> esc_html__( '-Select-', 'adifier' ),
				'1'	=> esc_html__( 'New', 'adifier' ),
				'2'	=> esc_html__( 'Manufacturer Refurbished', 'adifier' ),
				'3'	=> esc_html__( 'Used', 'adifier' ),
				'4'	=> esc_html__( 'For Parts Or Not Working', 'adifier' ),
			),
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
		array(
			'id' 				=> 'advert_exp_info',
			'name' 				=> esc_html__( 'Informed user about expiration', 'adifier' ),
			'desc'				=> esc_html__( 'It is changed by script change it here to No only if your mail server did not sent an email so you want to try again', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'0' => esc_html__( 'No', 'adifier' ),
				'1' => esc_html__( 'Yes', 'adifier' )
			),
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		),
	);

	$currencies = adifier_get_currencies_raw_list();
	if( count( $currencies ) > 1 ) {
		$advert_details[] = array(
			'id' 				=> 'advert_currency',
			'name' 				=> esc_html__( 'Currency', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> $currencies,
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',			
		);
	}

	$meta_boxes[] = array(
		'title' 		=> esc_html__( 'Details', 'adifier' ),
		'pages' 		=> 'advert',
		'fields' 		=> $advert_details,
	);

	$advert_promotions = array(
		array(
			'id' 				=> 'promo_bumpup',
			'name' 				=> esc_html__( 'Is Bump Up', 'adifier' ),
			'type' 				=> 'select',
			'options'			=> array(
				'no'	=> esc_html__( 'No', 'adifier' ),
				'yes'	=> esc_html__( 'Yes', 'adifier' )
			),
			'values_callback' 	=> 'adifier_get_promo_admin_status',
			'save_callback' 	=> 'adifier_save_promo_admin_status',
		),
		array(
			'id' 				=> 'promo_highlight',
			'name' 				=> esc_html__( 'Highlight Until', 'adifier' ),
			'type' 				=> 'datetime_unix',
		),
		array(
			'id' 				=> 'promo_topad',
			'name' 				=> esc_html__( 'Is Top Ads Until', 'adifier' ),
			'type' 				=> 'datetime_unix',
			'values_callback' 	=> 'adifier_get_promo_admin_status',
			'save_callback' 	=> 'adifier_save_promo_admin_status',
		),
		array(
			'id' 				=> 'advert_urgent',
			'name' 				=> esc_html__( 'Urgent Until', 'adifier' ),
			'type' 				=> 'datetime_unix',
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',
		),
		array(
			'id' 				=> 'promo_homemap',
			'name' 				=> esc_html__( 'On Home Map', 'adifier' ),
			'type' 				=> 'datetime_unix',
			'values_callback' 	=> 'adifier_get_promo_admin_status',
			'save_callback' 	=> 'adifier_save_promo_admin_status',
		),
	);

	$meta_boxes[] = array(
		'title' 		=> esc_html__( 'Promotions', 'adifier' ),
		'pages' 		=> 'advert',
		'fields' 		=> $advert_promotions,
		'context'		=> 'side',
	);	

	$advert_contact = array(
		array(
			'id' 				=> 'advert_location',
			'name' 				=> esc_html__( 'Location', 'adifier' ),
			'type' 				=> 'gmap',
			'extract_location'	=> true,
			'values_callback' 	=> 'adifier_get_advert_meta',
			'save_callback' 	=> 'adifier_save_advert_meta',
			'api_key'			=> adifier_get_option( 'google_api_key' ),
			'map_source'		=> adifier_get_option( 'map_source' )
		),
		array(
			'id' 				=> 'advert_phone',
			'name' 				=> esc_html__( 'Phone', 'adifier' ),
			'type' 				=> 'text',
		),
	);

	$meta_boxes[] = array(
		'title' 		=> esc_html__( 'Contact information', 'adifier' ),
		'pages' 		=> 'advert',
		'fields' 		=> $advert_contact,
	);	

	$advert_report = array(
		array(
			'id' 				=> 'advert_report',
			'name' 				=> esc_html__( 'Reason', 'adifier' ),
			'type' 				=> 'textarea',
		),
	);


	$meta_boxes[] = array(
		'title' 		=> esc_html__( 'Ad Report', 'adifier' ),
		'pages' 		=> 'advert',
		'fields' 		=> $advert_report,
	);	

	$order_meta = array(
		array(
			'id' 			=> 'order_payment_type',
			'name' 			=> esc_html__( 'Payment Type', 'adifier' ),
			'type' 			=> 'select',
			'allow_none' 	=> false,
			'options' 		=> apply_filters( 'adifier_payments_dropdown', array() )
		),
		array(
			'id' 	=> 'order_transaction_id',
			'name' 	=> esc_html__( 'Transaction Id', 'adifier' ),
			'type' 	=> 'text',
		),
		array(
			'id' 		=> 'order_paid',
			'name' 		=> esc_html__( 'Paid', 'adifier' ),
			'type' 		=> 'select',
			'options' 	=> array(
				'no'		=> esc_html__( 'No', 'adifier' ),
				'yes'		=> esc_html__( 'Yes', 'adifier' )
			)
		),
	);

	$meta_boxes[] = array(
		'title' 	=> esc_html__( 'Order Details', 'adifier' ),
		'pages' 	=> 'ad-order',
		'fields' 	=> $order_meta,
	);	

	return $meta_boxes;
}
add_filter('cmb_meta_boxes', 'adifier_custom_meta');
}

/*
* Main function for getting hierarchy structure for any given taxonomy
*/
if( !function_exists('adifier_get_taxonomy_hierarchy') ){
function adifier_get_taxonomy_hierarchy( $taxonomy, $parent = 0, $hide_empty = false ) {
	$taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;
	$terms = get_terms(array( 
		'taxonomy'		=> $taxonomy,
		'parent' 		=> $parent, 
		'hide_empty' 	=> $hide_empty 
	));
	$children = array();
	if( !empty( $terms ) && !is_wp_error( $terms ) ){
		foreach ( $terms as $term ){
			$term->children = adifier_get_taxonomy_hierarchy( $taxonomy, $term->term_id, $hide_empty );
			$children[ $term->term_id ] = $term;
		}
	}
	return $children;
}
}

/*
* Organize terms in hierarchy when they are already obtained via some other function than adifier_get_taxonomy_hierarchy()
* For example when we need hierarchy of terms assigned to post instead of all of them
*/
if( !function_exists('adifier_taxonomy_hierarchy') ){
function adifier_taxonomy_hierarchy( $terms, $parent_id = 0 ){
	$list = array();
	if( !empty( $terms ) && !is_wp_error( $terms ) ){
		foreach( $terms as $term ){
			if( $term->parent == $parent_id ){
				$list[$term->term_id] = $term;
			}
		}
	    foreach ( $list as $list_item ) {
	        $list_item->children = array();
	        $list_item->children = adifier_taxonomy_hierarchy( $terms, $list_item->term_id );
	    }		
	}

	return $list;
}
}

/*
* Get taxonomy ID hierarchy
*/
if( !function_exists('adifier_taxonomy_id_hierarchy') ){
function adifier_taxonomy_id_hierarchy( $terms ){
	$ids = array();
	if( !empty( $terms ) && !is_wp_error( $terms ) ){
		foreach( $terms as $term ){
			$ids[] = $term->term_id;
			if( !empty( $term->children ) ){
				$ids = array_merge( $ids, adifier_taxonomy_id_hierarchy( $term->children ) );
			}
		}	
	}

	return $ids;
}
}

/*
* Get taxonomy ID => name hierarchy
*/
if( !function_exists('adifier_taxonomy_id_name_hierarchy') ){
function adifier_taxonomy_id_name_hierarchy( $terms ){
	$ids = array();
	if( !empty( $terms ) ){
		foreach( $terms as $term ){
			$ids[] = array(
				'term_id' 	=> $term->term_id,
				'name'		=> $term->name
			);
			if( !empty( $term->children ) ){
				$ids = array_merge( $ids, adifier_taxonomy_id_name_hierarchy( $term->children ) );
			}
		}	
	}

	return $ids;
}
}

/*
* Handle image uploading of profile avatar and images of the advert
*/
if( !function_exists('adifier_handle_image_upload') ){
function adifier_handle_image_upload( $file, $attach_to = 0 ){
	$movefile = wp_handle_upload( $file, array( 'test_form' => false ) );

	if( !empty( $movefile['url'] ) ){
	
		$attachment = array(
			'guid'           => $movefile['url'],
			'post_mime_type' => $movefile['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['file'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $movefile['file'], $attach_to );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}
}
}

/*
* Get name of the tax which will be displayed bellow the price
*/
if( !function_exists('adifier_tax_included') ){
function adifier_tax_included(){
	$tax_name = adifier_get_option( 'tax_name' );
	echo !empty( $tax_name ) ? '<p class="tax-included">*'.$tax_name.' '.esc_html__( 'included', 'adifier' ).'</p>' : '';
}
}


/*
* List of available promotions ( they can have custom function for remove and apply which are defined in advert-functions.php )
*/
if( !function_exists('adifier_available_promotions') ){
function adifier_available_promotions(){
	return array(
		'promo_bumpup' => array(
            'id'        	=> 'promo_bumpup',
            'type'      	=> 'grouped_adifier',
            'title'     	=> esc_html__('Bump Up Ad', 'adifier'),
            'subfields' 	=> array( esc_html__( 'Price', 'adifier' ) ),
            'desc'      	=> esc_html__('Move ad to front (This will update creation date while maintaining expiration date). Decimal separator is . (dot) no thousands separator', 'adifier'),
			'front_desc'    => esc_html__('Creation date will be changed to current date while maintaining expiry date resulting in moving ad to first place again. This can be applied only once per ad.', 'adifier'),
            'handler'		=> 'adifier_bumpup_advert',
            'value_handler'	=> 'adifier_check_bumpup'
        ),
        'promo_highlight' => array(
            'id'        	=> 'promo_highlight',
            'type'      	=> 'grouped_adifier',
            'title'     	=> esc_html__('Highlight Ad', 'adifier'),
            'subfields' 	=> array( esc_html__( 'Days', 'adifier' ), esc_html__( 'Price', 'adifier' ) ),
            'desc'      	=> esc_html__('Highlight ad in the listing. Decimal separator is . (dot) no thousands separator', 'adifier'),
			'front_desc'    => esc_html__('Make your ad stand out with different colors.', 'adifier'),
            'value_holder'	=> 'meta_value',
            'repeatable'	=> true
        ),
        'promo_topad' => array(
            'id'        	=> 'promo_topad',
            'type'      	=> 'grouped_adifier',
            'title'     	=> esc_html__('Top Ad', 'adifier'),
            'subfields' 	=> array( esc_html__( 'Days', 'adifier' ), esc_html__( 'Price', 'adifier' ) ),
            'desc'      	=> esc_html__('Display ad in two positions - top of the listing and its current position. Decimal separator is . (dot) no thousands separator', 'adifier'),
			'front_desc'    => esc_html__('Push your ad to top of the listing while maintaining it\'s current position. There can be multiple top ads and display of them is random so all receive same exposure.', 'adifier'),
            'handler'		=> 'adifier_topad_advert',
            'value_handler'	=> 'adifier_check_topad',
            'repeatable'	=> true
        ),
        'promo_urgent' => array(
            'id'        	=> 'promo_urgent',
            'type'      	=> 'grouped_adifier',
            'title'     	=> esc_html__('Urgent Ad', 'adifier'),
            'subfields' 	=> array( esc_html__( 'Days', 'adifier' ), esc_html__( 'Price', 'adifier' ) ),
            'desc'      	=> esc_html__('Display ribbon with "URGENT" label on ad - users can also filter the ads by this data. Decimal separator is . (dot) no thousands separator', 'adifier'),
			'front_desc'    => esc_html__('This promotion will add "URGENT" ribbon on your ad.', 'adifier'),
            'value_holder'	=> 'extra_value',
            'repeatable'	=> true
        ),
        'promo_homemap' => array(
            'id'        	=> 'promo_homemap',
            'type'      	=> 'grouped_adifier',
            'title'     	=> esc_html__('Home Map Ad', 'adifier'),
            'subfields' 	=> array( esc_html__( 'Days', 'adifier' ), esc_html__( 'Price', 'adifier' ) ),
            'desc'      	=> esc_html__('Display ad on home page map. Decimal separator is . (dot) no thousands separator', 'adifier'),
			'front_desc'    => esc_html__('This promotion your ad will be visible on the map on home page.', 'adifier'),
            'handler'		=> 'adifier_homemap_advert',
            'value_handler'	=> 'adifier_check_homemap',
            'repeatable'	=> true
        ),
	);
}
}

/*
* Get selected currency specs
*/
if( !function_exists( 'adifier_get_currency_specs' ) ){
function adifier_get_currency_specs( $currency_id = '' ){
	$currencies = adifier_get_currencies();

	return !empty( $currency_id ) ? $currencies[$currency_id] : array_shift( $currencies );
}
}

/*
* Price formatting
*/
if( !function_exists('adifier_price_format') ){
function adifier_price_format( $price, $currency_id = '' ){
	$currency_specs = adifier_get_currency_specs( $currency_id );

	$currency_symbol = $currency_specs['sign'];
	$currency_symbol = '<span class="price-symbol">'.$currency_symbol.'</span>';

	$price = adifier_price_format_value( $price, true, $currency_id );

	return $currency_specs['form'] == 'front' ? $currency_symbol.$price : $price.$currency_symbol;
}
}

/*
* Format price value
*/
if( !function_exists('adifier_price_format_value') ){
function adifier_price_format_value( $price, $thousands = true, $currency_id = '' ){
	$currency_specs = adifier_get_currency_specs( $currency_id );

	return number_format( (float)$price, ($currency_specs['show_decimals'] == 'yes' ? 2 : 0), $currency_specs['decimal_separator'], $thousands == true ? $currency_specs['thousands_separator'] : '');
}
}

/*
* Make price mysql friendly
*/
if( !function_exists('adifier_mysql_format_price') ){
function adifier_mysql_format_price( $price, $currency_id = '' ){
	$currency_specs = adifier_get_currency_specs( $currency_id );

	return str_replace( array( $currency_specs['thousands_separator'], $currency_specs['decimal_separator'] ), array( '', '.' ), $price );
}
}

/*
* Validate price format
*/
if( !function_exists('adifier_validate_price_format') ){
function adifier_validate_price_format( $price, $currency_id = '' ){
	$currency_specs = adifier_get_currency_specs( $currency_id );

	if( ( $currency_specs['show_decimals'] == 'yes' && preg_match( "/^\b\d{1,3}(?:\\".$currency_specs['thousands_separator']."?\d{3})*(?:\\".$currency_specs['decimal_separator']."\d{2})?\b$/", $price ) ) || ( $currency_specs['show_decimals'] == 'no' && preg_match( "/^\b\d{1,3}(?:".$currency_specs['thousands_separator']."?\d{3})*?\b$/", $price ) ) ){
		return true;
	}
	return false;
}
}

/*
* Aceptable price formats
*/
if( !function_exists('adifier_acceptable_price_formats') ){
function adifier_acceptable_price_formats( $currency_id = '' ){
	return sprintf( esc_html__( ' (Aceptable formats: %s or %s)', 'adifier' ), adifier_price_format_value( 1000000, true, $currency_id ), adifier_price_format_value( 1000000, false, $currency_id ) );
}
}

/*
* Get list of top ads and while doing soo clear all expired ones
*/
if( !function_exists('adifier_get_top_ads_list') ){
function adifier_get_top_ads_list(){
	$top_ads = (array)get_option( 'adifier_top_ads' );
	$top_ads_list = array();
	if( !empty( $top_ads ) ){
		foreach( $top_ads as $term_id => $data ){
			if( !empty( $data ) ){
				foreach( $data as $advert_id => $end_time ){
					if( $end_time >= current_time( 'timestamp' ) ){
						$top_ads_list[$term_id][$advert_id] = $end_time;
					}
				}
			}
		}
	}

	if( $top_ads !== $top_ads_list ){
		update_option( 'adifier_top_ads', $top_ads_list );
	}

	return $top_ads_list;
}
}

/*
* Get list of home map ads and while doing soo clear all expired ones
*/
if( !function_exists('adifier_get_homemap_ads_list') ){
function adifier_get_homemap_ads_list(){
	$homemap_ads = (array)get_option( 'adifier_homemap_ads' );
	$homemap_list = array();
	if( !empty( $homemap_ads ) ){
		foreach( $homemap_ads as $advert_id => $end_time ){
			if( $end_time >= current_time( 'timestamp' ) ){
				$homemap_list[$advert_id] = $end_time;
			}
		}
	}

	if( $homemap_ads !== $homemap_list ){
		update_option( 'adifier_homemap_ads', $homemap_list );
	}

	return $homemap_list;
}
}

/*
* Sending mails
*/
if( !function_exists( 'adifier_send_mail' ) ){
function adifier_send_mail( $to, $subject, $message, $extra_headers = array() ){
    $email_sender = adifier_get_option( 'sender_email' );
    $name_sender = adifier_get_option( 'sender_name' );
    $headers   = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8"; 
    $headers[] = "From: ".esc_attr( $name_sender )." <".esc_attr( $email_sender ).">";
    $headers = array_merge( $headers, $extra_headers );
    
	return adifier_wp_send_mail( $to, $subject, $message, $headers );
}
}

/*
* Include files for payment modules
*/
foreach ( glob( dirname(__FILE__).DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR ."payments".DIRECTORY_SEPARATOR ."*.php" ) as $filename ){
	include( get_theme_file_path( 'includes/payments/'.basename( $filename ) ) );
}

/*
* Get advert category counts
*/
if( !function_exists('adifier_get_advert_taxonomy_counts') ){
function adifier_get_advert_taxonomy_counts( $term_ids ){
	global $wpdb;
	$query           = array();
	$query['select'] = "SELECT COUNT(posts.ID ) as term_count, terms.term_id";
	$query['from']   = "FROM {$wpdb->posts} AS posts";
	$query['join']   = "
		INNER JOIN {$wpdb->term_relationships} AS term_relationships ON posts.ID = term_relationships.object_id
		INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
		INNER JOIN {$wpdb->terms} AS terms USING( term_id ) 
		INNER JOIN {$wpdb->prefix}adifier_advert_data AS aad ON posts.ID = aad.post_id
		";

	$query['where']   = $wpdb->prepare("
		WHERE posts.post_type = 'advert' 
		AND posts.post_status = 'publish' 
		AND aad.expire >= %d 
		AND terms.term_id IN (".implode( ',', array_map( 'absint', $term_ids ) ).")", current_time( 'timestamp' ) );

	$query['group_by'] = 'GROUP BY terms.term_id';
	$query             = implode( ' ', $query );	

	$results = $wpdb->get_results( $query, ARRAY_A );
	$counts = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_id' ) );

	return $counts;
}
}


/*
* Allow subscribers to be selected as advert creators
*/
if( !function_exists('adifier_all_can_be_authors') ){
function adifier_all_can_be_authors( $query_args ) {
	global $current_screen;
	if( !empty( $current_screen->post_type ) && $current_screen->post_type == 'advert' ){
	    $query_args['who'] = '';
	}

	return $query_args;
}
add_filter( 'wp_dropdown_users_args', 'adifier_all_can_be_authors', 10 );
}

/*
Send contanct form
*/
if( !function_exists('adifier_send_contact') ){
function adifier_send_contact(){
	$errors = array();
	$name = $_POST['name'];
	$email = $_POST['email'];
	$subject = $_POST['subject']; 
	$message = nl2br( $_POST['message'] ); 

	if( empty( $_POST['aff-cpt'] ) ){
		return false;
	}

	if( !adifier_gdpr_given_consent() ){
		$response['message'] = '<div class="alert-error">'.esc_html__( 'You have not given consent.', 'adifier' ).'</div>';
	}
	else if( empty( $name ) || empty( $subject ) || empty( $email ) || empty( $message ) ){
		$response['message'] = '<div class="alert-error">'.esc_html__( 'All fields are required.', 'adifier' ).'</div>';
	}
	else if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		$response['message'] = '<div class="alert-error">'.esc_html__( 'E-mail address is not valid.', 'adifier' ).'</div>';
	}
	else{
		$email_to = adifier_get_option( 'contact_form_email' );
		$message = esc_html__( 'Name: ', 'adifier' )." {$name} <br><br> ".esc_html__( 'Email: ', 'adifier' )." {$email} <br><br> ".esc_html__( 'Message: ', 'adifier' )." <br> {$message}";

		$info = adifier_send_mail( $email_to, $subject, $message );

		if( $info ){
			$response['message'] = '<div class="alert-success">'.esc_html__( 'Your message was successfully submitted.', 'adifier' ).'</div>';
		}
		else{
			$response['message'] = '<div class="alert-error">'.esc_html__( 'Unexpected error occurred while attempting to send e-mail.', 'adifier' ).'</div>';
		}
		
	}
	
	echo json_encode( $response );
	die();	
}
add_action('wp_ajax_send_contact', 'adifier_send_contact');
add_action('wp_ajax_nopriv_send_contact', 'adifier_send_contact');
}

/*
** Retrieve link to seearch page
*/
if( !function_exists('adifier_get_search_link') ){
function adifier_get_search_link(){
	$search_link = adifier_get_permalink_by_tpl( 'page-tpl_search' );
	if( empty( $search_link ) || $search_link == 'javascript:;' ){
		$search_link = adifier_get_permalink_by_tpl( 'page-tpl_search_map' );
	}	

	return esc_url( $search_link );
}
}


/*
* Add update/pending filters to ad backend
*/
if( !function_exists('adifier_admin_adverts_filter_extend') ){
function adifier_admin_adverts_filter_extend( $views ) {
	global $wpdb;
	$updates = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'draft' AND post_parent <> '0' AND post_type = 'advert'" );
	if( $updates > 0 ){
		$views['update'] = '<a href="'.esc_url( admin_url('edit.php?post_type=advert&adifier_is_update=1') ).'">'.esc_html__( 'Updates', 'adifier' ).' <span class="count">('.$updates.')</span></a>';
	}
	$pending = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'draft' AND post_parent = '0' AND post_type = 'advert'" );
	if( $pending > 0 ){
		$views['pending'] = '<a href="'.esc_url( admin_url('edit.php?post_type=advert&adifier_pending=1') ).'">'.esc_html__( 'Pending', 'adifier' ).' <span class="count">('.$pending.')</span></a>';
	}
	if( !empty( $views['draft'] ) ){
		unset( $views['draft'] );
	}
    return $views;
}
add_filter('views_edit-advert','adifier_admin_adverts_filter_extend');
}

/*
If we are on on admin listing opf coupons add filters
*/
if( !function_exists('adifier_admin_filter_advert_updates') ){
function adifier_admin_filter_advert_updates( $query ) {
	if( !empty( $_GET['adifier_is_update'] ) ){
		$query->set( 'meta_key', 'adifier_is_update' );
		$query->set( 'meta_value', '1' );
	}
	else if( !empty( $_GET['adifier_pending'] ) ){
		$query->set( 'post_status', 'draft' );
		$query->set( 'meta_key', 'adifier_is_update' );
		$query->set( 'meta_compare', 'NOT EXISTS' );
	}

	return $query;
}
//add_filter('pre_get_posts', 'adifier_admin_filter_advert_updates');
}

if( !function_exists('adifier_ajax_quick_search') ){
function adifier_ajax_quick_search(){
	if( !empty( $_POST['s'] ) && strlen( $_POST['s'] ) >= 4 ){
		$adverts = new Adifier_Advert_Query(array(
			's'	=> $_POST['s']
		));
		
		$response['message'] = '<div class="alert-error">'.esc_html__( 'No results found.', 'adifier' ).'</div>';
		if( $adverts->have_posts() ){
			ob_start();
			?>
			<ul class="list-unstyled quick-search-list">
				<?php
				while( $adverts->have_posts() ){
					$adverts->the_post();
					?>
					<li class="flex-wrap flex-start-h">
						<a href="<?php the_permalink() ?>" class="qs-img">
							<?php adifier_get_advert_image( 'thumbnail' ); ?>
						</a>
						<div class="flex-right <?php echo has_post_thumbnail() ? esc_attr( '' ) : esc_attr( 'qs-full-right' ) ?>">
							<h5 class="adv-title">
								<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="text-overflow">
									<?php the_title(); ?>
								</a>
							</h5>
							<div class="bottom-advert-meta flex-wrap">
								<?php echo adifier_get_advert_price() ?>
							</div>
						</div>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
			$response['message'] = ob_get_contents();
			ob_end_clean();
		}

		wp_reset_postdata();
	}
	else{
		$response['message'] = '<div class="alert-error">'.esc_html__( 'At least 4 chars are required.', 'adifier' ).'</div>';
	}

	echo json_encode( $response );
	die();
}
add_action( 'wp_ajax_adifier_ajax_quick_search', 'adifier_ajax_quick_search' );
add_action( 'wp_ajax_nopriv_adifier_ajax_quick_search', 'adifier_ajax_quick_search' );
}


/*
* HTML wrapper for logos
*/
if( !function_exists('adifier_logo_html_wrapper') ){
function adifier_logo_html_wrapper( $source, $check_existance = false ){
	$logo = adifier_get_option( $source );
	if( !$check_existance || ( $check_existance && !empty( $logo['url'] ) ) ){
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ) ?>" class="logo">
		<?php		
			$logo_width = adifier_get_option( 'logo_width' );
			$logo_height = adifier_get_option( 'logo_height' );
			if( !empty( $logo['url'] ) ){
				$ext = pathinfo( $logo['url'], PATHINFO_EXTENSION );
				if( $ext == 'svg' ){
					echo adifier_parse_svg( $logo['id'] );
				}
				else{
					?>
					<img src="<?php echo esc_url( $logo['url'] ) ?>" alt="logo" width="<?php echo !empty( $logo['width'] ) ? esc_attr( $logo['width'] ) : esc_attr( $logo_width ) ?>" height="<?php echo !empty( $logo['height'] ) ? esc_attr( $logo['height'] ) : esc_attr( $logo_height ) ?>"/>
					<?php
				}
			}
			else{
				?>
				<h2><?php echo get_bloginfo( 'name' ) ?></h2>
				<?php
			}
		?>
		</a>
		<?php
	}
}
}

if( !function_exists('adifier_no_backend_subscriber') ){
function adifier_no_backend_subscriber() {
	if( function_exists('adifier_server_variables') ){
		$server_variables = adifier_server_variables();
		if ( !current_user_can( 'edit_posts' ) && !stristr( $server_variables['PHP_SELF'], 'admin-ajax.php' ) && !stristr( $server_variables['PHP_SELF'], 'async-upload.php' ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'admin_init', 'adifier_no_backend_subscriber' );
}

/*
* Get advert location source
*/
if( !function_exists('adifier_get_location_source') ){
function adifier_get_location_source( $position ){
	$use_google_location = adifier_get_option( 'use_google_location' );
	$use_predefined_locations = adifier_get_option( 'use_predefined_locations' );
	$source = 'geo_value';
	if( $use_google_location == 'no' && $use_predefined_locations == 'yes' ){
		$source = 'predefined';
	}
	else if( $use_google_location == 'yes' && $use_predefined_locations == 'yes' ){
		$source = adifier_get_option( $position );
	}
	else if( $use_google_location == 'no' && $use_predefined_locations == 'no' ){
		$source = '';
	}

	return $source;
}
}

/*
* Get GDPR checkbox
*/
if( !function_exists( 'adifier_gdpr_checkbox' ) ){
function adifier_gdpr_checkbox(){
	$gdpr_text = adifier_get_option( 'gdpr_text' );
	if( !empty( $gdpr_text ) ){
		$id = uniqid();
		?>
		<div class="form-group has-feedback">
			<div class="styled-checkbox">
				<input type="checkbox" name="gdpr" id="gdpr-<?php echo esc_attr( $id ) ?>">
				<label for="gdpr-<?php echo esc_attr( $id ) ?>"><div><?php echo $gdpr_text; ?></div></label>
			</div>
		</div>
		<?php
	}
}
}

/*
* Do we need to check gdpr?
*/
if( !function_exists( 'adifier_gdpr_given_consent' ) ){
function adifier_gdpr_given_consent(){
	$response = true;
	$gdpr_text = adifier_get_option( 'gdpr_text' );
	if( !empty( $gdpr_text ) ){
		$response = isset( $_POST['gdpr'] ) ? true : false;
	}

	return $response;
}
}

/*
* Clear tags which are not good in copy/paste
*/
if( !function_exists('adifier_clear_tags_copy_paste') ){
function adifier_clear_tags_copy_paste($in) {
  $in['paste_preprocess'] = "function(plugin, args){
    var whitelist = 'p,span,b,strong,i,em,h3,h4,h5,h6,ul,li,ol';
    var stripped = jQuery('<div>' + args.content + '</div>');
    var els = stripped.find('*').not(whitelist);
    for (var i = els.length - 1; i >= 0; i--) {
      var e = els[i];
      jQuery(e).replaceWith(e.innerHTML);
    }
    stripped.find('*').removeAttr('id').removeAttr('class');
    args.content = stripped.html();
  }";

  return $in;
}
add_filter('tiny_mce_before_init','adifier_clear_tags_copy_paste');
}

/*
* Remove some buttons
*/
if( !function_exists('adifier_remove_buttons_from_tinymce') ){
function adifier_remove_buttons_from_tinymce( $buttons ) {
    $remove_buttons = array(
        'link',
        'unlink'
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}
}

/*
* Validate hex color since beaver builder does not pass #
*/
if( !function_exists('adifier_validate_hex_color') ){
function adifier_validate_hex_color( $color ){
	if(!preg_match('/^#[a-f0-9]{6}$/i', $color)){
		$color = '#'.$color;
	}

	return $color;
}
}

/*
* Get image for categories since it supports SVG
*/
if( !function_exists('adifier_get_category_icon_img') ){
function adifier_get_category_icon_img( $icon_id ){
	$img_data = wp_get_attachment_image_src( $icon_id, 'full' );
	if( !empty( $img_data ) ){
		$ext = pathinfo( $img_data[0], PATHINFO_EXTENSION );
		if( $ext == 'svg' ){
			return adifier_parse_svg( $icon_id );
		}
		else{
			return '<img src="'.esc_url( $img_data[0] ).'" width="'.esc_attr( $img_data[1] ).'" height="'.esc_attr( $img_data[2] ).'" alt="cat-icon"/>';
		}
	}
}
}

if( !function_exists('adifier_redirect_cpt_archive') ){
function adifier_redirect_cpt_archive() {
	if( is_post_type_archive( 'advert' ) && stristr( $_SERVER['REQUEST_URI'], 'feed'  ) == false ) {
		wp_redirect( adifier_get_search_link(), 301 );
		exit();
	}
}
add_action( 'template_redirect', 'adifier_redirect_cpt_archive' );
}

/*
* Show checkbox for terms & conditions
*/
if( !function_exists( 'adifier_terms_checkbox' ) ){
function adifier_terms_checkbox( $source ){
	$terms = adifier_get_option( $source );
	$rnd = rand(1,555);
	if( !empty( $terms ) ){
	?>
		<div class="styled-checkbox">
			<input type="checkbox" id="terms_<?php echo esc_attr( $rnd ) ?>" name="terms" value="1">
			<label for="terms_<?php echo esc_attr( $rnd ) ?>"><?php esc_html_e( 'I agree to', 'adifier' ) ?>&nbsp;<a href="<?php echo esc_url( $terms ) ?>" target="_blank"><?php esc_html_e( 'terms & conditions', 'adifier' ) ?></a></label>
		</div>
	<?php
	}
}
}

/*
Get page templates
*/
if( !function_exists('adifier_get_available_page_templates') ){
function adifier_get_available_page_templates(){
	return array(
		'page-tpl_search.php' 		=> 'Search',
		'page-tpl_search_map.php' 	=> 'Search With Map',
		'page-tpl_contact.php' 		=> 'Page Contact',
		'page-tpl_sellers.php' 		=> 'Sellers',
		'page-tpl_browse_by_buy.php' 		=> 'Browse By Buy',
	);
}
}

/*
Let's add page template list to dropdown 
*/
if( !function_exists('adifier_add_page_template_to_dropdown') ){
function adifier_add_page_template_to_dropdown( $templates )
{

	$adifier_templates = adifier_get_available_page_templates();

	foreach( $adifier_templates as $key => $page_template ){
		if( empty( $templates[$key] ) ){
			$templates[$key] = $page_template;
		}
	}

    return $templates;
}
add_filter( 'theme_page_templates', 'adifier_add_page_template_to_dropdown' );
}


/*
Let's load page template for given page
*/
if( !function_exists('adifier_load_page_template') ){
function adifier_load_page_template( $template )
{
    if( is_page() ){
        $page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
        $adifier_templates = adifier_get_available_page_templates();
        $adifier_templates = array_keys( $adifier_templates );
        if( in_array( $page_template, $adifier_templates ) ){
        	$template = get_theme_file_path( $page_template );
        }
    }
    else if( is_singular( 'advert' ) ){
    	$template = get_theme_file_path( 'single-advert.php' );
    }
    else if( is_singular( 'ad-order' ) ){
    	$template = get_theme_file_path( 'single-ad-order.php' );
    }
    else if( is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ){
    	$template = get_theme_file_path( 'taxonomy.php' );
    }
    else if( is_author() ){
    	$template = get_theme_file_path( 'author.php' );	
    }

    return $template;
}
add_filter( 'template_include', 'adifier_load_page_template', 99 );
}

/*
Add adifier foioter parts
*/
if( !function_exists('adifier_footer') ){
function adifier_footer(){
	include( get_theme_file_path( 'adifier-footer.php' ) );
}
add_action( 'adifier_footer', 'adifier_footer' );
}


/*
Add adifier foioter parts
*/
if( !function_exists('adifier_page_header') ){
function adifier_page_header(){
	include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
	include( get_theme_file_path( 'includes/headers/header-search.php' ) );
	include( get_theme_file_path( 'includes/headers/gads.php' ) );
}
add_action( 'adifier_page_header', 'adifier_page_header' );
}

/*
Add adifier foioter parts
*/
if( !function_exists('adifier_page_adsence') ){
function adifier_page_adsence(){
	include( get_theme_file_path( 'includes/headers/gads.php' ) );
}
add_action( 'adifier_page_adsence', 'adifier_page_adsence' );
}


/*
Print google analytics in header
*/
if( !function_exists('adifier_google_analytics') ){
function adifier_google_analytics(){
	echo adifier_get_option( 'google_analytics' );
}
add_action('wp_head', 'adifier_google_analytics');
}

/*
* Breadcrumbs category hierarchy loop
*/
if( !function_exists('adifier_breadcrumb_cats_hierarchy') ){
function adifier_breadcrumb_cats_hierarchy( $terms ){
	$breadcrumb = '';
	if( !empty( $terms ) ){
		foreach( $terms as $term ) {
			$breadcrumb .= '<li><a href="'.get_term_link( $term ).'">'.$term->name.'</a></li>';
			if( !empty( $term->children ) ){
				$breadcrumb .= adifier_breadcrumb_cats_hierarchy( $term->children );
			}
		}
	}

	return $breadcrumb;
}
}

/*
* Adifier breadcrumbs
*/
if( !function_exists('adifier_breadcrumbs') ){
function adifier_breadcrumbs(){
	$breadcrumb = '';
	if( is_front_page() || ( is_home() && !class_exists('ReduxFramework') ) ){
		return '';
	}
	$breadcrumb .= '<ul class="list-unstyled list-inline breadcrumbs">';
	if( !is_front_page() ){
		$breadcrumb .= '<li><a href="'.esc_url( home_url('/') ).'">'.esc_html__( 'Home', 'adifier' ).'</a></li>';
	}
	if( is_home() ){
		$page_for_posts = get_option( 'page_for_posts' );
		if( !empty( $page_for_posts ) ){
			$breadcrumb .= '<li>'.get_the_title( $page_for_posts ).'</li>';
		}
		else{
			$breadcrumb .= '<li>'.esc_html__( 'Blog', 'adifier' ).'</li>';
		}
	}
	else if( is_category() || is_tax() ){
		$breadcrumb .= '<li>'.single_cat_title( '', false ).'</li>';
	}
	else if( is_404() ){
		$breadcrumb .= '<li>'.esc_html__( '404', 'adifier' ).'</li>';
	}
	else if( is_tag() ){
		$breadcrumb .= '<li>'.esc_html__('Search by tag: ', 'adifier'). get_query_var('tag').'</li>';
	}
	else if( is_author() ){
		if( function_exists('cmb_init') ){
			$breadcrumb .= '<li>'.esc_html__('Profile of', 'adifier').' '.get_the_author().'</li>';	
		}
		else{
			$breadcrumb .= '<li>'.esc_html__('Posts by', 'adifier').' '.get_the_author().'</li>';
		}
	}
	else if( is_archive() ){
		$breadcrumb .= '<li>'.esc_html__('Archive for:', 'adifier'). single_month_title(' ',false).'</li>';
	}
	else if( is_search() ){
		$breadcrumb .= '<li>'.esc_html__('Search results for: ', 'adifier').' '. get_search_query().'</li>';
	}
	else if( is_page() ){
		$ancestors = get_post_ancestors( get_the_ID() );
		if( !empty( $ancestors ) ){
			$ancestors = array_reverse( $ancestors );
			foreach( $ancestors as $ancestor ){
				$breadcrumb .= '<li><a href="'.get_the_permalink( $ancestor ).'">'.get_the_title( $ancestor ).'</a></li>';
			}
		}
		$breadcrumb .= '<li>'.get_the_title().'</li>';
	}
	else if( is_singular( 'post' ) ){
		$cats = wp_get_post_terms( get_the_ID(), 'category' );
		$cats = adifier_taxonomy_hierarchy( $cats );
		$breadcrumb .= adifier_breadcrumb_cats_hierarchy( $cats );
		
	}
	else if( is_singular( 'advert' ) ){
		$cats = wp_get_post_terms( get_the_ID(), 'advert-category' );
		$cats = adifier_taxonomy_hierarchy( $cats );
		$breadcrumb .= adifier_breadcrumb_cats_hierarchy( $cats );
		
	}
	else{
		$breadcrumb .= '<li>'.get_the_title().'</li>';
	}
	$breadcrumb .= '</ul>';

	return $breadcrumb;
}
}

/*
* Adifier breadvrumb title
*/
if( !function_exists('adifier_breadcrumbs_title') ){
function adifier_breadcrumbs_title(){
	if( is_home() ){
		$page_for_posts = get_option( 'page_for_posts' );
		if( !empty( $page_for_posts ) ){
			echo get_the_title( $page_for_posts );
		}
		else{
			esc_html_e( 'Blog', 'adifier' );
		}
	}
	else if( is_category() || is_tax() ){
		echo single_cat_title( '', false );
	}
	else if( is_404() ){
		esc_html_e( '404', 'adifier' );
	}
	else if( is_tag() ){
		echo get_query_var('tag');
	}
	else if( is_author() ){
		echo get_the_author();
	}
	else if( is_archive() ){
		echo single_month_title(' ', false);
	}
	else if( is_search() ){
		echo get_search_query();
	}
	else{
		the_title();
	}
}
}

/*
Allow search by ID
*/
if( !function_exists('adifier_posts_where_id_is_like') ){
function adifier_posts_where_id_is_like( $where ){
	if( is_admin() && is_search() ){
		$s = $_GET['s'];
		if( !empty($s) ){
			if( is_numeric($s) ){
				global $wpdb;
				
				$where = str_replace('(' . $wpdb->posts . '.post_title LIKE', '(' . $wpdb->posts . '.ID LIKE ' . $s . ') OR (' . $wpdb->posts . '.post_title LIKE', $where);
			}
		}
	}

	return $where;
}
add_filter('posts_where', 'adifier_posts_where_id_is_like', 99);
}


/*
* Enqueu currency data
*/
if( !function_exists('adifier_enqueue_curency_specs') ){
function adifier_enqueue_curency_specs(){
	wp_localize_script( 'bootstrap', 'adifier_currency_specs', adifier_get_currencies());
}
}
add_action('wp_enqueue_scripts', 'adifier_enqueue_curency_specs', 12 );

/*
* Update body class
*/
if( !function_exists('adifier_body_class') ){
function adifier_body_class( $classes ){
	if( is_author() ){
		$author = adifier_get_author();
		if( adifier_is_own_account( $author->ID ) ){
			$classes[] = 'author-dashboard';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'adifier_body_class' );
}

if( !function_exists('adifier_get_map_autocomplete_html') ){
function adifier_get_map_autocomplete_html(){
	$map_source = adifier_get_option( 'map_source' );
	if( $map_source == 'google' ){
		?>
			<input type="text" class="map-search" placeholder="<?php esc_attr_e( 'Start typing...', 'adifier' ) ?>">
		<?php
	}
	else if( $map_source == 'mapbox' ){
		?>
			<div id="map-search"></div>
		<?php
	}
}
}

/*
* If plugins are active then it is safe to load files bellow
*/
if( function_exists('adifier_create_post_types') && function_exists('adifier_server_variables') ){
	include( get_theme_file_path( 'includes/classes/orders.class.php' ) );
	if( is_admin() ){
		include( get_theme_file_path( 'includes/admin/advert-bids.php' ) );
		include( get_theme_file_path( 'includes/admin/messages-functions.php' ) );
		include( get_theme_file_path( 'includes/admin/profile-functions.php' ) );
		include( get_theme_file_path( 'includes/admin/reviews-functions.php' ) );
		include( get_theme_file_path( 'includes/redux-extension/redux-extensions.php' ) );

		include( get_theme_file_path( 'includes/advert-category-extend.php' ) );

		include( get_theme_file_path( 'radium-one-click-demo-install/init.php' ) );
	}

	include( get_theme_file_path( 'includes/compare.php' ) );

	include( get_theme_file_path( 'includes/custom-fields/admin-custom-fields.class.php' ) );
	include( get_theme_file_path( 'includes/custom-fields/custom-fields-advert.class.php' ) );	
	include( get_theme_file_path( 'includes/custom-fields/custom-fields-search.class.php' ) );
	include( get_theme_file_path( 'includes/custom-fields/custom-fields-front-advert.class.php' ) );

	include( get_theme_file_path( 'includes/shortcodes/shortcodes.php' ) );

	include( get_theme_file_path( 'includes/fonts.php' ) );
	include( get_theme_file_path( 'includes/font-icons.php' ) );
	include( get_theme_file_path( 'includes/classes/advert-query.class.php' ) );
	include( get_theme_file_path( 'includes/classes/theme-options.class.php' ) );
	include( get_theme_file_path( 'includes/classes/social-login.class.php' ) );
	include( get_theme_file_path( 'includes/classes/sms-verification.class.php' ) );

	include( get_theme_file_path( 'includes/classes/custom-table-import-export.class.php' ) );
}

include( get_theme_file_path( 'includes/author/advert-functions.php' ) );
include( get_theme_file_path( 'includes/author/profile-functions.php' ) );
include( get_theme_file_path( 'includes/author/auctions-functions.php' ) );
include( get_theme_file_path( 'includes/author/reviews-functions.php' ) );
include( get_theme_file_path( 'includes/author/messages-functions.php' ) );
include( get_theme_file_path( 'includes/author/dashboard-functions.php' ) );
include( get_theme_file_path( 'includes/author/acc_pay-functions.php' ) );


include( get_theme_file_path( 'includes/classes/widgets.class.php' ) );
include( get_theme_file_path( 'includes/classes/mailchimp.php' ) );
?>