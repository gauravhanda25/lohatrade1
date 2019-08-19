<?php
if( !class_exists( 'Adifier_Advert_Query' ) ){
class Adifier_Advert_Query extends WP_Query {

	public $args;

	function __construct( $args = array() ) {

		$args['orderby'] = empty( $args['orderby'] ) ? 'expire' : $args['orderby'];
		$args['order'] = empty( $args['order'] ) ? 'DESC' : $args['order'];

		$args = array_merge( array(
			'post_type' 			=> 'advert',
			'orderby' 				=> 'expire',
			'order' 				=> 'DESC',
			'post_parent'			=> 0,
			'status' 				=> '',
			'posts_per_page'		=> adifier_get_option( 'adverts_per_page' ),
			'return_all'			=> false,
			'expired'				=> false,
			'sold'					=> false,
			'urgent'				=> false,
			'include_top_ads'		=> false
		), $args);

		$this->args = $args;

		add_filter( 'posts_fields', array( $this, 'posts_fields' ) );
		add_filter( 'posts_join', array( $this, 'posts_join' ) );
		add_filter( 'posts_where', array( $this, 'posts_where' ) );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ));
		if( $this->args['include_top_ads'] ){
			add_filter( 'posts_request', array( $this, 'posts_request' ));
		}

		parent::__construct( $args );

		adifier_clear_filter( 'posts_fields', array( $this, 'posts_fields' ) );
		adifier_clear_filter( 'posts_join', array( $this, 'posts_join' ) );
		adifier_clear_filter( 'posts_where', array( $this, 'posts_where' ) );
		adifier_clear_filter( 'posts_orderby', array( $this, 'posts_orderby' ));
		if( $this->args['include_top_ads'] ){
			adifier_clear_filter( 'posts_request', array( $this, 'posts_request' ));
		}
	}

	/*
	* In order to provide top ads which will list with regular ones we need to use UNION ALL
	* This will be applied on completed query for searching
	*/
	static public function posts_request_process( $sql, $ids, $posts_per_page, $paged ){
		if( !empty( $ids ) ){
			global $wpdb;
			$ids = array_unique( $ids );
			$sql = preg_replace( "/LIMIT.*/", '', $sql );
			preg_match("/ORDER BY.*/", $sql, $match);
			$orderby = !empty( $match[0] ) ? str_replace( array('adverts.', 'ORDER BY ', $wpdb->posts.'.'), '', $match[0] ) : '';
			$sql = $sql1 = $sql2 = preg_replace( "/ORDER.*/", '', $sql );
			if( strstr( $orderby, 'post_date' ) ){
				$select_fields = "{$wpdb->posts}.ID, {$wpdb->posts}.post_author, FROM_UNIXTIME(1544822069+FLOOR(RAND()*31536000)) AS post_date, {$wpdb->posts}.post_date_gmt, {$wpdb->posts}.post_content, {$wpdb->posts}.post_title, {$wpdb->posts}.post_excerpt, {$wpdb->posts}.post_status, {$wpdb->posts}.comment_status, {$wpdb->posts}.ping_status, {$wpdb->posts}.post_password, {$wpdb->posts}.post_name, {$wpdb->posts}.to_ping, {$wpdb->posts}.pinged, {$wpdb->posts}.post_modified, {$wpdb->posts}.post_modified_gmt, {$wpdb->posts}.post_content_filtered, {$wpdb->posts}.post_parent, {$wpdb->posts}.guid, {$wpdb->posts}.menu_order, {$wpdb->posts}.post_type, {$wpdb->posts}.post_mime_type, {$wpdb->posts}.comment_count ";
				$sql1 = str_replace( $wpdb->posts.'.*', $select_fields, $sql1 );
			}
			$sql1 = str_replace( array( 'SQL_CALC_FOUND_ROWS', 'AND adverts.expire'), array( 'SQL_CALC_FOUND_ROWS 1 AS topad, ', 'AND '.$wpdb->posts.'.ID IN ('.join( ',', $ids ).') AND adverts.expire' ), $sql1 );
			$sql2 = str_replace( 'SQL_CALC_FOUND_ROWS', '0 AS topad, ', $sql2 );
			$offset = ( $paged - 1 ) * $posts_per_page;

			return "({$sql1}) UNION ALL ({$sql2}) ORDER BY topad DESC, {$orderby} LIMIT ".$wpdb->prepare( "%d, %d", $offset, $posts_per_page);
		}
		else{
			return $sql;
		}
	}


	function posts_request( $sql ){
		$top_ads = adifier_get_top_ads_list();
		$ids = array();
		if( !empty( $top_ads ) ){
			if( !empty( $_POST['category'] ) && !empty( $top_ads[$_POST['category']] ) ){
				$ids = array_keys( $top_ads[$_POST['category']] );
			}
			else{
				foreach( $top_ads as $term_id => $data ){
					$ids = array_merge( $ids, array_keys( $data ) );
				}
			}
		}
		if( !empty( $ids ) ){
			return self::posts_request_process( $sql, $ids, $this->args['posts_per_page'], $this->args['paged'] );
		}
		else{
			return $sql;
		}
	}

	/*
	* Select all values from adifier_advert_meta_data table
	*/
	static public function posts_main_fields( $sql ){
		return $sql . ", adverts.*, ".self::currency_filter()." AS sort_price ";
	}

	function posts_fields( $sql ) {
		return self::posts_main_fields( $sql );
	}

	/*
	* Join adifier_advert_data table and all relationships for range searches
	*/
	static public function posts_main_join( $sql ){
		global $wpdb;
		$sql .= " INNER JOIN {$wpdb->prefix}adifier_advert_data AS adverts ON $wpdb->posts.ID = adverts.post_id ";

		return $sql;
	}

	function posts_join( $sql ) {
		global $wpdb;
		$sql = self::posts_main_join( $sql );
		if( !empty( $this->args['tax_query_between'] ) ){
			foreach( $this->args['tax_query_between'] as $key => $data ){
				$unique = esc_sql( preg_replace("/[^a-z]+/", "", $data['taxonomy']) ).'_'.esc_sql( $key );
				$sql .= " LEFT JOIN {$wpdb->prefix}term_relationships AS btr{$unique} ON $wpdb->posts.ID = btr{$unique}.object_id ";			
			} 
		}

		return $sql;
	}

	static public  function posts_where_not_expired( $sql ){
		global $wpdb;

		$sql .= $wpdb->prepare( " AND adverts.expire > %d ", current_time('timestamp') );

		return $sql;
	}

	function posts_where( $sql ) {
		global $wpdb;

		/* if keyword is set and it is numeric search for ID as well */
		if( !empty( $this->args['s'] ) ){
			if( is_numeric( $this->args['s'] ) ){		
				$sql = str_replace( "({$wpdb->posts}.post_title LIKE", $wpdb->prepare( "({$wpdb->posts}.ID LIKE %s) OR ({$wpdb->posts}.post_title LIKE", $this->args['s'] ), $sql);
			}
		}		

		/* If we need to return all posts  - for profile listing */
		if( !$this->args['return_all'] && !$this->args['expired'] && !$this->args['sold'] ){
			$sql = self::posts_where_not_expired( $sql );
		}
		else if( $this->args['expired'] && !$this->args['sold'] ){
			$sql .= $wpdb->prepare( " AND adverts.expire <= %d ", current_time('timestamp') );	
		}

		/* Filtering by sold status */
		if( $this->args['sold'] ){
			$sql .= " AND adverts.sold = 1 ";
		}
		else if( !$this->args['return_all'] ){
			$sql .= " AND adverts.sold = 0 ";	
		}

		/* Filtering by type of advert */
		if( !empty( $this->args['type'] ) ){
			$sql .= $wpdb->prepare( " AND adverts.type = %d ", $this->args['type'] );
		}

		/* Filtering by tyconditionpe of advert */
		if( !empty( $this->args['cond'] ) ){
			$sql .= $wpdb->prepare( " AND adverts.cond = %d ", $this->args['cond'] );
		}

		/* Filtering by value from the range */
		if( !empty( $this->args['tax_query_between'] ) ){
			foreach( $this->args['tax_query_between'] as $key => $data ){
				$unique = esc_sql( preg_replace("/[^a-z]+/", "", $data['taxonomy']) ).'_'.esc_sql( $key );
				$query = $wpdb->prepare("SELECT t.term_id FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = %s AND t.slug ", $data['taxonomy']);				
				if( !empty( $data['terms'][0] ) && !empty( $data['terms'][1] ) ){
					$query .= $wpdb->prepare( 'BETWEEN %d AND %d', $data['terms'][0], $data['terms'][1] );
				}				
				else if( !empty( $data['terms'][0] ) ){
					$query .= $wpdb->prepare( '>= %d', $data['terms'][0] );
				}
				else if( !empty( $data['terms'][1] ) ){
					$query .= $wpdb->prepare( '<= %d', $data['terms'][1] );
				}
				$in_range_term_ids = $wpdb->get_col( $query );
				if( empty( $in_range_term_ids ) ){
					$in_range_term_ids = array( '0' );
				}
				$sql .= " AND btr{$unique}.term_taxonomy_id IN (".join(', ', $in_range_term_ids).") ";
			}
		}

		/* Filtering by location */
		if( !empty( $this->args['location'] ) ){
			$multiplicator = adifier_get_option( 'radius_units' ) == 'mi' ? 3959 : 6371;
			extract( $this->args['location'] );
			$sql .= $wpdb->prepare( " AND ( %d * ACOS ( COS ( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin ( radians(%f) ) * sin( radians( latitude ) ) ) ) <= %d ", $multiplicator, $latitude, $longitude, $latitude, $radius );
		}

		/* Filtering by price */
		if(  !empty( $this->args['price'][0] ) && !empty( $this->args['price'][1] ) ){
			$sort_price = self::currency_filter();
			$normalized_start_price = self::normalized_start_price(); /* this is used as max salary for job- offer */
			$sql .= $wpdb->prepare( " AND IF ( start_price > 0 AND type = 7, IF( ".$sort_price."  BETWEEN %d AND %d, 1, IF(".$normalized_start_price." BETWEEN %d AND %d, 1, IF( %d BETWEEN ".$sort_price." AND ".$normalized_start_price.", 1, IF( %d BETWEEN ".$sort_price." AND ".$normalized_start_price.", 1, 0)))), IF (".$sort_price." BETWEEN %d AND %d, 1, 0 )) = 1 ", $this->args['price'][0], $this->args['price'][1], $this->args['price'][0], $this->args['price'][1], $this->args['price'][0], $this->args['price'][1], $this->args['price'][0], $this->args['price'][1] );
		}
		else if(  !empty( $this->args['price'][0] ) ){
			$sort_price = self::currency_filter();
			$normalized_start_price = self::normalized_start_price();
			$sql .= $wpdb->prepare( " AND IF( start_price > 0 AND type = 7, IF( %d BETWEEN ".$sort_price." AND ".$normalized_start_price.", 1, 0), IF(".$sort_price." >= %d, 1, 0)) = 1 ", $this->args['price'][0], $this->args['price'][0] );
		}
		else if(  !empty( $this->args['price'][1] ) ){
			$sort_price = self::currency_filter();
			$normalized_start_price = self::normalized_start_price();
			$sql .= $wpdb->prepare( " AND IF( start_price > 0 AND type = 7, IF( %d BETWEEN ".$sort_price." AND ".$normalized_start_price.", 1, 0), IF(".$sort_price." <= %d, 1, 0)) = 1 ", $this->args['price'][1], $this->args['price'][1] );
		}

		/* Filtering by urgent */
		if( $this->args['urgent'] ){
			$sql .= $wpdb->prepare(" AND urgent >= %d ", current_time('timestamp') );
		}
		return $sql;
	}	

	function posts_orderby( $sql ){
		if( in_array( $this->args['orderby'], array( 'expire', 'views') ) ){
			$sql = ' adverts.'.$this->args['orderby'].' '.$this->args['order'];
		}
		else if( $this->args['orderby'] == 'price' ){
			$sql = ' sort_price '.$this->args['order'];	
		}

		return $sql;
	}

	/*
	* Normalize range of search
	*/
	static public function normalize_search_price( $price, $currency = '' ){
		$currencies = adifier_get_currencies();
		if( count( $currencies ) > 1 ){
			return $price * $currencies[$currency]['rate'];
		}

		return $price;
	}

	/* additional function will will normalize start_price which is being used as max_salary for Job - Offer type  ad type */
	static public function normalized_start_price(){
		$currencies = adifier_get_currencies();
		if( count( $currencies ) > 1 ){
			$start_price_list = "";
			foreach( $currencies as $currency ){
				$start_price_list .= " IF( currency = '".$currency['abbr']."', start_price * ".$currency['rate'].", ";
			}
			$sql = "IF( start_price > 0, ".$start_price_list."start_price".str_repeat( ")", count( $currencies ) ).", start_price )";
		}
		else{
			$sql = "start_price";
		}

		return $sql;
	}

	/* format if/else for multicurrency */
	static public function currency_filter(){
		$currencies = adifier_get_currencies();
		if( count( $currencies ) > 1 ){
			$sale_price_list = "";
			$price_list = "";
			foreach( $currencies as $currency ){
				$sale_price_list .= " IF( currency = '".$currency['abbr']."', sale_price * ".$currency['rate'].", ";
				$price_list .= " IF( currency = '".$currency['abbr']."', price * ".$currency['rate'].", ";
			}
			$sql = "IF( sale_price > 0, ".$sale_price_list."sale_price".str_repeat( ")", count( $currencies ) ).", ".$price_list."price".str_repeat( ")", count( $currencies ) )." )";
		}
		else{
			$sql = "IF( sale_price > 0, sale_price, price )";
		}

		return $sql;
	}
}
}

