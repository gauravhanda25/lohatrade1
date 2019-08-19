<?php
if( !class_exists( 'Adifier_Import_Export' ) ){
class Adifier_Import_Export{

	private $_tables;

	public function __construct(){
		add_action('admin_menu', array( $this, 'add_menu_item' ));
		$this->_tables = array(
			'adifier_advert_data',
			'adifier_bids',
			'adifier_cf',
			'adifier_cf_groups',
			'adifier_conversations',
			'adifier_conversation_messages',
			'adifier_reviews'
		);
	}

	public function add_menu_item(){
		adifier_menu_page( esc_html__( 'Export / Import Custom Data', 'adifier' ), esc_html__( 'Export / Import Custom Data', 'adifier' ), 'manage_options', 'adifier_export_import', array( $this, 'import_export_page' ) );
	}

	/*
	*	 Display import export page
	*/
	public function import_export_page(){
		?>
		<div class="wrap">

			<h2><?php esc_html_e( 'Import / Export Custom Data From Theme', 'adifier' ) ?> </h2>

			<h3><?php esc_html_e( 'Export', 'adifier' ) ?></h3>
			<div class="adifier-export-wrap">
				<?php $this->_export(); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'export' ) ) ) ?>" class="button"><?php esc_html_e( 'Export', 'adifier' ) ?></a>
				<p class="description"><?php esc_html_e( 'Click button below to get JSON export of your created fields which you can later import back using form below', 'adifier' ) ?></p>
			</div>

			<h3><?php esc_html_e( 'Import', 'adifier' ) ?></h3>
			<div class="adifier-import-wrap">

				<?php $this->_import(); ?>

				<form method="post" action="<?php echo esc_url( add_query_arg( array( 'action' => 'cd_import' ) ) ) ?>">
					<div class="adifier-import-group-wrap">
						<label><?php esc_html_e( 'Which table to import to', 'adifier' ) ?></label>
						<select name="adifier_import_table">
							<?php
							foreach( $this->_tables as $table ){
								?>
								<option value="<?php echo esc_attr( $table ) ?>"><?php echo esc_html( $table ) ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="adifier-import-group-wrap">
						<label><?php esc_html_e( 'Table content', 'adifier' ) ?></label>
						<textarea name="adifier_import_data"></textarea>
						<p class="description"><?php esc_html_e( 'Paste JSON of your custom data and click on import button', 'adifier' ) ?></p>
					</div>

					<input type="submit" class="button" value="<?php esc_attr_e( 'Import', 'adifier' ) ?>">
				</form>
			</div>
		</div>		
		<?php
	}

	/*
	* Export custom data values
	*/
	private function _export(){
		if( !empty( $_GET['action'] ) && $_GET['action'] == 'export' ){
			global $wpdb;
			$counter = 0;
			foreach( $this->_tables as $table ){
				$data = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}".esc_sql( $table ), ARRAY_A );
				$counter++;
				?>
				<div class="adifier-import-group-wrap">
					<div class="adifier-flex-label">
						<span class="adifier-export-counter"><?php echo esc_html( $counter ); ?></span>
						<label><?php echo esc_html__( 'Save content from textarea below in txt file and name it', 'adifier' ).' <b>'.$table.'</b>'; ?></label>
					</div>
					<textarea><?php echo json_encode( $data ); ?></textarea>
				</div>
				<?php
			}
		}
	}

	/*
	* Import custom data values
	*/
	private function _import(){
		if( !empty( $_POST['adifier_import_data'] ) ){
			self::do_import( $_POST['adifier_import_table'], $_POST['adifier_import_data'], true );
		}
	}

	/*
	* Do import which is also called from content importer
	*/
	static public function do_import( $table, $data, $show_result = false ){
		global $wpdb;
		$try = json_decode( stripslashes( $data ), true );
		if( json_last_error() > 0 ){
			$try = json_decode( $data, true );
		}

		if( !empty( $try ) ){
			foreach( $try as $row ){
				$info = $wpdb->insert(
					$wpdb->prefix.$table,
					$row
				);
			}
			if( $show_result ):
				?>
				<div class="updated notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Import process finished', 'adifier' ) ?></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'adifier' ) ?></span></button>
				</div>
				<?php
			endif;
		}		
	}
}
}

$adifier_import_export = new Adifier_Import_Export();
?>