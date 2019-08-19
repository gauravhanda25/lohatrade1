<?php

/*
* Check if ad is already in compare
*/
if( !function_exists('adifier_is_in_compare') ){
function adifier_is_in_compare( $id ){
	if( !empty( $_COOKIE['adifier_compare'] ) && $_COOKIE['adifier_compare'] != '-1' ){
		$list = json_decode( stripslashes( $_COOKIE['adifier_compare'] ), true );
		if( !empty( $list ) && in_array( $id, $list ) ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}
}


/*
* handle compare
*/
if( !function_exists('adifier_compare') ){
function adifier_compare(){
	$compare = isset( $_POST['compare'] ) ? $_POST['compare'] : '';
	$id = isset( $_POST['id'] ) ? $_POST['id'] : '';
	$list = array();
	if( !empty( $_COOKIE['adifier_compare'] ) && $_COOKIE['adifier_compare'] != '-1' ){
		$list = json_decode( stripslashes( $_COOKIE['adifier_compare'] ), true );
	}

	if( $compare == 'remove' ){
		$key = array_search( $id, $list );
		if ( $key !== false ) {
		    unset( $list[$key] );
		}
	}
	else if( $compare == 'add' && !in_array( $id, $list ) ){
		$list[] = $id;
	}

	setcookie( 'adifier_compare', json_encode( $list ), current_time( 'timestamp' ) + 86400, '/' );


	if( !empty( $list ) ){
		$ads = new Adifier_Advert_Query(array(
			'post__in'			=> $list,
			'posts_per_page'	=> adifier_get_option( 'compare_max_ads' )
		));

		$compare_fields = array();
		$compare_fields_organized = array();
		$adverts_data = array();

		if( $ads->have_posts() ){			
			while( $ads->have_posts() ){
				$ads->the_post();

				ob_start();
				?>
				<a href="<?php the_permalink() ?>" target="_blank">
					<?php adifier_get_advert_image( 'adifier-grid' ) ?>
				</a>
				<h5>
					<a href="<?php the_permalink() ?>" class="text-overflow" title="<?php echo esc_attr( get_the_title() ) ?>" target="_blank">
						<?php the_title(); ?>
					</a>
				</h5>
				<?php
				$content = ob_get_contents();
				ob_end_clean();

				$advert_fields = new Adifier_Custom_Front_Advert();
				if( empty( $compare_fields[$advert_fields->last_category_id] ) ){
					$advert_fields->set_cat_fields();
					$compare_fields[$advert_fields->last_category_id] = $advert_fields->cat_fields;	
				}
				else{
					$advert_fields->set_cat_fields( $compare_fields[$advert_fields->last_category_id] );
				}
				$advert_fields->set_fields_array();
				$adverts_data[get_the_ID()] = array(
					'fields' 	=> $advert_fields->fields_array,
					'content'	=> $content,
					'price'		=> adifier_get_advert_price()
				);
			}
			/* let's organize category fields first */
			foreach( $compare_fields as $cat_id => $fields ){
				foreach( $fields as $field ){
					if( $field->cf_type != 5 ){
						$compare_fields_organized[$field->cf_slug] = $field->cf_label;
					}
					else{
						$labels = explode( '|', $field->cf_label );
						foreach( $labels as $label ){
							$compare_fields_organized[sanitize_title($label)] = $label;
						}
					}
				}
			}

			?>
			<div class="responsive-table">
				<table>
					<tr>
						<th>&nbsp;</th>
						<?php
						foreach( $adverts_data as $id => $data ){
							?>
							<td class="cad_<?php echo esc_attr( $id ); ?>">
								<?php echo $data['content'] ?>
								<a href="javascript:void(0);" class="compare-remove" data-id="<?php echo esc_attr( $id ) ?>">
									<i class="aficon-times"></i>
								</a>
							</td>
							<?php
						}
						?>
					</tr>
					<?php foreach( $compare_fields_organized as $slug => $label ): ?>
						<tr>
							<th><?php echo esc_html( $label ) ?></th>
							<?php  foreach( $adverts_data as $id => $data ){ ?>
								<td class="cad_<?php echo esc_attr( $id ); ?>">
									<?php
									if( !empty( $data['fields'][$slug] ) ){
										echo $data['fields'][$slug]['value'];
									}
									else{
										echo '/';
									}
									?>
								</td>
							<?php } ?>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th><?php esc_html_e( 'Price', 'adifier' ) ?></th>
						<?php
						foreach( $adverts_data as $id => $data ){
							echo '<td class="cad_'.esc_attr( $id ).'"><div class="bottom-advert-meta">'.$data['price'].'</div></td>';
						}
						?>
					</tr>
				</table>
			</div>
			<?php
		}
		else{
			?>
			<h5 class="text-center"><?php esc_html_e( 'There are no ads to compare', 'adifier' ) ?></h5>
			<?php			
		}		
		wp_reset_postdata();
	}
	else{
		?>
		<h5 class="text-center"><?php esc_html_e( 'There are no ads to compare', 'adifier' ) ?></h5>
		<?php
	}

	die();
}
add_action( 'wp_ajax_adifier_compare', 'adifier_compare' );
add_action( 'wp_ajax_nopriv_adifier_compare', 'adifier_compare' );
}

?>