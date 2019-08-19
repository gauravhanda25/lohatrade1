<?php

class Adifier_kc_categories_list extends FLBuilderModule {
    public function __construct(){
        parent::__construct(array(
            'name'          => esc_html__('Categories list', 'adifier'),
            'description'   => esc_html__('Display categories in list on your page.', 'adifier'),
            'category'		=> esc_html__('Adifier', 'adifier'),
            'dir'           => get_theme_file_path( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
            'url'           => get_theme_file_uri( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
        ));
    }
}
?>