/*
* Hook into author query
*/
if( !function_exists('adifier_pre_get_posts_author') ){
function adifier_pre_get_posts_author( $query ){
	if( is_admin() ){
		return false;
	}
	global $adifier_widget_query;
	if( $query->is_author() && !is_single() && ( empty( $_GET['screen'] ) || !is_user_logged_in() ) && function_exists( 'adifier_create_post_types' )  ){
		$query->set( 'post_type', array( 'advert' ) );
		$query->set( 'orderby', 'date' );
		if( !empty( $_GET['filter-seller-ads'] ) ){
			$query->set( 's', $_GET['filter-seller-ads'] );
		}
		$query->set( 'order', 'DESC' );
		$query->query_vars['posts_per_page'] = adifier_get_option( 'adverts_per_page_author' );
	}
	else if( empty( $adifier_widget_query ) && ( is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ) ){
		$query->query_vars['posts_per_page'] = adifier_get_option( 'adverts_per_page_taxonomy' );
	}
}
add_action( 'pre_get_posts', 'adifier_pre_get_posts_author' );
}

/*
* Display top ads on category listing
*/
if( !function_exists('adifier_advert_category_posts_request') ){
function adifier_advert_category_posts_request( $sql ){
	global $adifier_widget_query;
	if( empty( $adifier_widget_query ) && ( is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ) ){
		global $wpdb;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$top_ads = adifier_get_top_ads_list();
		$id = get_queried_object()->term_id;
		$ids = array();
		if( !empty( $top_ads ) ){
			if( !empty( $top_ads[$id] ) ){
				$ids = array_keys( $top_ads[$id] );
			}
		}
		return Adifier_Advert_Query::posts_request_process( $sql, $ids, adifier_get_option( 'adverts_per_page_taxonomy' ), $paged );
	}
	return $sql;
}
add_filter( 'posts_request', 'adifier_advert_category_posts_request');
}


