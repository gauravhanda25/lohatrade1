<?php
$atts = adifier_beaverbuilder_get_atts( $settings );
include( get_theme_file_path( 'includes/shortcodes/kingcomposer/'.$settings->type.'.php' ) );
?>