<?php

/*Fields array
array(
	array(
		'id' => ''
		'title' => ''
		'type' => ''
		if( type is select )
			'options' => array()
			'multiple' => false / true
		endif
		'default' => ''
	)
)
*/

if( !class_exists('Adifier_Widget') ){
class Adifier_Widget extends WP_Widget {
	
	public $properties;
	public $widget_instance;

	function __construct() {
		parent::__construct( 
			$this->properties['id'], 
			$this->properties['title'], 
			array('description' => $this->properties['description'] )
		);
	}

	function widget($args, $instance) {
		$defaults = array();
		if( !empty( $this->properties['fields'] ) ){
			foreach( $this->properties['fields'] as $field ){
				if( empty( $instance[$field['id']] ) ){
					if( !empty( $field['default'] ) ){
						$instance[$field['id']] = $field['default'];
					}
					else{
						if( $field['type'] == 'text' ){
							$instance[$field['id']] = '';
						}
						else if( $field['type'] == 'select' ){
							$instance[$field['id']] = array();	
						}
					}
				}

				if( !empty( $field['filter'] ) ){
					$instance[$field['id']] == apply_filters( $field['filter'], $instance[$field['id']], $instance, $this->id_base );
				}
			}
		}

		$this->widget_instance = $instance;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		if( !empty( $this->properties['fields'] ) ){
			foreach( $this->properties['fields'] as $field ){
				$instance[$field['id']] = $new_instance[$field['id']];
			}
		}
		return $instance;
	}

	function form( $instance ) {
		if( !empty( $this->properties['fields'] ) ){
			foreach( $this->properties['fields'] as $field ){
				if( $field['type'] == 'text' ){
					$value = isset( $instance[$field['id']] ) ? $instance[$field['id']] : '';
					?>
					<p><label for="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>"><?php echo  $field['title']; ?></label></p>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field['id'] ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					<?php
				}
				else if( $field['type'] == 'select' ){
					$values = isset( $instance[$field['id']] ) ? (array)$instance[$field['id']] : array();
					?>
					<p><label for="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>"><?php echo  $field['title']; ?></label></p>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field['id'] ) ); ?>" <?php echo !empty( $field['multiple'] ) && $field['multiple'] ? 'multiple="multiple"' : '' ?>>
						<?php
						if( !empty( $field['options'] ) ){
							foreach( $field['options'] as $value => $label ){
								?>
								<option value="<?php echo esc_attr( $value ) ?>" <?php echo in_array( $value, $values ) ? 'selected="selected"' : '' ?>><?php echo  $label ?></option>
								<?php
							}
						}
						?>
					</select>
					<?php
				}
				else if( $field['type'] == 'textarea' ){
					$value = isset( $instance[$field['id']] ) ? $instance[$field['id']] : '';
					?>
					<p><label for="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>"><?php echo  $field['title']; ?></label></p>
					<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field['id'] ) ); ?>"><?php echo esc_attr( $value ); ?></textarea>
					<?php
				}
				if( !empty( $field['description'] ) ){
					echo '<p class="field-description">'.$field['description'].'</p>';
				}
			}
			echo '<p></p>';
		}
	}

	function start_widget( $args, $title = '' ){
		extract( $args );
		echo  $before_widget;
		if ( $title ){
			echo  $before_title . $title . $after_title; 
		}
	}

	function end_widget( $args ){
		extract( $args );
		echo  $after_widget;
	}
}
}