/*
* advert-category taxnonomy page select advert data
*/
if( !function_exists('adifier_advert_category_tax_fields') ){
function adifier_advert_category_tax_fields( $sql ){
	global $adifier_widget_query;
	if( empty( $adifier_widget_query ) && ( is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ) ){
		$sql = Adifier_Advert_Query::posts_main_fields( $sql );
	}

	return $sql;
}
add_filter( 'posts_fields', 'adifier_advert_category_tax_fields' );
}


/*
* advert-category taxnonomy page join advert table
*/
if( !function_exists('adifier_advert_category_tax_join') ){
function adifier_advert_category_tax_join( $sql ) {
	global $adifier_widget_query;
	$author_frontend_browse = false;
	if ( is_author() && function_exists( 'adifier_create_post_types' ) ){
		$author = adifier_get_author();
		if( ( adifier_is_own_account( $author->ID ) && !empty( $_GET['preview'] ) ) || !adifier_is_own_account( $author->ID ) ){
			$author_frontend_browse = true;
		}		
	}
	if( empty( $adifier_widget_query ) && ( $author_frontend_browse || is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ) ){
		$sql = Adifier_Advert_Query::posts_main_join( $sql );
	}

	return $sql;
}
add_filter( 'posts_join', 'adifier_advert_category_tax_join' );
}

