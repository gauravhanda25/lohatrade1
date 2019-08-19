<?php

class Adifier_kc_slider_bg_text extends FLBuilderModule {
    public function __construct(){
        parent::__construct(array(
            'name'          => esc_html__('Image/Text Slider', 'adifier'),
            'description'   => esc_html__('Display image&text slider on your page.', 'adifier'),
            'category'		=> esc_html__('Adifier', 'adifier'),
            'dir'           => get_theme_file_path( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
            'url'           => get_theme_file_uri( 'includes/shortcodes/beaverbuilder/'.str_replace( 'Adifier_', '', __CLASS__ ) ),
        ));
    }
}
?>