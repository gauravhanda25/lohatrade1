<?php

class Adifier_kc_search extends FLBuilderModule {
    public function __construct(){
        parent::__construct(array(
            'name'          => esc_html__('Search Form', 'adifier'),
            'description'   => esc_html__('Display search form on the page.', 'adifier'),
            'category'		=> esc_html__('Adifier', 'adifier'),
            'dir'           => get_theme_file_path( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
            'url'           => get_theme_file_uri( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
        ));
    }
}
?>