/*
* advert-category taxnonomy page grab only not expired
*/
if( !function_exists('adifier_advert_category_tax_where') ){
function adifier_advert_category_tax_where( $sql ){
	global $wpdb, $adifier_widget_query;
	if( empty( $adifier_widget_query ) && ( is_tax( 'advert-category' ) || is_tax( 'advert-location' ) ) ){
		$sql = Adifier_Advert_Query::posts_where_not_expired( $sql );
	}
	else if( is_author() && function_exists( 'adifier_create_post_types' ) ){
		$author = adifier_get_author();
		if( ( adifier_is_own_account( $author->ID ) && !empty( $_GET['preview'] ) ) || !adifier_is_own_account( $author->ID ) ){
			global $wpdb;
			$sql .= " AND adverts.sold = 0 ";
		}
	}

	return $sql;
}
add_filter( 'posts_where', 'adifier_advert_category_tax_where' );
}


/*
*Check if is cached
*/
if( !function_exists('adifier_get_advert_data') ){
function adifier_get_advert_data( $post_id ){
	global $wpdb, $post;
	$data = wp_cache_get( $post_id, 'adifier_advert_meta' );
	if ( !$data ) {
		if( !empty( $post->expire ) ){
			$data = array(
				'meta_id'		=> $post->meta_id,
				'post_id'		=> $post->post_id,
				'latitude'		=> $post->latitude,
				'longitude'		=> $post->longitude,
				'price'			=> $post->price,
				'sale_price'	=> $post->sale_price,
				'expire'		=> $post->expire,
				'urgent'		=> $post->urgent,
				'sold'			=> $post->sold,
				'views'			=> $post->views,
				'views_data'	=> $post->views_data,
				'type'			=> $post->type,
				'cond'			=> $post->cond,
				'bids'			=> $post->bids,
				'start_price'	=> $post->start_price,
				'currency'		=> $post->currency,
			);
		}
		else{
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}adifier_advert_data WHERE post_id = %d", $post_id ), ARRAY_A );
		}		
        wp_cache_add( $post_id, $data, 'adifier_advert_meta' );
    }

    return $data;	
}
}

