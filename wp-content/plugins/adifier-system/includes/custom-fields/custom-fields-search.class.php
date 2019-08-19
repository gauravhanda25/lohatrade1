<?php
/*
Class for handling custom fields on search page
*/
if( !class_exists('Adifier_Custom_Fields_Search') ){
class Adifier_Custom_Fields_Search{

	/*
	* Connect to different actions
	*/
	public static function launch(){
		add_action( 'wp_ajax_adifier_get_cf_filter', 'Adifier_Custom_Fields_Search::get_cf_filter_ajax' );
		add_action( 'wp_ajax_nopriv_adifier_get_cf_filter', 'Adifier_Custom_Fields_Search::get_cf_filter_ajax' );
		add_action( 'wp_ajax_adifier_get_filter_subfield', 'Adifier_Custom_Fields_Search::get_subfield' );
		add_action( 'wp_ajax_nopriv_adifier_get_filter_subfield', 'Adifier_Custom_Fields_Search::get_subfield' );
	}

	/*
	*  Start dispalying fields layout on seearch sidebar
	*/
	static public function get_cf_filter( $category_id ){
		$fields = self::get_fields_by_category_id( $category_id, false );
		if( !empty( $fields ) ){
			foreach( $fields as $field ){
				!empty( $_REQUEST ) ? extract( $_REQUEST ) : '';
				$selected_values = !empty( $cf_fields[$field->cf_slug] ) ? $cf_fields[$field->cf_slug] : array();
				self::generate_field( $field, $selected_values );
			}
		}
	}

	/*
	* Get custom fields based on the selected category
	*/
	static public function get_fields_by_category_id( $category_id, $show_hidden = true ){
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}adifier_cf AS cf LEFT JOIN {$wpdb->prefix}adifier_cf_groups AS cf_groups ON cf.group_id = cf_groups.group_id WHERE FIND_IN_SET( %d, categories ) ".( $show_hidden ? '' : ' AND cf.cf_is_hidden = 0 ' )." ORDER BY cf_order ASC", $category_id));
	}

	/*
	* Grap prefix from type4 fields which is wrapped in [] and will be applied as prefix for value
	*/
	static public function get_field_type4_prefix( $label ){
		preg_match('#\[(.*?)\]#', $label, $match);
		return !empty( $match[1] ) ? $match[1] : '';
	}

	/*
	* Grap sufix from type4 fields which is wrapped in [] and will be applied as prefix for value
	*/
	static public function get_field_type4_sufix( $label ){
		preg_match('#\((.*?)\)#', $label, $match);
		return !empty( $match[1] ) ? $match[1] : '';
	}

	/*
	* Grap clean label for type4 field
	*/
	static public function get_field_type4_clean_label( $label ){
		return preg_replace( array( '#\((.*?)\)#', '#\[(.*?)\]#' ), '', $label );
	}	

	/*
	* Sort values
	*/
	static public function sort_values( $terms, $sort ){
		if( $sort == 'name' ){
			usort($terms, function($a, $b) {
			    return strcmp( $a->name, $b->name );
			});
		}
		else if( $sort == 'name_num' ){
			usort($terms, function($a, $b) {
			    return (float)$a->slug > (float)$b->slug ? 1 : -1;
			});
		}
		else if( $sort == 'id' ){
			usort($terms, function($a, $b) {
			    return $a->term_id > $b->term_id ? 1 : -1;
			});
		}		

		return $terms;
	}

	/*
	* Generate field HTML based on field type
	*/
	static public function generate_field( $field, $selected_values ){
		if( $field->cf_type == 1 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			$terms = self::sort_values( $terms, $field->cf_orderby );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label></label>
					<select name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][]" multiple="multiple" class="select2-multiple">
						<?php
						foreach( $terms as $term ){
							echo '<option value="'.esc_attr( $term->term_id ).'" '.( in_array( $term->term_id, $selected_values ) ? 'selected="selected"' : '' ).'>'.$term->name.'</option>';
						}  
						?>
					</select>
				</div>
				<?php
			}
		}
		else if( $field->cf_type == 2 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			$terms = self::sort_values( $terms, $field->cf_orderby );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label></label>
					<select name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" class="select2-single">
						<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
						<?php
						foreach( $terms as $term ){
							echo '<option value="'.esc_attr( $term->term_id ).'" '.( selected( $selected_values, $term->term_id, false ) ).'>'.$term->name.'</option>';
						}  
						?>
					</select>
				</div>
				<?php
			}
		}
		else if( $field->cf_type == 3 ){
			$min_date = !empty( $selected_values['min'] ) ? $selected_values['min'] : '';
			$max_date = !empty( $selected_values['max'] ) ? $selected_values['max'] : '';
			?>
			<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
				<label><?php echo esc_html( $field->cf_label ) ?></label></label>
				<div class="date-range">
					<input type="text" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][min]" value="<?php echo esc_attr( $min_date ) ?>" class="cf-datepicker" placeholder="<?php esc_attr_e( 'From', 'adifier' ) ?>"/>
					<input type="text" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][max]" value="<?php echo esc_attr( $max_date ) ?>" class="cf-datepicker" placeholder="<?php esc_attr_e( 'To', 'adifier' ) ?>"/>
				</div>
			</div>
			<?php
		}
		else if( $field->cf_type == 4 ){
			$range = !empty( $selected_values ) ? $selected_values : '';
			$sufix = self::get_field_type4_sufix( $field->cf_label );
			$prefix = self::get_field_type4_prefix( $field->cf_label );
			$max_value = self::get_max_cf_slide_range( $field->cf_slug );
			$min_value = self::get_min_cf_slide_range( $field->cf_slug );
			
			if( $max_value > 0 ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo self::get_field_type4_clean_label( $field->cf_label ) ?></label></label>
					<div class="slider-wrap slider-range">
						<div class="filter-slider" data-range="true" data-min="<?php echo esc_attr( $min_value ) ?>" data-max="<?php echo esc_attr( $max_value ) ?>" data-prefix="<?php echo esc_attr( $prefix ) ?>" data-sufix="<?php echo esc_attr( $sufix ) ?>"></div>
						<input type="hidden" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" value="<?php echo esc_attr( $range ) ?>"/>
						<div class="slider-value"></div>
					</div>
				</div>
				<?php
			}
			
		}
		else if( $field->cf_type == 5 ){
			$selected_term_ids = !empty( $selected_values ) ? $selected_values : array();
			ob_start();
			if( !empty( $selected_term_ids ) ){
				foreach( $selected_term_ids as $depth => $term_id ){
					$last_parent = 0;
					if( !empty( $term_id ) ){
						$term = get_term_by( 'id', $term_id, $field->cf_slug );
						if( !is_wp_error( $term ) ){
							$last_parent = $term->parent;
							self::nested_select( $field, $term_id, $term->parent, $depth );
						}
					}
					else{
						self::nested_select( $field,'', $last_parent, $depth );		
					}
				}
			}
			else{
				self::nested_select( $field );
			}
			$nested = ob_get_contents();
			ob_end_clean();
			?>
			<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
				<?php echo $nested; ?>
			</div>
			<?php
		}
		else if( $field->cf_type == 6 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label></label>
					<ul class="list-unstyled list-inline color-search">
						<li class="colored-default">
							<input type="radio" id="color-none" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" value="">
							<label class="animation" for="color-none" style="background:#fff;"></label>
						</li>						
						<?php
						foreach( $terms as $term ){
							?>
							<li class="<?php echo  $term->slug == 'ffffff' ? esc_attr( 'colored-default' ) : esc_attr( '' ) ?>">
								<input type="radio" id="color-<?php echo esc_attr( $term->term_id ) ?>" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, (array)$selected_values ) ? 'checked="checked"' : '' ?>>
								<label class="animation" for="color-<?php echo esc_attr( $term->term_id ) ?>" style="background:#<?php echo esc_attr( $term->slug ) ?>;"></label>
							</li>
							<?php
						}  
						?>						
					</ul>
				</div>
				<?php
			}
		}
		else if( $field->cf_type == 7 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label></label>
					<ul class="list-unstyled list-inline color-search">
						<?php
						foreach( $terms as $term ){
							?>
							<li class="<?php echo  $term->slug == 'ffffff' ? esc_attr( 'colored-default' ) : esc_attr( '' ) ?>">
								<input type="checkbox" id="color-<?php echo esc_attr( $term->term_id ) ?>" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, $selected_values ) ? 'checked="checked"' : '' ?>>
								<label class="animation" for="color-<?php echo esc_attr( $term->term_id ) ?>" style="background:#<?php echo esc_attr( $term->slug ) ?>;"></label>
							</li>
							<?php
						}  
						?>						
					</ul>
				</div>
				<?php
			}
		}
		else if( $field->cf_type == 8 ){
			$min_value = !empty( $selected_values['min'] ) ? $selected_values['min'] : '';
			$max_value = !empty( $selected_values['max'] ) ? $selected_values['max'] : '';
			?>
			<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
				<label><?php echo esc_html( $field->cf_label ) ?></label></label>
				<div class="date-range">
					<input type="text" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][min]" value="<?php echo esc_attr( $min_value ) ?>" placeholder="<?php esc_attr_e( 'min', 'adifier' ) ?>"/>
					<input type="text" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][max]" value="<?php echo esc_attr( $max_value ) ?>" placeholder="<?php esc_attr_e( 'max', 'adifier' ) ?>"/>
				</div>
			</div>
			<?php
		}
		if( $field->cf_type == 9 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			$terms = self::sort_values( $terms, $field->cf_orderby );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label>
					<ul class="list-unstyled <?php echo esc_attr( $field->cf_slug ) ?>-filter">
						<?php
						$counter = 0;
						foreach( $terms as $term ){
							?>
							<li>
								<div class="styled-checkbox">
									<input type="checkbox" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][]" id="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, $selected_values ) ? 'checked="checked"' : '' ?>>
									<label for="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>"><?php echo $term->name ?></label>
								</div>
							</li>
							<?php
							$counter++;
						}  
						?>
					</ul>
				</div>
				<?php
			}
		}
		else if( $field->cf_type == 10 ){
			$terms = adifier_get_taxonomy_hierarchy( $field->cf_slug );
			$terms = self::sort_values( $terms, $field->cf_orderby );
			if( !empty( $terms ) ){
				?>
				<div class="filter-group cf-field type_<?php echo esc_attr( $field->cf_type ); ?> cf_<?php echo esc_attr( $field->cf_slug ) ?>">
					<label><?php echo esc_html( $field->cf_label ) ?></label>
					<ul class="list-unstyled <?php echo esc_attr( $field->cf_slug ) ?>-filter">
						<li>
							<div class="styled-radio">
								<input type="radio" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" <?php echo empty( $selected_term ) ? esc_attr( 'checked="checked"' ) : '' ?> id="<?php echo esc_attr( $field->cf_slug ) ?>_0" value="">
								<label for="<?php echo esc_attr( $field->cf_slug ) ?>_0"><?php esc_html_e( 'All', 'adifier' ) ?></label>
							</div>
						</li>						
						<?php
						$counter = 1;
						foreach( $terms as $term ){
							?>
							<li>
								<div class="styled-radio">
									<input type="radio" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>]" id="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>" value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo in_array( $term->term_id, (array)$selected_values ) ? 'checked="checked"' : '' ?>>
									<label for="<?php echo esc_attr( $field->cf_slug ) ?>_<?php echo esc_attr( $counter ) ?>"><?php echo $term->name ?></label>
								</div>
							</li>
							<?php
							$counter++;
						}  
						?>
					</ul>
				</div>
				<?php
			}
		}		
	}

	/*
	* Dispaly HTML for nested select field type
	*/
	static public function nested_select( $field, $selected_term_id = '', $parent = 0, $depth = 0 ){
		$terms = get_terms(array(
			'taxonomy'		=> $field->cf_slug,
			'parent'		=> $parent,
			'hide_empty'	=> false
		));
		$terms = self::sort_values( $terms, $field->cf_orderby );
		if( !empty( $terms ) ){
			$label = explode( '|', $field->cf_label );
			?>
			<div class="<?php echo  $depth > 0 ? esc_attr( 'cf-nested depth_'.$depth) : esc_attr( '' ) ?>">
				<label><?php echo esc_html( $label[$depth] ) ?></label>
				<select class="nested-select select2-single" name="cf_fields[<?php echo esc_attr( $field->cf_slug ) ?>][<?php echo esc_attr( $depth ) ?>]" data-depth="<?php echo esc_attr( $depth ) ?>" data-maxdepth="<?php echo esc_attr( sizeof( $label ) - 1 ) ?>" data-fieldid="<?php echo esc_attr( $field->cf_id ) ?>">
					<option value=""><?php esc_html_e( '- Select -', 'adifier' ) ?></option>
					<?php
					foreach( $terms as $term ){
						?>
						<option value="<?php echo esc_attr( $term->term_id ) ?>" <?php echo selected( $term->term_id, $selected_term_id ) ? 'selected="selected"' : '' ?>><?php echo esc_html( $term->name ) ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<?php
		}
	}

	/*
	* Get CF layout via AJAX
	*/
	static public function get_cf_filter_ajax(){
		self::get_cf_filter( $_REQUEST['category_id'] );
		die();
	}

	/*
	* Get subfield of nested select via AJAX
	*/
	static public function get_subfield(){
		$field_id = !empty( $_REQUEST['field_id'] ) ? $_REQUEST['field_id'] : '';
		$depth = !empty( $_REQUEST['depth'] ) ? $_REQUEST['depth'] : '';
		$value = !empty( $_REQUEST['value'] ) ? $_REQUEST['value'] : '';
		$field = Adifier_Admin_Custom_Fields::get_custom_field_by_id( $field_id );
		if( !empty( $field ) ){
			self::nested_select( $field, '', $value, $depth );
		}
		die();
	}

	/*
	* Display taxononmy filter on search page
	*/
	static public function taxonomy_filter( $title, $name, $taxonomy, $selected_term ){
		$terms = adifier_get_taxonomy_hierarchy( $taxonomy, 0, true );
		if( !empty( $terms ) ){
			?>
			<label class="label-bottom-margin"><?php echo esc_html( $title ) ?></label>
			<ul class="list-unstyled taxonomy-filter <?php echo esc_attr( $name ) ?>-filter">
				<li>
					<div class="styled-radio">
						<input type="radio" name="<?php echo esc_attr( $name ) ?>" value="" id="<?php echo esc_attr( $name ) ?>" <?php echo empty( $selected_term ) ? esc_attr( 'checked="checked"' ) : '' ?>>
						<label for="<?php echo esc_attr( $name ) ?>"><?php esc_html_e( 'All', 'adifier' ) ?></label>
					</div>
				</li>
				<?php self::taxonomy_listing( $name, $terms, $taxonomy, $selected_term ); ?>
			</ul>
			<?php
		}
	}
	

	/*
	* Display filter taxonomies
	*/
	static public function taxonomy_listing( $name, $terms, $taxonomy, $selected_term ){
		$search_more_less = adifier_get_option( 'search_more_less' );
		$counter = 0;
		foreach( $terms as $term ){
			$counter++;
			?>
			<li class="<?php echo !empty( $search_more_less ) && $counter > $search_more_less ? esc_attr( 'term-hidden' ) : '' ?>">
				<div class="styled-radio">
					<input type="radio" name="<?php echo esc_attr( $name ) ?>" value="<?php echo esc_attr( $term->term_id ) ?>" id="<?php echo esc_attr( $name ) ?>-<?php echo esc_attr( $term->term_id ) ?>" <?php echo  $term->term_id == $selected_term ? esc_attr( 'checked="checked"' ) : '' ?>>
					<label for="<?php echo esc_attr( $name ) ?>-<?php echo esc_attr( $term->term_id ) ?>"><?php echo esc_html( $term->name ) ?></label>
					<?php
						if( !empty( $term->children ) ){
							?>
							<a href="javascript:void(0);"><i class="aficon-angle-down"></i></a>
							<?php
						}
					?>					
				</div>
				<?php
				if( !empty( $term->children ) ){
					?>
					<ul class="list-unstyled hidden">
						<?php self::taxonomy_listing( $name, $term->children, $taxonomy, $selected_term ); ?>
					</ul>
					<?php
				}
				?>
			</li>
			<?php
		}

		if( !empty( $search_more_less ) && $counter > $search_more_less ){
			?>
			<li class="toggle-more-less-wrap">
				<a href="javascript:void(0)" data-less="<?php esc_attr_e( 'Show Less', 'adifier' ) ?>" data-more="<?php esc_attr_e( 'Show More', 'adifier' ) ?>" class="toggle-more-less"><span><?php esc_html_e( 'Show More', 'adifier' ) ?></span> <i class="aficon-caret-down"></i></a>
			</li>
			<?php
		}
	} 

	/*
	* Get maximum value for price which will be used for slider
	*/
	static public function get_max_price(){
		global $wpdb;
		return $wpdb->get_var("SELECT MAX( IF( sale_price > 0, sale_price, price ) ) FROM {$wpdb->prefix}adifier_advert_data");
	}

	/*
	* Get maximum value for type4 field which is used as numeric range
	*/	
	static public function get_max_cf_slide_range( $taxonomy ){
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT MAX( CAST( slug AS UNSIGNED ) ) FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id WHERE taxonomy = %s", $taxonomy));
	}

	/*
	* Get maximum value for type4 field which is used as numeric range
	*/	
	static public function get_min_cf_slide_range( $taxonomy ){
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT MIN( CAST( slug AS UNSIGNED ) ) FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id WHERE taxonomy = %s", $taxonomy));
	}	
}
Adifier_Custom_Fields_Search::launch();
}
?>