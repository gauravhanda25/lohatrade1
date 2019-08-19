<?php
/*
Class for displaying custom values on single advert
*/
if( !class_exists('Adifier_Custom_Front_Advert') ){
class Adifier_Custom_Front_Advert{

	public $fields_array;
	public $last_category_id;
	public $cat_fields;

	/*
	* Grab all categories and organize them an then grab last child from which the fields wioll be fetched
	*/
	public function __construct(){
		$this->fields_array = array();
		$categories = get_the_terms( get_the_ID(), 'advert-category' );
		$categories = adifier_taxonomy_hierarchy( $categories );

		$this->_set_child_category( $categories );
	}

	/*
	* Populate category fields ( this is also used in compare )
	*/
	public function set_cat_fields( $fields = array() ){
		if( !empty( $fields ) ){
			$this->cat_fields = $fields;
		}
		else{
			$this->cat_fields = Adifier_Custom_Fields_Search::get_fields_by_category_id( $this->last_category_id );
		}
	}

	/*
	* Populate advert fields and values
	*/	
	public function set_fields_array(){
		$this->_custom_fields_and_values_array();
	}

	/*
	* Get latest child category
	*/
	private function _set_child_category( $categories ){
		if( !empty( $categories ) ){
			foreach( $categories as $category ){
				if( !empty( $category->children ) ){
					$this->_set_child_category( $category->children );
				}
				else{
					$this->last_category_id = $category->term_id;
				}
			}
		}
	}

	/*
	* Get advret custom fields and values in array
	*/
	private function _custom_fields_and_values_array(){
		if( !empty( $this->cat_fields ) ){
			foreach( $this->cat_fields as $field ){
				$selected_terms = wp_get_object_terms( get_the_ID(), $field->cf_slug );
				if( !empty( $selected_terms ) ){
					if( $field->cf_type == 5 ){
						$selected_terms = adifier_taxonomy_hierarchy( $selected_terms );
						$this->fields_array = array_merge( $this->fields_array, $this->_nested_values_array( $selected_terms, explode( '|', $field->cf_label ) ) );
					}
					else{
						$this->fields_array[$field->cf_slug] = $this->_generate_array_fields( $field, $selected_terms );
					}
				}
			}
		}
	}

	/*
	* Create array for field
	*/
	private function _generate_array_fields( $field, $selected_terms ){
		$data = array(
			'type' => $field->cf_type,
		);
		$list = array();
		if( in_array( $field->cf_type, array(1, 2, 8, 9, 10) ) ){
			$data['label'] = $field->cf_label;
			foreach( $selected_terms as $term ){
				$list[] = $term->name;
			}
			$data['value'] = join( ', ', $list );
		}
		else if( $field->cf_type == 3 ){
			$data['label'] = $field->cf_label;
			foreach( $selected_terms as $term ){
				$list[] = date_i18n( get_option( 'date_format' ), $term->slug );
			}
			$data['value'] = join( ', ', $list );
		}
		else if( $field->cf_type == 4 ){
			$prefix = Adifier_Custom_Fields_Search::get_field_type4_prefix( $field->cf_label );
			$sufix = Adifier_Custom_Fields_Search::get_field_type4_sufix( $field->cf_label );
			$data['label'] = Adifier_Custom_Fields_Search::get_field_type4_clean_label( $field->cf_label );
			foreach( $selected_terms as $term ){
				$list[] = $prefix.$term->name.$sufix;
			}
			$data['value'] = join( ', ', $list );			
		}
		else if( $field->cf_type == 6 ){
			$selected_value = array_shift( $selected_terms );
			$data['label'] = $field->cf_label;
			$data['value'] = '<span class="cf-color-value '.( in_array( $selected_value->slug, array( 'fff', 'ffffff' ) ) ? esc_attr( 'cf-color-border' ) : esc_attr( '' ) ).'" style="background: #'.esc_attr( $selected_value->slug ).'"></span>';
		}
		else if( $field->cf_type == 7 ){
			$data['label'] = $field->cf_label;
			foreach( $selected_terms as $term ){
				$list[] = '<span class="cf-color-value '.( in_array( $term->slug, array( 'fff', 'ffffff' ) ) ? esc_attr( 'cf-color-border' ) : esc_attr( '' ) ).'" style="background: #'.esc_attr( $term->slug ).'"></span>';
			}
			$data['value'] = join( ' ', $list );
		}
		return $data;
	}

	/*
	* create children for nested values
	*/
	private function _nested_values_array( $selected_terms, $labels, $depth = 0 ){
		$list = array();
		foreach( $selected_terms as $selected_term ){
			$list[] = $selected_term->name;
		}
		$data[sanitize_title($labels[$depth])] = array(
			'label' => $labels[$depth],
			'value' => join( ', ', $list ),
			'type'	=> 5
		);		
		if( !empty( $selected_term->children ) ){
			$data = array_merge( $data, $this->_nested_values_array( $selected_term->children, $labels, $depth+1 ) );
		}

		return $data;
	}

	/*
	Display values based on field type
	*/
	private function _generate_fields(){
		foreach( $this->fields_array as $field_array ){
			$this->_cf_html_field( $field_array );
		}
	}

	private function _cf_html_field( $field ){
		?>
		<li class="flex-wrap">
			<span class="cf-label">
				<?php echo esc_html( $field['label'] ) ?>
			</span>	
			<span class="cf-value">
				<?php  echo  $field['value'];  ?>
			</span>	
		</li>		
		<?php
	}


	/*
	* Display datea if they exists
	*/
	public function print_cf_data(){
		$this->set_cat_fields();
		$this->set_fields_array();
		if( !empty( $this->fields_array ) ){
			?>
			<div class="white-block">
				<div class="white-block-title">
					<h5><?php esc_html_e( 'More Details', 'adifier' ) ?></h5>
				</div>
				<div class="white-block-content">
					<ul class="list-unstyled cf-advert-list list-inline">
						<?php $this->_generate_fields(); ?>
					</ul>
				</div>
			</div>
			<?php			
		}
	}
}
}
?>