/*
*Clear cached advert meta
*/
if( !function_exists('adifier_clear_advert_meta') ){
function adifier_clear_advert_meta( $post_id ){
	wp_cache_delete( $post_id, 'adifier_advert_meta' );
}
}

/*
*Get advert meta
*/
if( !function_exists('adifier_get_advert_meta') ){
function adifier_get_advert_meta( $post_id, $meta_key, $single = false ){
	$meta_key = str_replace( 'advert_', '', $meta_key );
	$return = '';

	if( $meta_key == 'location' ){
		return get_post_meta( $post_id, 'advert_location' );
	}
	else{
		$data = adifier_get_advert_data( $post_id );
		if( !$single ){
			return array( $data[$meta_key] );
		}
		else{
			return $data[$meta_key];
		}
	}
}
}

/*
* Calculate new expire time
*/
if( !function_exists('adifier_calculate_expire_time') ){
function adifier_calculate_expire_time( $post_id ){
	$type = adifier_get_advert_meta( $post_id, 'type', true );
	if( $type == '2' ){
		$expire_time = adifier_get_option( 'auction_expires' );
	}
	else{
		$expire_time = adifier_get_option( 'regular_expires' );
	}
	if( !empty( $expire_time ) ){
		return current_time('timestamp') + ( $expire_time * 86400 );
	}
}
}

/*
* Save advert meta values and apply expire if it is not set
*/
if( !function_exists('adifier_save_advert_meta') ){
function adifier_save_advert_meta( $post_id, $meta_key, $meta_value ){
	global $wpdb;

	$meta_key = str_replace( 'advert_', '', $meta_key );

	if( is_array( $meta_value ) ){
		$meta_value = array_shift( $meta_value );
	}

	if( $meta_key == 'price' && !empty( $_POST['post_author'] ) ){
		if( $_POST['advert_type']['cmb-field-0'] == '2' && (int)$meta_value == 0 ){
			$meta_value = $_POST['advert_start_price']['cmb-field-0'];
		}
		if( $_POST['advert_type']['cmb-field-0'] == '8' ){
			$meta_value = 0;
		}
	}

	/* let's empty sale price for transition to job type */
	if( $meta_key == 'sale_price' && !empty( $_POST['post_author'] ) ){
		if( in_array( $_POST['advert_type']['cmb-field-0'], array( 7,8 ) )  ){
			$meta_value = 0;
		}
	}

	if( $meta_key == 'start_price' && !empty( $_POST['post_author'] ) ){
		if( !in_array( $_POST['advert_type']['cmb-field-0'], array( 2,7 ) )  ){
			$meta_value = 0;
		}
	}


	if( $meta_key == 'expire' ){
		if( empty( $meta_value ) ){
			$meta_value = adifier_calculate_expire_time( $post_id );
		}
	}

	$data = array(
		$meta_key => $meta_value
	);

	if( $meta_key == 'location' ){

		if( empty( $meta_value['lat'] ) && !empty( $_POST['post_author'] ) ){
			$meta_value = get_user_meta( $_POST['post_author'], 'location', true );
		}
		else{
			update_post_meta( $post_id, 'advert_location', $meta_value );
		}

		if( empty( $meta_value ) ){
			return false;
		}

		$data = array(
			'latitude' => $meta_value['lat'],
			'longitude' => $meta_value['long'],
		);
	}

	$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}adifier_advert_data WHERE post_id = %d", $post_id ) );
	if( $result ){
		$wpdb->update(
			$wpdb->prefix.'adifier_advert_data',
			$data,		
			array( 
				'post_id' => $post_id
			),
			array(
				'%s',
			),
			array(
				'%s',
			)
		);
	}
	else{
		$defaults = array(
			'meta_id'		=> '',
			'post_id' 		=> $post_id,
			'latitude' 		=> '',
			'longitude' 	=> '',
			'price' 		=> 0,
			'sale_price' 	=> '',
			'expire'		=> '',
			'urgent'		=> '',
			'sold'			=> 0,
			'views'			=> 0,
			'views_data'	=> '',
			'type'			=> 1,
			'cond'			=> 0,
			'bids'			=> 0,
			'start_price'	=> '',
			'currency'		=> ''
		);

		$vals = array_merge( $defaults, $data );

		$wpdb->insert(
			$wpdb->prefix.'adifier_advert_data',
			$vals,
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%f',
				'%f',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%f',
				'%s'
			)
		);
	}
}
}


