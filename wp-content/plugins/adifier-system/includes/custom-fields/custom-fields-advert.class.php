<?php
if( !class_exists('Adifier_Custom_Fields_Advert') ){
class Adifier_Custom_Fields_Advert{

	public static function launch(){
		add_action( 'adifier_amb_action', 'Adifier_Custom_Fields_Advert::cf_meta_box' );
		add_action( 'wp_ajax_adifier_get_cf', 'Adifier_Custom_Fields_Advert::get_cf' );
		add_action( 'wp_ajax_adifier_get_subfield', 'Adifier_Custom_Fields_Advert::get_subfield' );
		add_action( 'save_post_advert', 'Adifier_Custom_Fields_Advert::save_post', 10, 2 );
	}

	/*
	* Add fields on the project edit page
	*/
	public static function cf_meta_box(){
		adifier_amb(
			'adifier_advert_custom_fields',
			esc_html__( 'Additional Information', 'adifier' ),
			'Adifier_Custom_Fields_Advert::advert_custom_fields',
			'advert'
		);
	}

	/*
	* Print div wrapper for fields on advert edit page
	*/
	public static function advert_custom_fields(){
		?>
		<div class="adifier-advert-custom-fields">
			<?php self::no_custom_fields() ?>
		</div>
		<?php
	}

	private static function no_custom_fields(){
		?>
		<div class="no-custom-fields">
			<?php esc_html_e( 'Select categories to show custom fields', 'adifier' ); ?>
		</div>
		<?php
	}

	/*
	* generate HTMl for fields
	*/
	private static function generate_field( $field, $selected_terms ){
		echo '<div class="cf-field type_'.esc_attr( $field->cf_type ).'">';
			//if field type of Multiple Selection
			if( $field->cf_type == 1 ){
				$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
				$terms = Adifier_Custom_Fields_Search::sort_values( $terms, $field->cf_orderby );
				$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
				?>
				<div class="cf-subfield">
					<div class="flex-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
						<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
							<div class="styled-checkbox cf-no-terms">
								<input type="checkbox" class="no-terms-check" id="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms" />
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms"><?php esc_html_e( 'Value not found?', 'adifier' ) ?></label>
							</div>
						<?php endif; ?>
					</div>
					<div class="cf-values-wrap">
						<select name="cf_fields[type_1][<?php echo esc_attr( $field->cf_slug ) ?>][value][]" id="<?php echo esc_attr( $field->cf_slug ) ?>" multiple="multiple" class="select2-multiple">
							<?php
							foreach( $terms as $term ){
								echo '<option value="'.esc_attr( $term->term_id ).'" '.( in_array( $term->term_id, $selected_term_ids ) ? 'selected="selected"' : '' ).'>'.$term->name.'</option>';
							}  
							?>
						</select>
						<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
					</div>
				</div>

				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="cf-no-terms-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>_new"><?php esc_html_e( 'Input list of new values separated with pipe "|"', 'adifier' ) ?></label>
						<input type="text" name="cf_fields[type_1][<?php echo esc_attr( $field->cf_slug ) ?>][new]" id="<?php echo esc_attr( $field->cf_slug ) ?>_new" />
					</div>
				<?php endif; ?>
				<?php
			}
			//if field type of Single Select
			else if( $field->cf_type == 2 ){
				$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
				$terms = Adifier_Custom_Fields_Search::sort_values( $terms, $field->cf_orderby );
				$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
				?>			
				<div class="cf-subfield">
					<div class="flex-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
						<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
							<div class="styled-checkbox cf-no-terms">
								<input type="checkbox" class="no-terms-check" id="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms" />
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms"><?php esc_html_e( 'Value not found?', 'adifier' ) ?></label>
							</div>
						<?php endif; ?>
					</div>
					<div class="cf-values-wrap">
						<select name="cf_fields[type_2][<?php echo esc_attr( $field->cf_slug ) ?>][value]" id="<?php echo esc_attr( $field->cf_slug ) ?>" class="select2-single">
							<option value=""><?php esc_html_e( '-Select-', 'adifier' ) ?></option>
							<?php
							foreach( $terms as $term ){
								echo '<option value="'.esc_attr( $term->term_id ).'" '.( in_array( $term->term_id, $selected_term_ids ) ? 'selected="selected"' : '' ).'>'.$term->name.'</option>';
							}  
							?>
						</select>
						<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
					</div>
				</div>
				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="cf-no-terms-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>_new"><?php esc_html_e( 'Input new value', 'adifier' ) ?></label>
						<input type="text" name="cf_fields[type_2][<?php echo esc_attr( $field->cf_slug ) ?>][new]" id="<?php echo esc_attr( $field->cf_slug ) ?>_new" />
					</div>
				<?php endif; ?>
				<?php
			}
			//if field is type of single date
			else if( $field->cf_type == 3 ){
				$term = !empty($selected_terms) ? array_shift( $selected_terms ) : '';
				$value = !empty( $term ) ? $term->name : '';
				?>
				<label for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
				<input type="text" class="cf-datepicker" name="cf_fields[type_3][<?php echo esc_attr( $field->cf_slug ) ?>][value]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value="<?php echo !empty( $value ) ? esc_attr( date_i18n( 'm/d/Y', $value ) ) : '' ?>"/>
				<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				<?php
			}
			//if field is type of single input but numeric for range
			else if( $field->cf_type == 4 ){
				$term = !empty($selected_terms) ? array_shift( $selected_terms ) : '';
				$value = !empty( $term ) ? $term->name : '';
				?>
				<label for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
				<input type="number" step="any" name="cf_fields[type_4][<?php echo esc_attr( $field->cf_slug ) ?>][value]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value="<?php echo esc_attr( $value ) ?>"/>
				<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				<?php
			}
			//if it is nested field
			else if( $field->cf_type == 5 ){
				usort( $selected_terms, function ($a, $b) { return $a->term_id - $b->term_id; });
				$selected_term_ids = array_values( wp_list_pluck( $selected_terms, 'term_id' ) );
				?>				
				<div class="cf-subfield">
					<?php
					if( !empty( $selected_term_ids ) ){
						foreach( $selected_terms as $depth => $term ){
							self::nested_select( $field, $selected_term_ids, $term->parent, $depth );
						}
					}
					else{
						self::nested_select( $field, $selected_term_ids );
					}
					?>
					<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				</div>
				<input type="hidden" name="cf_fields[type_5][<?php echo esc_attr( $field->cf_slug ) ?>][levels]" value="<?php echo esc_attr( count( explode( '|', $field->cf_label ) ) ); ?>" />
				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="cf-no-terms-wrap">
						<div class="cf-nested-inputs">
							<?php self::nested_select_inputs_for_new( explode( '|', $field->cf_label ), $field->cf_slug ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php
			}
			// if it is color
			else if( $field->cf_type == 6 ){
				if( $field->cf_fixed == 0 ){
					$term = !empty($selected_terms) ? array_shift( $selected_terms ) : '';
					$value = !empty( $term ) ? $term->name : '';
					?>
					<div class="flex-wrap flex-center-v">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>" class="label-bottom-margin"><?php echo esc_html( $field->cf_label ) ?></label>
						<input type="text" class="cf-colorpicker" name="cf_fields[type_6][<?php echo esc_attr( $field->cf_slug ) ?>][new]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value="<?php echo esc_attr( $value ) ?>"/>
					</div>
					<?php
				}
				else{
					$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
					$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
					?>
					<label for="<?php echo esc_attr( $field->cf_slug ) ?>" class="label-bottom-margin"><?php echo esc_html( $field->cf_label ) ?></label>
					<ul class="list-unstyled list-inline color-search">
						<?php if( $field->cf_is_mandatory != 1 ): ?>
							<li class="colored-default">
								<input type="radio" id="color-none" name="cf_fields[type_6][<?php echo esc_attr( $field->cf_slug ) ?>][value]" value="">
								<label class="animation" for="color-none" style="background:#fff;"></label>
							</li>
						<?php endif; ?>
						<?php
						foreach( $terms as $term ){
							?>
							<li class="<?php echo in_array( $term->slug, array( 'fff', 'ffffff' ) ) ? esc_attr( 'colored-default' ) : esc_attr( '' ) ?>">
								<input type="radio" id="color-<?php echo esc_attr( $term->term_id ) ?>" name="cf_fields[type_6][<?php echo esc_attr( $field->cf_slug ) ?>][value]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, $selected_term_ids ) ? 'checked="checked"' : '' ?>>
								<label class="animation" for="color-<?php echo esc_attr( $term->term_id ) ?>" style="background:#<?php echo esc_attr( $term->slug ) ?>;"></label>
							</li>
							<?php
						}  
						?>
					</ul>					
					<?php
				}
				?>			
				<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				<?php
			}
			// if it is multiple colors
			else if( $field->cf_type == 7 ){
				if( $field->cf_fixed == 0 ){
					?>
					<div class="flex-wrap flex-center-v">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>" class="label-bottom-margin"><?php echo esc_html( $field->cf_label ) ?></label>
						<div class="flex-right">
							<?php
							if( !empty( $selected_terms ) ){
								foreach( $selected_terms as $term ){
									?>
									<div class="flex-wrap cf-multiple-color-item">
										<input type="text" class="cf-colorpicker" name="cf_fields[type_7][<?php echo esc_attr( $field->cf_slug ) ?>][new][]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value="<?php echo esc_attr( $term->name ) ?>"/> <a href="javascript:void(0);" class="cf-multiple-color-reset">X</a>
									</div>
									<?php
								}
							}
							else{
								?>
								<div class="flex-wrap cf-multiple-color-item">
									<input type="text" class="cf-colorpicker" name="cf_fields[type_7][<?php echo esc_attr( $field->cf_slug ) ?>][new][]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value=""/> <a href="javascript:void(0);" class="cf-multiple-color-reset">X</a>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<div class="text-right">
						<a href="javascript:void(0)" class="cf-another-color"><?php esc_html_e( '+ Add Color', 'adifier' ) ?></a>
					</div>					
					<?php
				}
				else{
					$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
					$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
					?>
					<label for="<?php echo esc_attr( $field->cf_slug ) ?>" class="label-bottom-margin"><?php echo esc_html( $field->cf_label ) ?></label>
					<ul class="list-unstyled list-inline color-search">
						<?php
						foreach( $terms as $term ){
							?>
							<li class="<?php echo in_array( $term->slug, array( 'fff', 'ffffff' ) ) ? esc_attr( 'colored-default' ) : esc_attr( '' ) ?>">
								<input type="checkbox" id="color-<?php echo esc_attr( $term->term_id ) ?>" name="cf_fields[type_7][<?php echo esc_attr( $field->cf_slug ) ?>][value][]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, $selected_term_ids ) ? 'checked="checked"' : '' ?>>
								<label class="animation" for="color-<?php echo esc_attr( $term->term_id ) ?>" style="background:#<?php echo esc_attr( $term->slug ) ?>;"></label>
							</li>
							<?php
						}  
						?>
					</ul>					
					<?php
				}
				?>			
				<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				<?php
			}
			//if field is type of single input but numeric for range of inputs
			else if( $field->cf_type == 8 ){
				$term = !empty($selected_terms) ? array_shift( $selected_terms ) : '';
				$value = !empty( $term ) ? $term->name : '';
				?>
				<label for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
				<input type="number" step="any" name="cf_fields[type_8][<?php echo esc_attr( $field->cf_slug ) ?>][value]" id="<?php echo esc_attr( $field->cf_slug ) ?>" value="<?php echo esc_attr( $value ) ?>"/>
				<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
				<?php
			}
			//if field is type of checkboxes 
			else if( $field->cf_type == 9 ){
				$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
				$terms = Adifier_Custom_Fields_Search::sort_values( $terms, $field->cf_orderby );
				$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
				?>
				<div class="cf-subfield">
					<div class="flex-wrap">
						<label class="label-bottom-margin" for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
						<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
							<div class="styled-checkbox cf-no-terms">
								<input type="checkbox" class="no-terms-check" id="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms" />
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms"><?php esc_html_e( 'Value not found?', 'adifier' ) ?></label>
							</div>
						<?php endif; ?>
					</div>
					<div class="cf-values-wrap">
						<?php
						$counter = 0;
						foreach( $terms as $term ){
							?>
							<div class="styled-checkbox">
								<input type="checkbox" name="cf_fields[type_9][<?php echo esc_attr( $field->cf_slug ) ?>][value][]" id="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $selected_term_ids ) ? 'checked="checked"' : '' ?>>
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>"><div><?php echo $term->name; ?></div></label>								
							</div>
							<?php
							$counter++;
						}  
						?>
						<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
					</div>
				</div>

				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="cf-no-terms-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>_new"><?php esc_html_e( 'Input list of new values separated with pipe "|"', 'adifier' ) ?></label>
						<input type="text" name="cf_fields[type_9][<?php echo esc_attr( $field->cf_slug ) ?>][new]" id="<?php echo esc_attr( $field->cf_slug ) ?>_new" />
					</div>
				<?php endif; ?>
				<?php
			}
			//if field is type of radioboxes 
			else if( $field->cf_type == 10 ){
				$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
				$terms = Adifier_Custom_Fields_Search::sort_values( $terms, $field->cf_orderby );
				$selected_term_ids = wp_list_pluck( $selected_terms, 'term_id' );
				?>
				<div class="cf-subfield">
					<div class="flex-wrap">
						<label class="label-bottom-margin" for="<?php echo esc_attr( $field->cf_slug ) ?>"><?php echo esc_html( $field->cf_label ) ?></label>
						<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
							<div class="styled-checkbox cf-no-terms">
								<input type="checkbox" class="no-terms-check" id="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms" />
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms"><?php esc_html_e( 'Value not found?', 'adifier' ) ?></label>
							</div>
						<?php endif; ?>
					</div>
					<div class="cf-values-wrap">
						<?php
						$counter = 0;
						foreach( $terms as $term ){
							?>
							<div class="styled-radio">
								<input type="radio" name="cf_fields[type_10][<?php echo esc_attr( $field->cf_slug ) ?>][value]" id="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $selected_term_ids ) ? 'checked="checked"' : '' ?>>
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>"><div><?php echo $term->name; ?></div></label>								
							</div>
							<?php
							$counter++;
						}  
						?>
						<p class="description"><?php echo esc_html( $field->cf_description ) ?></p>
					</div>
				</div>

				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="cf-no-terms-wrap">
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>_new"><?php esc_html_e( 'Input new value', 'adifier' ) ?></label>
						<input type="text" name="cf_fields[type_10][<?php echo esc_attr( $field->cf_slug ) ?>][new]" id="<?php echo esc_attr( $field->cf_slug ) ?>_new" />
					</div>
				<?php endif; ?>
				<?php
			}			
		echo '</div>';
	}

	/*
	* Build select blog for nested selects
	*/
	public static function nested_select( $field, $selected_term_ids, $parent = 0, $depth = 0 ){
		$terms = get_terms(array(
			'taxonomy'		=> $field->cf_slug,
			'parent'		=> $parent,
			'hide_empty'	=> false
		));

		$terms = Adifier_Custom_Fields_Search::sort_values( $terms, $field->cf_orderby );
		
		$label = explode( '|', $field->cf_label );
		?>
		<div class="nested-field-wrap <?php echo  $depth > 0 ? esc_attr( 'cf-nested depth_'.$depth) : esc_attr( '' ) ?>">
			<div class="flex-wrap">
				<label for="<?php echo esc_attr( $field->cf_slug ) ?>-<?php echo esc_attr( $depth ) ?>"><?php echo esc_html( $label[$depth] ) ?></label>
				<?php if( $field->cf_fixed == 0 || current_user_can('edit_published_posts') ): ?>
					<div class="styled-checkbox cf-no-terms">
						<input type="checkbox" class="no-terms-check" id="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms-<?php echo esc_attr( $depth ) ?>" data-depth="<?php echo esc_attr( $depth ) ?>" data-maxdepth="<?php echo esc_attr( sizeof( $label ) - 1 ) ?>"/>
						<label for="<?php echo esc_attr( $field->cf_slug ) ?>_no_terms-<?php echo esc_attr( $depth ) ?>"><?php esc_html_e( 'Value not found?', 'adifier' ) ?></label>
					</div>
				<?php endif; ?>
			</div>
			<select class="cf-no-change select2-single" name="cf_fields[type_5][<?php echo esc_attr( $field->cf_slug ) ?>][value][depth_<?php echo esc_attr( $depth ) ?>]" id="<?php echo esc_attr( $field->cf_slug ) ?>-<?php echo esc_attr( $depth ) ?>" data-depth="<?php echo esc_attr( $depth ) ?>" data-maxdepth="<?php echo esc_attr( sizeof( $label ) - 1 ) ?>" data-fieldid="<?php echo esc_attr( $field->cf_id ) ?>">
				<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
				<?php
				if( !empty( $terms ) ){
					foreach( $terms as $term ){
						?>
						<option value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, $selected_term_ids ) ? 'selected="selected"' : '' ?>><?php echo esc_html( $term->name ) ?></option>
						<?php
					}
				}
				?>
			</select>	
		</div>		
		<?php
	}

	/*
	* get subfield of the selected value
	*/
	public static function get_subfield(){
		$field_id = !empty( $_POST['field_id'] ) ? $_POST['field_id'] : '';
		$depth = !empty( $_POST['depth'] ) ? $_POST['depth'] : '';
		$value = !empty( $_POST['value'] ) ? $_POST['value'] : '';
		$field = Adifier_Admin_Custom_Fields::get_custom_field_by_id( $field_id );
		if( !empty( $field ) ){
			self::nested_select( $field, array(), $value, $depth );
		}
		die();
	}

	/*
	* Helper function for the deeper levels of adding new nested select
	*/
	private static function nested_select_inputs_for_new( $hierarchy_labels, $slug ){
		if( !empty( $hierarchy_labels ) ){
			foreach( $hierarchy_labels as $count => $label ){
				?>
				<div class="cf-new-hierarchy-label <?php echo esc_attr( 'depth_'.$count ) ?>">
					<label><?php echo sprintf( esc_html__( 'New %s', 'adifier' ), $label ) ?></label>
					<input type="text" name="cf_fields[type_5][<?php echo esc_attr( $slug ) ?>][new][depth_<?php echo esc_attr( $count ) ?>]" placeholder="<?php echo sprintf( esc_html__( 'Type new %s here...', 'adifier' ), $label ) ?>">
				</div>
				<?php
			}
		}
	}

	/*
	* Fetch custom fields
	*/
	public static function get_cf( $terms = array(), $post_id = 0 ){
		global $wpdb;
		$terms = !empty( $_POST['terms'] ) ? $_POST['terms'] : $terms;
		$post_id = !empty( $_POST['post_id'] ) ? $_POST['post_id'] : $post_id;
		if( !empty( $terms ) ){
			$fields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}adifier_cf AS cf LEFT JOIN {$wpdb->prefix}adifier_cf_groups AS cf_groups ON cf.group_id = cf_groups.group_id WHERE CONCAT( ',', categories, ',' ) REGEXP ',(".esc_sql( implode('|', $terms) )."),' ORDER BY cf_order ASC");
			if( !empty( $fields ) ){
				foreach( $fields as $field ){
					$selected_terms = wp_get_object_terms( $post_id, $field->cf_slug );
					self::generate_field( $field, $selected_terms );
				}
			}
		}
		else{
			self::no_custom_fields();
		}
		
		if( isset( $_POST['post_id'] ) || isset( $_POST['terms'] ) ){
			die();
		}
	}

	/*
	* helper for inserting new terms
	*/
	private static function insert_term( $term_name, $slug, $args = array() ){
		$term_name = (string)$term_name;
		$term = term_exists( $term_name, $slug );
		if( !empty( $term ) ){
			return $term;
		}
		return wp_insert_term(
			$term_name,
			$slug,
			$args
		);
	}


	/**
	* Handle submit of the advert
	*/
	public static function save_post( $post_id, $post ){
		if( !empty( $_GET['page'] ) && $_GET['page'] == 'pmxi-admin-import' ){
			return;
		}
		
		if( isset( $_POST['_inline_edit'] ) ){
			return;
		}

		//first get all custom fields assigned to current post and unset it
		$attached_taxonomies = get_object_taxonomies( 'advert', 'objects' );
		$unset_taxonomies = array();
		foreach( $attached_taxonomies as $taxonomy_slug => $taxonomy_data ){
			if( !in_array( $taxonomy_slug, array( 'advert-category', 'advert-location' ) ) ){
				$unset_taxonomies[] = $taxonomy_slug;
			}
		}
		if( !empty( $unset_taxonomies ) ){
			wp_delete_object_term_relationships( $post_id, $unset_taxonomies );
		}

		if( !empty( $_POST['cf_fields'] ) ){
			foreach( $_POST['cf_fields'] as $type => $fields ){
				if( !empty( $fields ) ){
					foreach( $fields as $slug => $data ){
						//if field is not hierarchical
						if( $type !== 'type_5' ){
							//if there is some new things to add
							if( !empty( $data['new'] ) ){
								//convert to array since we have muliple selection possible
								$data['new'] = !is_array( $data['new'] ) ? explode( '|', $data['new'] ) : $data['new'];
								foreach( $data['new'] as $term_name ){
									if( !empty( $term_name ) ){
										//insert new term and if it is OK add it to the list of the value
										$term = self::insert_term( $term_name, $slug );
										if( !is_wp_error( $term ) ){
											$data['value'][] = $term['term_id'];
										}
									}
								}
							}
							//if it is single value for date or numeric value is treated just like data[new]
							else if( $type == 'type_3' ){
								$term = self::insert_term( strtotime( $data['value'] ), $slug );
								if( !is_wp_error( $term ) ){
									$data['value'] = array( $term['term_id'] );
								}
							}
							else if( $type == 'type_4' || $type == 'type_8' ){
								$term = self::insert_term( $data['value'] , $slug );
								if( !is_wp_error( $term ) ){
									$data['value'] = array( $term['term_id'] );
								}
							}
							// else if( $type == 'type_6' ){
							// 	if( !empty( $data['value_id'] )) {
							// 		$data['value'] = array( $data['value_id'] );
							// 	}
							// 	else{
							// 		$term = self::insert_term( $data['value'], $slug );
							// 		if( !is_wp_error( $term ) ){
							// 			$data['value'] = array( $term['term_id'] );
							// 		}
							// 	}
							// }
							// else if( $type == 'type_7' ){
							// 	if( !empty( $data['value_ids'] )) {
							// 		$data['value'] = $data['value_ids'];
							// 	}
							// 	else if( !empty( $data['values'] ) ){
							// 		foreach( $data['values'] as $value ){
							// 			$term = self::insert_term( $value, $slug );
							// 			if( !is_wp_error( $term ) ){
							// 				$data['value'][] = absint( $term['term_id'] );
							// 			}
							// 		}
							// 	}								
							// }
							//if we have some terms then set it to post
							if( !empty( $data['value'] ) ){
								//we need to convert all values to int othervise new terms will be created
								$term_ids = array_map('intval', (array)$data['value']);
								wp_set_post_terms( $post_id, $term_ids, $slug );
							}
						}
						//if field is hierarchical
						else{
							//if we have new fields to add
							//first check if there are new values and if their number is equal to level size
							$term_ids = array();
							for( $i=0; $i<$data['levels']; $i++ ){
								if( !empty( $data['value']['depth_'.$i] ) ){
									$term_ids[] = $data['value']['depth_'.$i];
								}
								else if( !empty( $data['new']['depth_'.$i] ) ){
									$term = self::insert_term(
										$data['new']['depth_'.$i],
										$slug,
										array(
											'parent'	=> 	sizeof( $term_ids ) > 0 ? $term_ids[sizeof( $term_ids ) - 1] : 0
										)
									);
									$term_ids[] = absint( $term['term_id'] );
								}
							}

							if( !empty( $term_ids ) && sizeof( $term_ids ) == $data['levels'] ){
								wp_set_post_terms( $post_id, $term_ids, $slug );
							}						

						}
					}
				}
			}
		}
	}
}
Adifier_Custom_Fields_Advert::launch();
}
?>