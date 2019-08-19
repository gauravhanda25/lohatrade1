<header class="header-4 upper-header">
	<div class="container">
		<div class="flex-wrap">
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/navigation-btn.php' ) ); ?>
			</div>			
			<?php include( get_theme_file_path( 'includes/headers/logo.php' ) ); ?>
			<div class="show-on-414">
				<?php include( get_theme_file_path( 'includes/headers/submit-btn.php' ) ); ?>
			</div>
			<form action="<?php echo adifier_get_search_link() ?>" class="header-4-search header-search flex-wrap">
				<?php include( get_theme_file_path( 'includes/headers/search-parts/keyword.php' ) ); ?>
				<a href="javascript:void(0);" class="af-button submit-form">
					<i class="aficon-search"></i>
				</a>
			</form>
			<?php include( get_theme_file_path( 'includes/headers/special.php' ) ); ?>
		</div>
	</div>
</header>
<header class="header-2 header-4 lower-header sticky-header">
	<div class="container">
		<div class="flex-wrap flex-start-h">
			<?php adifier_logo_html_wrapper( 'dark_nav_logo', true ); ?>
			<div class="categories-dropdown header-alike">
				<a href="javascript:void(0);" class="header-cats-trigger flex-wrap flex-start-h">
					<i class="aficon-ion-android-menu"></i>
					<?php esc_html_e( 'All Categories', 'adifier' ) ?>
				</a>
				<ul class="dropdown-menu header-cats list-unstyled <?php echo ( ( is_front_page() && adifier_get_option( 'header_4_cats_opened' ) == 'yes' ) || !empty( $_GET['header_4_cats_opened'] ) ) ? esc_html( 'open' ) : '' ?> ">
					<?php 
					$header_4_cats = adifier_get_option('header_4_cats');
					$args = array(
						'taxonomy' => 'advert-category'
					);
					if( !empty( $header_4_cats ) ){
						$args['include'] = explode( ",", $header_4_cats );
						$args['orderby'] = 'include';
					}
					else{
						$args['parent'] = 0;
					}

					$categories = get_terms( $args );
					
					if( !empty($categories) ){
						foreach( $categories as $category ){
							?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
									<?php
									$advert_cat_icon = get_term_meta( $category->term_id, 'advert_cat_icon', true );
									if( !empty( $advert_cat_icon ) ){
										echo adifier_get_category_icon_img( $advert_cat_icon );
									}
									?>
									<?php echo esc_html( $category->name ); ?>
								</a>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
			<?php include( get_theme_file_path( 'includes/headers/navigation.php' ) ); ?>
			<div class="flex-right">
			</div>
		</div>
	</div>
</header>