/*
*  Get HTML for advert price
*/
if( !function_exists('adifier_get_advert_price') ){
function adifier_get_advert_price( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$currency = adifier_get_advert_meta( $post_id, 'currency', true );
	$price_html = '<div class="price">';
		$type = adifier_get_advert_meta( $post_id, 'type', true );
		if( $type == '1' ){
			$price = adifier_get_advert_meta( $post_id, 'price', true );
			if( $price > 0 ){
				$price = adifier_price_format( $price, $currency );
				$sale_price = adifier_get_advert_meta( $post_id, 'sale_price', true );
				$price_html .= ( (float)$sale_price > 0 )  ? '<span class="'.( strlen( $price ) >= 45 ? esc_attr('price-abs') : esc_attr('') ).'">'.$price.'</span>'.adifier_price_format( $sale_price, $currency ) : $price;
			}
			else{
				$price_html .= esc_html__( 'Call for price', 'adifier' );
			}
		}
		else if ( $type == '2' ){
			$currency = adifier_get_advert_meta( $post_id, 'currency', true );
			$price = adifier_price_format( adifier_get_advert_meta( $post_id, 'price', true ), $currency );
			$bids = adifier_get_advert_meta( $post_id, 'bids', true );
			$price_html .= $price.'<div class="price-bids"><span>'.$bids.' '.( $bids == '1' ? esc_html__( 'Bid', 'adifier' ) : esc_html__( 'Bids', 'adifier' ) ).'</span></div>';
		}
		else if( $type == '3' ){
			$price_html .= '<span class="text-price">'.esc_html__( 'Buying', 'adifier' ).'</span>';
			$price = adifier_get_advert_meta( $post_id, 'price', true );
			if( $price > 0 ){
				$price_html .= '<span class="no-strike">('.adifier_price_format( $price, $currency ).')</span>';
			}
		}
		else if( $type == '4' ){
			$price_html .= '<span class="text-price">'.esc_html__( 'Exchange', 'adifier' ).'</span>';
		}
		else if( $type == '5' ){
			$price_html .= '<span class="text-price">'.esc_html__( 'Gift', 'adifier' ).'</span>';
		}
		else if( $type == '6' ){
			$price = adifier_get_advert_meta( $post_id, 'price', true );

			if( $price > 0 ){
				$price_html .= adifier_price_format( $price, $currency );
				$rent_period = get_post_meta( $post_id, 'advert_rent_period', true );
				switch( $rent_period ){
					case '1' : $rent_period = esc_html__( '/ month', 'adifier' ); break;
					case '2' : $rent_period = esc_html__( '/ week', 'adifier' ); break;
					case '3' : $rent_period = esc_html__( '/ day', 'adifier' ); break;
					case '4' : $rent_period = esc_html__( '/ hour', 'adifier' ); break;
					default  : $rent_period = '';
				}

				$price_html .= !empty( $rent_period ) ? '<span class="no-strike">'.$rent_period.'</span>' : '';
			}
			else{
				$price_html .= esc_html__( 'Call for rent', 'adifier' );
			}			
		}
		else if( $type == '7' ){
			$price = adifier_get_advert_meta( $post_id, 'price', true );
			if( $price > 0 ){
				$price = adifier_price_format( $price, $currency );
				$start_price = adifier_get_advert_meta( $post_id, 'start_price', true );
				$price_html .= $price.( (float)$start_price > 0 ? ' - '.adifier_price_format( $start_price, $currency ) : '' );
			}
			else{
				$price_html .= esc_html__( 'Call for salary', 'adifier' );
			}
		}
		else if( $type == '8' ){
			$price_html .= '<span class="text-price">'.esc_html__( 'Job - Wanted', 'adifier' ).'</span>';
		}
	$price_html .= '</div>';

	return $price_html;
}
}

/*
* Get status of the advert
*/
if( !function_exists('adifier_get_advert_status') ){
function adifier_get_advert_status( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$approval_method = adifier_get_option( 'approval_method' );

	if( adifier_get_advert_meta( $post_id, 'sold', true ) == 1 ){
		return '<div class="status sold">'.esc_html__( 'Sold', 'adifier' ).'</div>';	
	}
	else if( adifier_is_expired() ){
		return '<div class="status expired">'.esc_html__( 'Expired', 'adifier' ).'</div>';	
	}
	if( $approval_method == 'manual' ){
		if( get_post_status() == 'draft' ){
			return '<div class="status pending">'.esc_html__( 'Pending', 'adifier' ).'</div>';
		}
		$children = get_children(array(
			'post_type' 	=> 'advert',
			'post_parent' 	=> get_the_ID(),
			'numberposts'	=> '1'
		));	
		if( count( $children ) > 0 ){
			return '<div class="status pending-update">'.esc_html__( 'Update', 'adifier' ).'</div>';
		}
	}

	return '<div class="status live">'.esc_html__( 'Live', 'adifier' ).'</div>';	
}
}

