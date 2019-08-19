<?php
/**
 * Version 0.0.3
 *
 * This file is just an example you can copy it to your theme and modify it to fit your own needs.
 * Watch the paths though.
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'Radium_Theme_Demo_Data_Importer' ) ) {

	require_once( plugin_dir_path( __FILE__ ).'importer/radium-importer.php' ); //load admin theme data importer

	class Radium_Theme_Demo_Data_Importer extends Radium_Theme_Importer {

		/**
		 * Set framewok
		 *
		 * options that can be used are 'default', 'radium' or 'optiontree'
		 *
		 * @since 0.0.3
		 *
		 * @var string
		 */
		public $theme_options_framework = 'default';

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.1
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Set the key to be used to store theme options
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_option_name       = 'adifier_options'; //set theme options name here (key used to save theme options). Optiontree option name will be set automatically

		/**
		 * Set name of the theme options file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_options_file_name = 'theme_options.txt';

		/**
		 * Set name of the widgets json file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widgets_file_name       = 'widgets.json';

		/**
		 * Set name of the content file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $content_demo_file_name  = 'content.xml';

		/**
		 * Holds a copy of the widget settings
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widget_import_results;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 0.0.1
		 */
		public function __construct() {

			$this->demo_files_path = plugin_dir_path( __FILE__ ).'demo-files/'; //can
			add_action( 'radium_importer_after_content_import', array($this, 'kc_data') );
			if( !get_option( 'radium_imported_demo' ) ){
				add_action( 'init', array($this, 'import_custom_data') );
			}
			self::$instance = $this;
			parent::__construct();

		}

		public function kc_data(){
			global $wpdb;
			$update_pages = array(
				'Home',
				'Home Page 2',
				'Home Page 3',
				'Home Page 4',
				'Home Page 5',
				'Home Page 6',
				'Home Page 7',
				'Home Page 8',
			);
			$new_data = maybe_unserialize('a:7:{s:4:"mode";s:2:"kc";s:7:"classes";s:0:"";s:3:"css";s:0:"";s:9:"max_width";s:0:"";s:9:"thumbnail";s:0:"";s:9:"collapsed";s:0:"";s:9:"optimized";s:0:"";}');
			foreach( $update_pages as $page_title ){
				$page = get_page_by_title( $page_title );
				if( !empty( $page->ID ) ){
					delete_post_meta( $page->ID, 'kc_data' );
					update_post_meta( $page->ID, 'kc_data', $new_data );
				}
			}

			/* let's update things for custom fields */
			$cfs = array(
				'Cars CF' 			=> array( 'vehicles', 'boats-watercrafts', 'campers', 'cars', 'motocycles', 'snowmobiles', 'trucks' ),
				'Electronics CF'	=> array( 'electronics', 'computers', 'drones', 'notebooks', 'phones', 'watches' ),
				'Jobs CF'			=> array( 'jobs', 'dancers', 'drivers', 'film-stunts', 'graphic-web-design' ),
				'Real Estate CF' 	=> array( 'real-estate', 'appartments', 'houses', 'mansons' ),
				'Services CF'		=> array( 'services', 'builders', 'car-mechanic', 'moving-storage' )
			);

			foreach( $cfs as $cf_group => $slugs ){
				$term_ids = get_terms(array(
					'taxonomy'	=> 'advert-category',
					'fields' 	=> 'ids',
					'slug'		=> $slugs
				));

				if( !empty( $term_ids ) ){
					$wpdb->update(
						$wpdb->prefix.'adifier_cf_groups',
						array(
							'categories' => join( ',', $term_ids )
						),
						array(
							'name' => $cf_group
						),
						array(
							'%s'
						),
						array(
							'%s'
						)
					);
				}
			} 
		}

		/**
		 * Add menus - the menus listed here largely depend on the ones registered in the theme
		 *
		 * @since 0.0.1
		 */
		public function set_demo_menus(){

			// Menus to Import and assign - you can remove or add as many as you want
			$top_menu = get_term_by('name', 'Main Menu', 'nav_menu');

			set_theme_mod( 'nav_menu_locations', array(
					'main-navigation' => $top_menu->term_id,
				)
			);

			$this->flag_as_imported['menus'] = true;

			/* Assign Home and Blog page */
			$home = get_page_by_title('Home');
			update_option('page_on_front',$home->ID);
			update_option('show_on_front','page');

			$blog = get_page_by_title('News');
			update_option('page_for_posts',$blog->ID);			

		}

		/*
		* Import custom data
		*/
		public function import_custom_data(){
	    	if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'demo-data' ){
				/*
				*  First import all data to custom tables
				*/
				$tables = array(
					'adifier_advert_data',
					'adifier_bids',
					'adifier_cf',
					'adifier_cf_groups',
					'adifier_conversations',
					'adifier_conversation_messages',
					'adifier_reviews'
				);

				foreach( $tables as $table ){
					$data = file_get_contents( $this->demo_files_path.$table.'.txt' );
					Adifier_Import_Export::do_import( $table, $data );
				}
	    	}	
	    }	

	}

	new Radium_Theme_Demo_Data_Importer;

}