if( !class_exists('Adifier_Widget_Adverts') ){
class Adifier_Widget_Adverts extends Adifier_Widget{
	function __construct(){
		$this->properties =	array(
			'id' 			=> 'adifier_posts_list',
			'title' 		=> esc_html__('Adifier Ads','adifier'),
			'description' 	=> esc_html__('Display ads','adifier'),
			'fields' 		=> array(
				array(
					'id' 		=> 'title',
					'title' 	=> esc_html__( 'Title', 'adifier' ),
					'type' 		=> 'text',
					'filter' 	=> 'widget_title'
				),
				array(
					'id' 		=> 'number',
					'title' 	=> esc_html__( 'Number', 'adifier' ),
					'type' 		=> 'text'
				),
				array(
					'id' 			=> 'advert_ids',
					'title' 		=> esc_html__( 'Ad Ids', 'adifier' ),
					'description'	=> esc_html__( 'Input comma separated list of ad IDs you wish to show', 'adifier' ),
					'type' 			=> 'text'
				),
				array(
					'id' 			=> 'cat_ids',
					'title' 		=> esc_html__( 'Category Ids', 'adifier' ),
					'description'	=> esc_html__( 'Input comma separated list of ad category IDs from whcih to show the posts', 'adifier' ),
					'type' 			=> 'text'
				),
				array(
					'id' 			=> 'loc_ids',
					'title' 		=> esc_html__( 'Location Ids', 'adifier' ),
					'description'	=> esc_html__( 'Input comma separated list of ad location IDs from whcih to show the posts', 'adifier' ),
					'type' 			=> 'text'
				),
				array(
					'id' 			=> 'orderby',
					'title' 		=> esc_html__( 'Order By', 'adifier' ),
					'type' 			=> 'select',
					'options'		=> array(
						'views'			=> esc_html__( 'Popularity', 'adifier' ),
						'date'			=> esc_html__( 'Date', 'adifier' ),
						'price'			=> esc_html__( 'Price', 'adifier' ),
						'expire'		=> esc_html__( 'Expire', 'adifier' ),
					)	
				),
				array(
					'id' 			=> 'order',
					'title' 		=> esc_html__( 'Order', 'adifier' ),
					'type' 			=> 'select',
					'options'		=> array(
						'ASC'			=> esc_html__( 'Ascending', 'adifier' ),
						'DESC'			=> esc_html__( 'Descending', 'adifier' ),
					)	
				),
			)
		);
		parent::__construct();
	}

	function widget($args, $instance) {
		parent::widget( $args, $instance );
		extract( $this->widget_instance );

		$this->start_widget( $args, $title );
		if( !empty( $advert_ids ) ){
			$query = array(
				'post__in' 	=> explode(',', $advert_ids),
				'orderby'	=> 'post__in'
			);
		}
		else{
			$query = array(
				'orderby'				=> $orderby,
				'order'					=> $order,
				'posts_per_page'		=> $number,
				'tax_query'				=> array()
			);

			if( !empty( $cat_ids ) ){
				$query['tax_query'][] = array(
					'taxonomy'	=> 'advert-category',
					'term_ids'	=> explode( ',', $cat_ids )
				);
			}

			if( !empty( $loc_ids ) ){
				$query['tax_query'][] = array(
					'taxonomy'	=> 'advert-location',
					'term_ids'	=> explode( ',', $loc_ids )
				);
			}
		}
		global $adifier_widget_query;
		$adifier_widget_query = true;
		$adverts = new Adifier_Advert_Query( $query );
		$adifier_widget_query = false;
		if( $adverts->have_posts() ){
			?>			
			<ul class="list-unstyled">
				<?php
				while( $adverts->have_posts() ){
					$adverts->the_post();
					?>
					<li class="flex-wrap flex-start-h">
						<?php if( has_post_thumbnail() ): ?>
							<div class="flex-left">
								<a href="<?php the_permalink() ?>">
									<?php  adifier_get_advert_image( 'adifier-widget' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="flex-right">
							<h5>
								<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="text-overflow">
									<?php the_title() ?>
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
		}
		wp_reset_postdata();

		$this->end_widget( $args );
	}
}
}

if( !class_exists('Adifier_Widget_Categories') ){
class Adifier_Widget_Categories extends Adifier_Widget{
	function __construct(){
		$this->properties =	array(
			'id' 			=> 'adifier_advert_categories',
			'title' 		=> esc_html__('Adifier Ad Categories','adifier'),
			'description' 	=> esc_html__('Display ad categories','adifier'),
			'fields' 		=> array(
				array(
					'id' 		=> 'title',
					'title' 	=> esc_html__( 'Title', 'adifier' ),
					'type' 		=> 'text',
					'filter' 	=> 'widget_title'
				),
				array(
					'id' 		=> 'number',
					'title' 	=> esc_html__( 'Number', 'adifier' ),
					'type' 		=> 'text'
				),
				array(
					'id' 		=> 'icon',
					'title' 	=> esc_html__( 'Show Icon', 'adifier' ),
					'type' 		=> 'select',
					'options'	=> array(
						'yes' => esc_html__( 'Yes', 'adifier' ),
						'no'  => esc_html__( 'No', 'adifier' )
					)
				),
				array(
					'id' 			=> 'cat_ids',
					'title' 		=> esc_html__( 'Categories', 'adifier' ),
					'description'	=> esc_html__( 'Input comma separated list of ad category IDs you wish to show', 'adifier' ),
					'type' 			=> 'text'
				),
			)
		);
		parent::__construct();
	}

	function widget($args, $instance) {
		parent::widget( $args, $instance );
		extract( $this->widget_instance );

		$this->start_widget( $args, $title );
		$query_args = array(
			'taxonomy'	=> 'advert-category',
			'parent'	=> 0
		);
		if( !empty( $number ) ){
			$query_args['number'] = $number;
		}
		else if( !empty( $cat_ids ) ){
			unset( $query_args['parent'] );
			$query_args['include'] = explode(',', $cat_ids);
		}

		$terms = get_terms( $query_args );
		if( !empty( $terms ) ){
			$term_ids = wp_list_pluck( $terms, 'term_id' );
			$term_counts = adifier_get_advert_taxonomy_counts( $term_ids );			
			?>			
			<ul class="list-unstyled">
				<?php
				foreach( $terms as $term ){
					$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
					?>
					<li class="flex-wrap">
						<div class="flex-left">
							<a href="<?php echo esc_url( get_term_link( $term ) ) ?>" class="flex-left flex-wrap flex-start-h">
								<?php
									if( $icon == 'yes' ){
										$advert_cat_icon = get_term_meta( $term->term_id, 'advert_cat_icon', true );
										if( !empty( $advert_cat_icon ) ){
											echo adifier_get_category_icon_img( $advert_cat_icon );
										}
									}
								?>								
								<?php echo  $term->name; ?>
							</a>
						</div>
						<span>
							<?php echo  $term_count.' '._n( 'ad', 'ads', $term_count, 'adifier' ); ?>
						</span>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		wp_reset_postdata();

		$this->end_widget( $args );
	}
}
}

if( !class_exists('Adifier_Widget_Locations') ){
class Adifier_Widget_Locations extends Adifier_Widget{
	function __construct(){
		$this->properties =	array(
			'id' 			=> 'adifier_advert_locations',
			'title' 		=> esc_html__('Adifier Ad Locations','adifier'),
			'description' 	=> esc_html__('Display ad locations','adifier'),
			'fields' 		=> array(
				array(
					'id' 		=> 'title',
					'title' 	=> esc_html__( 'Title', 'adifier' ),
					'type' 		=> 'text',
					'filter' 	=> 'widget_title'
				),
				array(
					'id' 		=> 'number',
					'title' 	=> esc_html__( 'Number', 'adifier' ),
					'type' 		=> 'text'
				),
				array(
					'id' 			=> 'loc_ids',
					'title' 		=> esc_html__( 'Locations', 'adifier' ),
					'description'	=> esc_html__( 'Input comma separated list of ad location IDs you wish to show', 'adifier' ),
					'type' 			=> 'text'
				),
			)
		);
		parent::__construct();
	}

	function widget($args, $instance) {
		parent::widget( $args, $instance );
		extract( $this->widget_instance );

		$this->start_widget( $args, $title );
		$query_args = array(
			'taxonomy'	=> 'advert-location',
			'parent'	=> 0
		);
		if( !empty( $number ) ){
			$query_args['number'] = $number;
		}
		else if( !empty( $loc_ids ) ){
			unset( $query_args['parent'] );
			$query_args['include'] = explode(',', $loc_ids);
		}

		$terms = get_terms( $query_args );
		if( !empty( $terms ) ){
			$term_ids = wp_list_pluck( $terms, 'term_id' );
			$term_counts = adifier_get_advert_taxonomy_counts( $term_ids );			
			?>			
			<ul class="list-unstyled">
				<?php
				foreach( $terms as $term ){
					$term_count = !empty( $term_counts[$term->term_id] ) ? $term_counts[$term->term_id] : 0;
					?>
					<li class="flex-wrap">
						<a href="<?php echo esc_url( get_term_link( $term ) ) ?>">
							<i class="aficon-crosshairs"></i> <?php echo  $term->name; ?>
						</a>
						<span>
							<?php echo  $term_count.' '._n( 'ad', 'ads', $term_count, 'adifier' ); ?>
						</span>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		wp_reset_postdata();

		$this->end_widget( $args );
	}
}
}

if( !class_exists('Adifier_Widget_Search') ){
class Adifier_Widget_Search extends Adifier_Widget{
	function __construct(){
		$this->properties =	array(
			'id' 			=> 'adifier_search',
			'title' 		=> esc_html__('Adifier Search Ads','adifier'),
			'description' 	=> esc_html__('Display search form for ads','adifier'),
			'fields' 		=> array(
				array(
					'id' 		=> 'title',
					'title' 	=> esc_html__( 'Title', 'adifier' ),
					'type' 		=> 'text',
					'filter' 	=> 'widget_title'
				),
			)
		);
		parent::__construct();
	}

	function widget($args, $instance) {
		parent::widget( $args, $instance );
		extract( $this->widget_instance );

		$this->start_widget( $args, $title );

		?>
		<div class="kc-search widget-alike-search">
			<?php include( get_theme_file_path( 'includes/headers/search-form.php' ) ); ?>
		</div>		
		<?php

		$this->end_widget( $args );
	}
}
}


if( !function_exists('adifier_widgets_register') ){
function adifier_widgets_register() {
	if ( !is_blog_installed() ){
		return;
	}	
	/* register new ones */
	register_widget('Adifier_Widget_Adverts');
	register_widget('Adifier_Widget_Search');
	register_widget('Adifier_Widget_Categories');
	register_widget('Adifier_Widget_Locations');
}

add_action('widgets_init', 'adifier_widgets_register', 20);
}
?>