/*
* Get condition of the selling goods
*/
if( !function_exists('adifier_get_advert_condition') ){
function adifier_get_advert_condition( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$cond = adifier_get_advert_meta( $post_id, 'cond', true );

	if( empty( $cond ) ){
		return '';
	}

	switch( $cond ){
		case '1' : return esc_html__( 'New', 'adifier' ); break;
		case '2' : return esc_html__( 'Manufacturer Refurbished', 'adifier' ); break;
		case '3' : return esc_html__( 'Used', 'adifier' ); break;
		case '4' : return esc_html__( 'For Parts Or Not Working', 'adifier' ); break;
	}
}
}

/*
* Get Brand of the selling goods
*/
if( !function_exists('adifier_get_advert_brand') ){
function adifier_get_advert_brand( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$brand = adifier_get_advert_meta( $post_id, 'cond', true );

	if( empty( $brand ) ){
		return '';
	}

	switch( $brand ){
		case '1' : return esc_html__( 'Tata', 'adifier' ); break;
		case '2' : return esc_html__( 'JSW', 'adifier' ); break;
		case '3' : return esc_html__( 'Apollo', 'adifier' ); break;
		case '4' : return esc_html__( 'Metal junction', 'adifier' ); break;
		case '5' : return esc_html__( 'SAIL', 'adifier' ); break;
		case '6' : return esc_html__( 'ESSAR', 'adifier' ); break;
		case '7' : return esc_html__( 'Jindal', 'adifier' ); break;
		case '8' : return esc_html__( 'Imported', 'adifier' ); break;
	}
}
}


/*
* Check if the advert is expired
*/
if( !function_exists('adifier_is_expired') ){
function adifier_is_expired(  $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	if( empty( $post_id ) ){
		return false;
	}
	if( empty( adifier_get_advert_meta( $post_id, 'expire', true ) ) || adifier_get_advert_meta( $post_id, 'expire', true ) > current_time( 'timestamp' ) ){
		return false;
	}
	else{
		return true;
	}
}
}

/*
* Get category of the advert
*/
if( !function_exists('adifier_get_advert_category') ){
function adifier_get_advert_category( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$terms = wp_get_post_terms( $post_id, 'advert-category' );
	$parent = '';
	if( !empty( $terms ) && !is_wp_error( $terms ) ){
		foreach( $terms as $term ){
			if( $term->parent == 0 ){
				return '<a href="'.get_term_link( $term ).'">'.$term->name.'</a>';
			}
		}
	}
}
}

/*
* Get advert geo location
*/
if( !function_exists('adifier_get_advert_geo_location') ){
function adifier_get_advert_geo_location( $post = 0 ){
	$post_id = empty( $post->ID ) ? get_the_ID() : $post->ID;
	$location = get_post_meta( $post_id, 'advert_location', true );
	if( empty( $location ) ){
		$author_id = empty( $post->author ) ? get_the_author_meta('ID') : $post->author;
		$location = get_user_meta( $author_id, 'location', true );		
	}

	return $location;
}
}

/*
* Get advert location
*/
if( !function_exists('adifier_get_advert_location') ){
function adifier_get_advert_location( $post = 0 ){
	$post_id = empty( $post->ID ) ? get_the_ID() : $post->ID;
	$source = adifier_get_location_source( 'single_location_display' );
	if( $source == 'geo_value' ){
		$location = adifier_get_advert_geo_location();
		if( !empty( $location['city'] ) ){
			return $location['city'];
		}
	}
	else{
		$terms = wp_get_post_terms( $post_id, 'advert-location' );
		if( !empty( $terms ) && !is_wp_error( $terms ) ){
			$terms_naming = wp_list_pluck( $terms, 'name', 'term_id' );
			$parents = wp_list_pluck( $terms, 'parent' );
			$term_ids =  wp_list_pluck( $terms, 'term_id' );
			$term_id = array_diff( $term_ids, $parents );
			$term_id = array_shift( $term_id );

			return '<a href="'.esc_url( get_term_link( $term_id ) ).'">'.$terms_naming[$term_id].'</a>';
		}
	}
}
}

/*
* Get advert phone
*/
if( !function_exists('adifier_get_advert_phone') ){
function adifier_get_advert_phone( $post = 0 ){
	$post_id = empty( $post->ID ) ? get_the_ID() : $post->ID;
	$phone = get_post_meta( $post_id, 'advert_phone', true );
	if( empty( $phone ) ){
		$author_id = empty( $post->author ) ? get_the_author_meta('ID') : $post->author;
		$phone = get_user_meta( $author_id, 'phone', true );		
	}

	return $phone;
}
}

/*
* Get advert views
*/
if( !function_exists('adifier_get_advert_views') ){
function adifier_get_advert_views( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	return adifier_get_advert_meta( $post_id, 'views', true );
}
}

