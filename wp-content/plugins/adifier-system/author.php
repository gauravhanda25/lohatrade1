<?php
if( class_exists( 'ReduxFrameworkPlugin' ) ){
	include( get_theme_file_path( 'includes/author/author.php' ) );
}
else{
	include( get_theme_file_path( 'index.php' ) );
}
?>