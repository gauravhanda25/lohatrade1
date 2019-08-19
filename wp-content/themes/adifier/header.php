<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if( !is_singular( 'ad-order' ) ): ?>
	<?php 
	if( function_exists('adifier_create_post_types') ){
		adifier_confirm_email();
	}
	?>


	<?php 
	if( !function_exists('adifier_create_post_types') ){
		include( get_theme_file_path( 'includes/headers/header-default.php' ) );
	}
	else if( !adifier_is_own_account() ){
		$header_style = adifier_get_option( 'header_style' );
		if( empty( $header_style ) ){
			$header_style = 'header-1';
		}
		if( is_page() && get_page_template_slug() == 'page-tpl_search_map.php' ){
			$header_style = 'header-1';
		}
		if( !empty($_GET['header_style']) ){
			$header_style = $_GET['header_style'];
		}
		include( get_theme_file_path( 'includes/headers/'.$header_style.'.php' ) );
	}
	?>
<?php endif; ?>