/*
* Increase number of views
*/
if( !function_exists('adifier_increase_advert_view') ){
function adifier_increase_advert_view(){
	$views = adifier_get_advert_meta( get_the_ID(), 'views', true );
	$views++;
	adifier_save_advert_meta( get_the_ID(), 'views', $views );
	adifier_views_chart_data();
}
}

/*
* Populate views data for chart display
*/
if( !function_exists('adifier_views_chart_data') ){
function adifier_views_chart_data(){
	$views_data = json_decode( adifier_get_advert_meta( get_the_ID(), 'views_data', true ), true );
	$time = strtotime( current_time( 'd-m-Y' ) );
	if( !empty( $views_data[$time] ) ){
		$views_data[$time]++;
	}
	else{
		$views_data[$time] = 1;
	}

	adifier_save_advert_meta( get_the_ID(), 'views_data', json_encode( $views_data ) );
}
}

/*
* check if the add is negotiable
*/
if( !function_exists('adifier_is_negotiable') ){
function adifier_is_negotiable( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$is_negotiable = get_post_meta( $post_id, 'advert_negotiable', true );
	if( $is_negotiable == 1 ){
		return true;
	}
	else{
		return false;
	}
}
}

/*
* check if the add is an urgent
*/
if( !function_exists('adifier_is_urgent') ){
function adifier_is_urgent( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$urgent = adifier_get_advert_meta( $post_id, 'urgent', true );
	if( $urgent > current_time('timestamp') ){
		return true;
	}
	else{
		return false;
	}
}
}

/*
* Check if the add is an urgent
*/
if( !function_exists('adifier_is_highlighted') ){
function adifier_is_highlighted( $post_id = 0 ){
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$promo_highlight = get_post_meta( $post_id, 'promo_highlight', true );
	if( $promo_highlight > current_time('timestamp') ){
		return true;
	}
	else{
		return false;
	}
}
}

/*
* Check if the add is an top ad
*/
if( !function_exists( 'adifier_is_topad' ) ){
function adifier_is_topad( $post = 0 ){
	$post = get_post( $post );
	if( isset( $post->topad ) ){
		return !empty( $post->topad ) ? true : false;
	}
	else{
		$ids = adifier_topads_ids_list();
		return in_array( $post->ID, $ids );
	}
}
}

/*
* Get array of top ads IDs
*/
if( !function_exists('adifier_topads_ids_list') ){
function adifier_topads_ids_list(){
	$topads = adifier_get_top_ads_list();
	$ids = array();
	if( !empty( $topads ) ){
		foreach( $topads as $ads ){
			$ids = array_merge( $ids, array_keys( $ads ) );
		}
	}

	return $ids;
}
}

/*
* get marker for advert based on category
*/
if( !function_exists('adifier_get_advert_marker') ){
function adifier_get_advert_marker( $post_id ){
	$cats = wp_get_post_terms( $post_id, 'advert-category' );
	$cats = adifier_taxonomy_hierarchy( $cats );
	$cat_ids = adifier_taxonomy_id_hierarchy( $cats );

	$cat_ids = array_reverse( $cat_ids );
	if( !empty( $cat_ids ) && !is_wp_error( $cat_ids ) ){
		foreach( $cat_ids as $cat_id ){
			$advert_cat_marker = get_term_meta( $cat_id, 'advert_cat_marker', true );
			if( !empty( $advert_cat_marker ) ){
				$temp = wp_get_attachment_image_src( $advert_cat_marker, 'full' );
				return $temp;
				break;
			}
		}
	}

	return '';
}
}

/*
* Get advert map data in array
*/
if( !function_exists('adifier_get_advert_map_data') ){
function adifier_get_advert_map_data( $post = 0 ){
	$post = get_post( $post );
	$marker_icon = adifier_get_advert_marker( $post->ID );
	return array(
		'latitude'		=> $post->latitude,
		'longitude'		=> $post->longitude,
		'icon'			=> !empty( $marker_icon[0] ) ? $marker_icon[0] : '',
		'width'			=> !empty( $marker_icon[1] ) ? $marker_icon[1] : '',
		'height'		=> !empty( $marker_icon[2] ) ? $marker_icon[2] : '',
		'id'			=> $post->ID
	);
}
}

/*
* Post data for map search
*/
if( !function_exists( 'adifier_get_map_lat_long' ) ){
function adifier_get_map_lat_long(){
	$data = adifier_get_advert_map_data();
	?>
	<div class="search-map-la-long hidden" data-id="<?php echo esc_attr( $data['id'] ) ?>" data-longitude="<?php echo esc_attr( $data['longitude'] ) ?>" data-latitude="<?php echo esc_attr( $data['latitude'] ) ?>" data-icon="<?php echo esc_attr( $data['icon'] ) ?>" data-iconwidth="<?php echo esc_attr( $data['width'] ) ?>" data-iconheight="<?php echo esc_attr( $data['height'] ) ?>"></div>
	<?php
}
}
?>