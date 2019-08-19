<?php

class Adifier_kc_af_locations_list extends FLBuilderModule {
    public function __construct(){
        parent::__construct(array(
            'name'          => esc_html__('Locations List', 'adifier'),
            'description'   => esc_html__('Display location element in plain list', 'adifier'),
            'category'		=> esc_html__('Adifier', 'adifier'),
            'dir'           => get_theme_file_path( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
            'url'           => get_theme_file_uri( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
        ));
    }
}
?>