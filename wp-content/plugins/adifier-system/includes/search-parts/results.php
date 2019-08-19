<div class="ajax-search">
	<?php
	if( $adverts->have_posts() ){
		?>
		<div class="white-block">
			<div class="white-block-content">
				<div class="flex-wrap search-order">
					<h6>
						<?php esc_html_e( 'Showing ', 'adifier' ); ?>
						<strong><?php echo esc_html( ( $page - 1 ) * $per_page + 1 ) ?></strong>
						-
						<strong><?php echo $page * $per_page > $adverts->found_posts ? esc_html( $adverts->found_posts ) : esc_html( $page * $per_page ) ?></strong> 
						<?php esc_html_e( 'of', 'adifier' ); ?>
						<strong><?php echo esc_html( $adverts->found_posts ) ?></strong>
						<?php $adverts->found_posts == 1 ? esc_html_e( 'ad found', 'adifier' ) : esc_html_e( 'ads found', 'adifier' ); ?>
					</h6>
					<div class="flex-right flex-wrap">
						<div class="styled-select styled-select-no-label">
							<select name="af_orderby" class="orderby">
								<option value="" <?php selected( '', $orderby ) ?>><?php esc_html_e( 'Sort By Date', 'adifier' ) ?></option>
								<option value="expire-ASC" <?php selected( 'expire-ASC', $orderby ) ?>><?php esc_html_e( 'Sort By Expire', 'adifier' ) ?></option>
								<option value="views-DESC" <?php selected( 'views-DESC', $orderby ) ?>><?php esc_html_e( 'Sort By Popularity', 'adifier' ) ?></option>
								<option value="price-ASC" <?php selected( 'price-ASC', $orderby ) ?>><?php esc_html_e( 'Sort By Price - Ascending', 'adifier' ) ?></option>
								<option value="price-DESC" <?php selected( 'price-DESC', $orderby ) ?>><?php esc_html_e( 'Sort By Price - Descending', 'adifier' ) ?></option>
							</select>
						</div>
						<div class="layout-view">
							<a href="javascript:void(0);" class="<?php echo  $layout == 'grid' ? esc_attr( 'active' ) : esc_attr( '' ) ?>" data-style="grid"><i class="aficon-th"></i></a>
							<a href="javascript:void(0);" class="<?php echo  $layout == 'list' ? esc_attr( 'active' ) : esc_attr( '' ) ?>" data-style="list"><i class="aficon-th-list"></i></a>
							<a href="javascript:void(0);" class="<?php echo  $layout == 'card' ? esc_attr( 'active' ) : esc_attr( '' )  ?>" data-style="card"><i class="aficon-th-large"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="af-items-<?php echo  $layout == 'list' ? esc_attr( 1 ) : ( $layout == 'card' ? esc_attr( 2 ) : esc_attr( $result_listing ) )  ?> <?php echo esc_attr('af-listing-'.$layout) ?>" >
			<?php
			while( $adverts->have_posts() ){
				$adverts->the_post();
				echo '<div class="af-item-wrap">';
					include( get_theme_file_path( 'includes/advert-boxes/'.$layout.'.php' ) );
				echo '</div>';
			}
			?>
		</div>

		
		<?php
		if( !empty( $pagination ) ){
			?>
			<div class="pagination">
				<?php echo $pagination ?>
			</div>
			<?php
		}
		?>

		<?php
	}
	else{
		?>
		<div class="white-block no-advert-found">
			<div class="white-block-content text-center">
				<i class="aficon-question-circle"></i>
				<h6><?php esc_html_e( 'No ads found matched your criteria', 'adifier' ) ?></h6>
			</div>
		</div>
		<?php
	}

	?>
</div>