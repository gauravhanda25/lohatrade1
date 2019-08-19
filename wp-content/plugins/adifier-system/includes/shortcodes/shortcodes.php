<?php

/*
* List of all available shortcodes and their options which will then be used in Beaver and KingCompsoer registration
*/
if( !function_exists('adifier_get_shortcodes') ){
function adifier_get_shortcodes(){
	return array(
        array(
            'kc_search' => array(
                'name' 			=> esc_html__( 'Search Form', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'style',
			            'label' 		=> esc_html__( 'Style', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'shadow' 		=> esc_html__( 'With Shadow', 'adifier' ),
						    'normal' 		=> esc_html__( 'Normal', 'adifier' ),
						    'vertical' 		=> esc_html__( 'Vertical', 'adifier' ),
						    'labeled' 		=> esc_html__( 'Labeled', 'adifier' ),
						),				            
			            "description" 	=> esc_html__("Select type of search form","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_service' => array(
                'name' 			=> esc_html__( 'Service', 'adifier' ),
                'description' 	=> esc_html__('Display service', 'adifier'),
                'category' 		=> 'Content',
                'params' 		=> array(
                    array(
                        'name' 			=> 'icon',
                        'label' 		=> esc_html__( 'Select Icon', 'adifier' ),
                        'type' 			=> 'icon_picker'
                    ),
                    array(
                        'name' 			=> 'icon_bg_color',
                        'label' 		=> esc_html__( 'Icon BG Color', 'adifier' ),
                        'type' 			=> 'color_picker'
                    ),
                    array(
                        'name' 			=> 'icon_font_color',
                        'label' 		=> esc_html__( 'Icon Font Color', 'adifier' ),
                        'type' 			=> 'color_picker'
                    ),
                    array(
                        'name' 			=> 'title',
                        'label' 		=> esc_html__( 'Title', 'adifier' ),
                        'type' 			=> 'text'
                    ),
                    array(
                        'name' 			=> 'subtitle',
                        'label' 		=> esc_html__( 'Subtitle', 'adifier' ),
                        'type' 			=> 'text'
                    ),
                    array(
                        'name' 			=> 'style',
                        'label' 		=> esc_html__( 'Service Style', 'adifier' ),
                        'type' 			=> 'select',
                        'options'		=> array(
                        	'vertical'			=> esc_html__( 'Vertical', 'adifier' ),
                        	'horizontal'		=> esc_html__( 'Horizontal', 'adifier' ),
                        	'horizontal right'	=> esc_html__( 'Horizontal With Icon Right', 'adifier' ),
                        )
                    ),
                )
            ),
        ),
        array(
            'kc_adverts' => array(
                'name' 			=> esc_html__( 'Ads', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'ads_source',
			            'label' 		=> esc_html__( 'Ads Source', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'by_choice' 		=> esc_html__( 'By Choice', 'adifier' ),
						    'by_category' 		=> esc_html__( 'By Category', 'adifier' ),
						    'by_location' 		=> esc_html__( 'By Location', 'adifier' )
						),				            
			            "description" 	=> esc_html__("Select from where you want to pull ads","adifier")
			        ),
			        array(
			            'name' 			=> 'topads',
			            'type' 			=> 'select',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> array( 'by_category', 'by_location' )
				    	),
			            'options'		=> array(
			            	'no' => esc_html__( 'No', 'adifier' ),
			            	'yes' => esc_html__( 'Yes', 'adifier' )
			            ),
			            'label' 		=> esc_html__( 'Top Ads Only', 'adifier' ),
			            "description" 	=> esc_html__("Display only topads","adifier")
			        ),
			        array(
			            'name' 			=> 'post_ids',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> 'by_choice'
				    	),
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'post_type'		=> 'advert'
			            ),
			            'label' 		=> esc_html__( 'Ads', 'adifier' ),
			            "description" 	=> esc_html__("Select which ads to show","adifier")
			        ),
			        array(
			            'name' 			=> 'post_number',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> array( 'by_category', 'by_location' )
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Ads Number', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many ads to grab', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'type',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> array( 'by_category', 'by_location' )
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	''	=> esc_html__( 'All', 'adifier' ),
			            	'1'	=> esc_html__( 'Sell', 'adifier' ),
			            	'2'	=> esc_html__( 'Auction', 'adifier' ),
			            	'3'	=> esc_html__( 'Buy', 'adifier' ),
			            	'4'	=> esc_html__( 'Exchange', 'adifier' ),
			            	'5'	=> esc_html__( 'Gift', 'adifier' ),
			            	'6'	=> esc_html__( 'Rent', 'adifier' ),
			            	'7'	=> esc_html__( 'Job - Offer', 'adifier' ),
			            	'8'	=> esc_html__( 'Job - Wanted', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Type', 'adifier' ),
			            "description" 	=> esc_html__( 'Select type of ads', 'adifier' )
			        ),				        
			        array(
			            'name' 			=> 'category_ids',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> 'by_category'
				    	),
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-category'
			            ),
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            "description" 	=> esc_html__( 'Select categories from which to show ads', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'location_ids',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> 'by_location'
				    	),
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-location'
			            ),
			            'label' 		=> esc_html__( 'Locations', 'adifier' ),
			            "description" 	=> esc_html__( 'Select location from which to show ads', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'orderby',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> array( 'by_category', 'by_location' )
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'expire'		=> esc_html__( 'By Expire', 'adifier' ),
			            	'views'			=> esc_html__( 'By Popularity', 'adifier' ),
			            	'price'			=> esc_html__( 'By Price', 'adifier' ),
			            	'date'			=> esc_html__( 'By Date', 'adifier' ),
			            	'title'			=> esc_html__( 'By Title', 'adifier' ),
			            	'rand'			=> esc_html__( 'Random', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Order By', 'adifier' ),
			            "description" 	=> esc_html__("Select by which field to order ads","adifier")
			        ),
			        array(
			            'name' 			=> 'order',
				    	'relation'		=> array(
				    		'parent'		=> 'ads_source',
				    		'show_when'		=> array( 'by_category', 'by_location' )
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'ASC'			=> esc_html__( 'Ascending', 'adifier' ),
			            	'DESC'			=> esc_html__( 'Descending', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Order', 'adifier' ),
			            "description" 	=> esc_html__("Select how to order ads","adifier")
			        ),
			        array(
			            'name' 			=> 'style',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'grid'			=> esc_html__( 'Grid', 'adifier' ),
			            	'list'			=> esc_html__( 'List', 'adifier' ),
			            	'card'			=> esc_html__( 'Card', 'adifier' ),
			            	'big_slider'	=> esc_html__( 'Big Slider', 'adifier' )
			            ),
			            'label' 		=> esc_html__( 'Style', 'adifier' ),
			            "description" 	=> esc_html__("Select style of the ads","adifier")
			        ),
			        array(
				    	'relation'		=> array(
				    		'parent'		=> 'style',
				    		'show_when'		=> array( 'grid', 'list', 'card' )
				    	),
			            'name' 			=> 'slider',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show In Slider', 'adifier' ),
			            "description" 	=> esc_html__( 'To show ads in slider or not', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'visible_items',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Visible Items', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display on each slide', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'autoplay_speed',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Autoplay Speed (in ms)', 'adifier' ),
			            "description" 	=> esc_html__( 'Leave emptyto disable', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'double_row',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Double Row', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to show ads in two rows on each slide set this option to Yes', 'adifier' )
			        ),
			        array(
				    	'relation'		=> array(
				    		'parent'		=> 'style',
				    		'show_when'		=> array( 'grid', 'list', 'card' )
				    	),
			            'name' 			=> 'items_in_row',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'1'	=> '1',
			            	'2'	=> '2',
			            	'3'	=> '3',
			            	'4'	=> '4',
			            	'5'	=> '5',
			            	'6'	=> '6',
			            ),
			            'label' 		=> esc_html__( 'Items In Row', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display in each row', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_categories' => array(
                'name' 			=> esc_html__( 'Categories', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'category_ids',
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-category'
			            ),
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which categories to display or leave empty to show all of them', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'style',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'side_icon_bg'	=> esc_html__( 'Side Icon With Background', 'adifier' ),
			            	'top_icon_bg'	=> esc_html__( 'Top icon With Background', 'adifier' ),
			            	'side_icon'		=> esc_html__( 'Side Icon', 'adifier' ),
			            	'top_icon'		=> esc_html__( 'Top Icon', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Style', 'adifier' ),
			            "description" 	=> esc_html__( 'Select box style for categories', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'show_count',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show Count', 'adifier' ),
			            "description" 	=> esc_html__( 'Show or hide number of ads per category', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'slider',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show In Slider', 'adifier' ),
			            "description" 	=> esc_html__( 'To show items in slider or not', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'visible_items',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Visible Items', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display on each slide', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'autoplay_speed',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Autoplay Speed (in ms)', 'adifier' ),
			            "description" 	=> esc_html__( 'Leave emptyto disable', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'double_row',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Double Row', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to show items in two rows on each slide set this option to Yes', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'items_in_row',
			            'type' 			=> 'select',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'no'
				    	),
			            'options'		=> array(
			            	'1' => '1',
			            	'2' => '2',
			            	'3' => '3',
			            	'4' => '4',
			            	'5' => '5',
			            	'6' => '6',
			            ),
			            'label' 		=> esc_html__( 'Items In Row', 'adifier' ),
			            "description" 	=> esc_html__( 'How many categories per row', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'icon_max_width',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Max Image Width', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to make icon smaller you can place here max width. For example 40px', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'name_font_size',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Name Font Size', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to change font size of category name input it here. For example 13px', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'count_font_size',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Count Font Size', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to change font size of category count input it here. For example 13px', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'icon_margin',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Icon Bottom Margin', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to adjust bottom marginof the icon input it here. For example 25px', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'box_padding',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Box Padding', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to adjust box padding input it here. For example 25px', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'show_empty',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show empty?', 'adifier' ),
			            "description" 	=> esc_html__( 'Select yes if you want to dispaly empty categories', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_categories_list' => array(
                'name' 			=> esc_html__( 'Categories List', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'category_ids',
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-category'
			            ),
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which categories to display or leave empty to show all of them', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'show_count',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show Count', 'adifier' ),
			            "description" 	=> esc_html__( 'Show or hide number of ads per category', 'adifier' )
			        ),			        
			        array(
			            'name' 			=> 'show_empty',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show empty?', 'adifier' ),
			            "description" 	=> esc_html__( 'Select yes if you want to dispaly empty categories', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_categories_tree' => array(
                'name' 			=> esc_html__( 'Categories Tree', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'category_ids',
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-category'
			            ),
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which categories to display or leave empty to show all of them', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'subs',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Subcategories Number', 'adifier' ),
			            "description" 	=> esc_html__( 'Input how many subcategories to display', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'slider',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show In Slider', 'adifier' ),
			            "description" 	=> esc_html__( 'To show items in slider or not', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'visible_items',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Visible Items', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display on each slide', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'autoplay_speed',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Autoplay Speed (in ms)', 'adifier' ),
			            "description" 	=> esc_html__( 'Leave emptyto disable', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'double_row',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Double Row', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to show items in two rows on each slide set this option to Yes', 'adifier' )
			        ),				        
			        array(
			            'name' 			=> 'items_in_row',
			            'type' 			=> 'select',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'no'
				    	),
			            'options'		=> array(
			            	'1' => '1',
			            	'2' => '2',
			            	'3' => '3',
			            	'4' => '4',
			            	'5' => '5',
			            	'6' => '6',
			            ),
			            'label' 		=> esc_html__( 'Items In Row', 'adifier' ),
			            "description" 	=> esc_html__( 'How many categories per row', 'adifier' )
			        ),			        
			        array(
			            'name' 			=> 'show_empty',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show empty?', 'adifier' ),
			            "description" 	=> esc_html__( 'Select yes if you want to dispaly empty categories', 'adifier' )
			        ),			        
                )
            ),
        ),
        array(
            'kc_round_icon' => array(
                'name' 			=> esc_html__( 'Round Icon', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'icon',
			            'type' 			=> 'icon_picker',
			            'label' 		=> esc_html__( 'Icon', 'adifier' ),
			            "description" 	=> esc_html__( 'Select an icon', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'Icon Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select color of the icon', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'bg_color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'Icon Background Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select background color of the icon', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'size',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Icon Size?', 'adifier' ),
			            "description" 	=> esc_html__( 'Input size of the icon in pixels', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'width',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Box Width', 'adifier' ),
			            "description" 	=> esc_html__( 'Input width in pixels for the box of the icon', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'height',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Box Width', 'adifier' ),
			            "description" 	=> esc_html__( 'Input height in pixels for the box of the icon', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_blogs' => array(
                'name' 			=> esc_html__( 'Blogs', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'post_ids',
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'post_type'		=> 'post'
			            ),
			            'label' 		=> esc_html__( 'Blogs', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which blogs to show or leave empty and input number of latest to show in field below', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'post_number',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Number Of Posts', 'adifier' ),
			            "description" 	=> esc_html__( 'Input number of posts to show', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'slider',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show In Slider', 'adifier' ),
			            "description" 	=> esc_html__( 'To show posts in slider or not', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'visible_items',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Visible Items', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display on each slide', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'autoplay_speed',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Autoplay Speed (in ms)', 'adifier' ),
			            "description" 	=> esc_html__( 'Leave emptyto disable', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'double_row',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Double Row', 'adifier' ),
			            "description" 	=> esc_html__( 'If you want to show posts in two rows on each slide set this option to Yes', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'items_in_row',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'no'
				    	),
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'1'	=> '1',
			            	'2'	=> '2',
			            	'3'	=> '3',
			            ),
			            'label' 		=> esc_html__( 'Items In Row', 'adifier' ),
			            "description" 	=> esc_html__( 'Select how many items to display in each row', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_price_tables' => array(
                'name' 			=> esc_html__( 'Price Tables', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'package',
			            'type' 			=> 'select',
			            'options'		=> adifier_pt_packs(),
			            'label' 		=> esc_html__( 'Package / Subscription', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which package /subscription to show ', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'is_active',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'	=> esc_html__( 'No', 'adifier' ),
			            	'yes'	=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Is Active', 'adifier' ),
			            "description" 	=> esc_html__( 'If this package is active it will stand out more', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_promotion_map' => array(
                'name' 			=> esc_html__( 'Promotion Map', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'height',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Map Height', 'adifier' ),
			            "description" 	=> esc_html__( 'Input map height. For example 400px', 'adifier' )
			        )
			     )
            ),
        ),
        array(
            'kc_af_title' => array(
                'name' 			=> esc_html__( 'Title', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'heading',
			            'label' 		=> esc_html__( 'Heading', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    1 => 1,
						    2 => 2,
						    3 => 3,
						    4 => 4,
						    5 => 5,
						    6 => 6,
						),				            
			            "description" 	=> esc_html__("Select heading for title","adifier")
			        ),
			        array(
			            'name' 			=> 'align',
			            'label' 		=> esc_html__( 'Align', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'left' 			=> esc_html__( 'Left', 'adifier' ),
						    'center' 		=> esc_html__( 'Center', 'adifier' ),
						    'right' 		=> esc_html__( 'Right', 'adifier' )
						),				            
			            "description" 	=> esc_html__("Select title text align","adifier")
			        ),
			        array(
			            'name' 			=> 'title',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Title Text', 'adifier' ),
			            "description" 	=> esc_html__( 'Input title text. If you want to have some words colored use span like this <span style="color: #fff">TEXT</span> for bold use b tag <b>TEXT</b>', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'subtitle',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Subtitle Text', 'adifier' ),
			            "description" 	=> esc_html__( 'Input subtitle text. If you want to have some words colored use span like this <span style="color: #fff">TEXT</span> for bold use b tag <b>TEXT</b>', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_typed_text' => array(
                'name' 			=> esc_html__( 'Typed Text', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'tag',
			            'label' 		=> esc_html__( 'Tag Wrapper', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'h1' => 'h1',
						    'h2' => 'h2',
						    'h3' => 'h3',
						    'h4' => 'h4',
						    'h5' => 'h5',
						    'h6' => 'h6',
						    'p' => 'p',
						),				            
			            "description" 	=> esc_html__("Select tag wrapper for the text","adifier")
			        ),
			        array(
			            'name' 			=> 'align',
			            'label' 		=> esc_html__( 'Align', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'left' 			=> esc_html__( 'Left', 'adifier' ),
						    'center' 		=> esc_html__( 'Center', 'adifier' ),
						    'right' 		=> esc_html__( 'Right', 'adifier' )
						),				            
			            "description" 	=> esc_html__("Select title text align","adifier")
			        ),
			        array(
			            'name' 			=> 'color',
			            'label' 		=> esc_html__( 'Color', 'adifier' ),
			            'type' 			=> 'color_picker',
			        ),
			        array(
			            'name' 			=> 'smart',
			            'label' 		=> esc_html__( 'Smart Word Guess', 'adifier' ),
			            'type' 			=> 'select',
						'options' 		=> array(
						    'yes' 	=> esc_html__( 'Yes', 'adifier' ),
						    'no' 	=> esc_html__( 'No', 'adifier' ),
						),				            
			            "description" 	=> esc_html__("If this is enabled and you have multiple texts for example 'We Are Number One' 'We Are Awesome' script will recognize We Are and will type only text after it otherwise it will type it all","adifier")
			        ),
			        array(
			            'name' 			=> 'speed',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Type Speed', 'adifier' ),
			        ),
			        array(
			            'name' 			=> 'back_speed',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Back Speed', 'adifier' ),
			        ),
			        array(
			            'name' 			=> 'texts',
			            'type' 			=> 'group',
			            'params'		=> array(
					        array(
					            'name' 			=> 'text',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Text', 'adifier' ),
					        ),
			            ),
			            "description" 	=> esc_html__( 'Input text to be typed', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_af_locations' => array(
                'name' 			=> esc_html__( 'Locations', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'style',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'style1' => esc_html__( 'Style 1', 'adifier' ),
			            	'style2' => esc_html__( 'Style 2', 'adifier' ),
			            	'style3' => esc_html__( 'Style 3', 'adifier' ),
			            	'style4' => esc_html__( 'Style 4', 'adifier' ),
			            	'style5' => esc_html__( 'Style 5', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Style', 'adifier' ),
			            "description" 	=> esc_html__( 'Select style for displaying locations', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'grouped_terms',
			            'label' 		=> esc_html__( 'Locations', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(
					        array(
					            'name' 			=> 'term_id',
					            'type'			=> 'autocomplete',
					            'options'		=> array(
					            	'multiple'		=> false,
					            	'taxonomy'		=> 'advert-location'
					            ),
					            'label' 		=> esc_html__( 'Location', 'adifier' ),
					            "description" 	=> esc_html__( 'Select location to dispaly', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'image',
					            'type' 			=> 'attach_image',
					            'label' 		=> esc_html__( 'Image', 'adifier' ),
					            "description" 	=> esc_html__( 'Select image representative for location', 'adifier' )
					        ),
						),				            
			            "description" 	=> esc_html__("Select location","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_af_locations_list' => array(
                'name' 			=> esc_html__( 'Locations In List', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'list_color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'List Items Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select color of the items in the list', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'location_ids',
			            'type'			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-location'
			            ),
			            'label' 		=> esc_html__( 'Locations', 'adifier' ),
			            "description" 	=> esc_html__( 'Select location to dispaly', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'columns',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'1'	=> '1',
			            	'2'	=> '2',
			            	'3'	=> '3',
			            	'4'	=> '4',
			            	'5'	=> '5',
			            ),
			            'label' 		=> esc_html__( 'Colulmns', 'adifier' ),
			            "description" 	=> esc_html__( 'In how many columns to display the list', 'adifier' )
			        ),
                )
            ),
        ),
        array(
            'kc_categories_transparent' => array(
                'name' 			=> esc_html__( 'Categories Transparent', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'visible_items',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Visible Items', 'adifier' ),
			            "description" 	=> esc_html__( 'How many items to be visible in ths slider', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'autoplay_speed',
				    	'relation'		=> array(
				    		'parent'		=> 'slider',
				    		'show_when'		=> 'yes'
				    	),
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Autoplay Speed (in ms)', 'adifier' ),
			            "description" 	=> esc_html__( 'Leave emptyto disable', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'grouped_terms',
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(
					        array(
					            'name' 			=> 'term_id',
					            'type'			=> 'autocomplete',
					            'options'		=> array(
					            	'multiple'		=> false,
					            	'taxonomy'		=> 'advert-category'
					            ),
					            'label' 		=> esc_html__( 'Category', 'adifier' ),
					            "description" 	=> esc_html__( 'Select category to display', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'image',
					            'type' 			=> 'attach_image',
					            'label' 		=> esc_html__( 'Image', 'adifier' ),
					            "description" 	=> esc_html__( 'Select image representative for category or leave empty to use associated one', 'adifier' )
					        ),
						),				            
			            "description" 	=> esc_html__("Select categories","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_quick_search' => array(
                'name' 			=> esc_html__( 'Quick Search', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array()
            ),
        ),
        array(
            'kc_slider_bg_text' => array(
                'name' 			=> esc_html__( 'Image/Text Slider', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'text_color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'Text Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select color of the text', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'speed',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Slider Timeout', 'adifier' ),
			            "description" 	=> esc_html__( 'For how long to display the slide in ms', 'adifier' )
			        ),			        
			        array(
			            'name' 			=> 'grouped_slides',
			            'label' 		=> esc_html__( 'Slides', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(
					        array(
					            'name' 			=> 'big_text',
					            'type'			=> 'text',
					            'label' 		=> esc_html__( 'Big Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input big text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'small_text',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Small Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input small text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'image',
					            'type' 			=> 'attach_image',
					            'label' 		=> esc_html__( 'Image', 'adifier' ),
					            "description" 	=> esc_html__( 'Select image', 'adifier' )
					        ),
						),				            
			            "description" 	=> esc_html__("Create slides","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_interactive_slider' => array(
                'name' 			=> esc_html__( 'Interactive Slider', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'dots_color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'Dots Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select color of dots navigation', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'slider_height',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Height', 'adifier' ),
			            "description" 	=> esc_html__( 'INput height of the slider in pixels (i.e. 450px)', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'speed',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Slider Timeout', 'adifier' ),
			            "description" 	=> esc_html__( 'For how long to display the slide in ms', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'grouped_slides',
			            'label' 		=> esc_html__( 'Slides', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(							
					        array(
					            'name' 			=> 'big_text',
					            'type'			=> 'text',
					            'label' 		=> esc_html__( 'Big Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input big text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'big_text_color',
					            'type' 			=> 'color_picker',
					            'label' 		=> esc_html__( 'Big Text Color', 'adifier' ),
					            "description" 	=> esc_html__( 'Select color of the big text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'small_text',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Small Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input small text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'small_text_color',
					            'type' 			=> 'color_picker',
					            'label' 		=> esc_html__( 'Small Text Color', 'adifier' ),
					            "description" 	=> esc_html__( 'Select color of the small text color', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'button_text',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Text Button', 'adifier' ),
					            "description" 	=> esc_html__( 'Input text of the button', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'button_link',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Button Link', 'adifier' ),
					            "description" 	=> esc_html__( 'Input link for the button', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'image',
					            'type' 			=> 'attach_image',
					            'label' 		=> esc_html__( 'Image', 'adifier' ),
					            "description" 	=> esc_html__( 'Select image', 'adifier' )
					        ),
						),				            
			            "description" 	=> esc_html__("Create slides","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_text_slider' => array(
                'name' 			=> esc_html__( 'Text Slider', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'text_color',
			            'type' 			=> 'color_picker',
			            'label' 		=> esc_html__( 'Text Color', 'adifier' ),
			            "description" 	=> esc_html__( 'Select color of the text', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'speed',
			            'type' 			=> 'text',
			            'label' 		=> esc_html__( 'Slider Timeout', 'adifier' ),
			            "description" 	=> esc_html__( 'For how long to display the slide in ms', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'align',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'left'			=> esc_html__( 'Left', 'adifier' ),
			            	'center'		=> esc_html__( 'Center', 'adifier' ),
			            	'right'			=> esc_html__( 'Right', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Align', 'adifier' ),
			            "description" 	=> esc_html__( 'Select align of the text', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'grouped_slides',
			            'label' 		=> esc_html__( 'Slides', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(
					        array(
					            'name' 			=> 'big_text',
					            'type'			=> 'text',
					            'label' 		=> esc_html__( 'Big Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input big text', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'small_text',
					            'type' 			=> 'text',
					            'label' 		=> esc_html__( 'Small Text', 'adifier' ),
					            "description" 	=> esc_html__( 'Input small text', 'adifier' )
					        ),
						),				            
			            "description" 	=> esc_html__("Create slides","adifier")
			        ),
                )
            ),
        ),
        array(
            'kc_how_it_works' => array(
                'name' 			=> esc_html__( 'How It Works', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'hiw_item',
			            'label' 		=> esc_html__( 'Item', 'adifier' ),
			            'type' 			=> 'group',
						'params' 		=> array(
					        array(
					            'name' 			=> 'icon',
					            'type'			=> 'icon_picker',
					            'label' 		=> esc_html__( 'Icon', 'adifier' ),
					            "description" 	=> esc_html__( 'Select icon', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'icon_bg_color',
					            'type'			=> 'color_picker',
					            'label' 		=> esc_html__( 'Icon BG Color', 'adifier' ),
					            "description" 	=> esc_html__( 'Select icon background color', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'icon_font_color',
					            'type'			=> 'color_picker',
					            'label' 		=> esc_html__( 'Icon Font Color', 'adifier' ),
					            "description" 	=> esc_html__( 'Select icon font color', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'icon_bg_color_hover',
					            'type'			=> 'color_picker',
					            'label' 		=> esc_html__( 'Icon BG Color Hover', 'adifier' ),
					            "description" 	=> esc_html__( 'Select icon background color on hover', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'icon_font_color_hover',
					            'type'			=> 'color_picker',
					            'label' 		=> esc_html__( 'Icon Font Color Hover', 'adifier' ),
					            "description" 	=> esc_html__( 'Select icon font color on hover', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'title',
					            'type'			=> 'text',
					            'label' 		=> esc_html__( 'Title', 'adifier' ),
					            "description" 	=> esc_html__( 'Input title', 'adifier' )
					        ),
					        array(
					            'name' 			=> 'description',
					            'type'			=> 'text',
					            'label' 		=> esc_html__( 'Description', 'adifier' ),
					            "description" 	=> esc_html__( 'Input description', 'adifier' )
					        ),
						)
			        ),
                )
            ),
        ),
        array(
            'kc_categories_table' => array(
                'name' 			=> esc_html__( 'Categories Table', 'adifier' ),
                'category' 		=> 'Content',
                'params' 		=> array(
			        array(
			            'name' 			=> 'category_ids',
			            'type' 			=> 'autocomplete',
			            'options'		=> array(
			            	'multiple'		=> true,
			            	'taxonomy'		=> 'advert-category'
			            ),
			            'label' 		=> esc_html__( 'Categories', 'adifier' ),
			            "description" 	=> esc_html__( 'Select which categories to display or leave empty to show all of them', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'columns',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'2' => '2',
			            	'3' => '3',
			            	'4' => '4',
			            	'5' => '5',
			            ),
			            'label' 		=> esc_html__( 'Columns', 'adifier' ),
			            "description" 	=> esc_html__( 'In how many columns to show the categories', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'show_count',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show Count', 'adifier' ),
			            "description" 	=> esc_html__( 'Show or hide number of ads per category', 'adifier' )
			        ),
			        array(
			            'name' 			=> 'show_empty',
			            'type' 			=> 'select',
			            'options'		=> array(
			            	'no'			=> esc_html__( 'No', 'adifier' ),
			            	'yes'			=> esc_html__( 'Yes', 'adifier' ),
			            ),
			            'label' 		=> esc_html__( 'Show empty?', 'adifier' ),
			            "description" 	=> esc_html__( 'Select yes if you want to dispaly empty categories', 'adifier' )
			        ),			        
                )
            ),
        ),
	);
}
}


if( !function_exists('adifier_remove_default_elements') ){
function adifier_remove_default_elements( $atts, $base ){
    if( in_array( 
    		$base, 
    		array( 
    			'kc_box', 
    			'kc_image_gallery',
    			'kc_title',
    			'kc_twitter_feed',
    			'kc_instagram_feed',
    			'kc_fb_recent_post',
    			'kc_flip_box', 
    			'kc_counter_box',
    			'kc_post_type_list', 
    			'kc_carousel_images',
    			'kc_carousel_post',
    			'kc_coundown_timer',
    			'kc_divider',
    			'kc_testimonial', 
    			'kc_team',
    			'kc_pricing',
    			'kc_image_fadein',
    			'kc_image_hover_effects',
    			'kc_blog_posts',
    			'kc_nested',
    			'kc_creative_button'
    		) 
    	) )
    {
        return null;
    }

    return $atts;
}
add_filter('kc_add_map', 'adifier_remove_default_elements', 1 , 2 );
}

/* If user is using king composer */
if( !function_exists('adifier_kingcomposer_shortcodes') && function_exists( 'kc_add_map' ) ){
function adifier_kingcomposer_shortcodes() {
	global $kc;

	$shortcode_template = get_theme_file_path( 'includes/shortcodes/kingcomposer/' );
    $kc->set_template_path( $shortcode_template );

	$kc->add_map_param(
	    'kc_row',
	    array(
	       'name' 			=> 'bg_slider',
		   'label' 			=> esc_html__('BG Slider Images', 'adifier'),
		   'type' 			=> 'attach_images'
	    ), 
	    2
	);

	$shortcodes = adifier_get_shortcodes();
	foreach( $shortcodes as $shortcode ){
	    kc_add_map( $shortcode );			
	}

}  
add_action('init', 'adifier_kingcomposer_shortcodes', 99 );
}

/* 
* If user is using Beaver Builder
* We need to remap kc array for shortcodes to be beaver friendly
*/
if( !function_exists('adifier_beaverbuilder_shortcodes') && class_exists( 'FLBuilder' ) ){
function adifier_beaverbuilder_shortcodes(){
	foreach ( glob( plugin_dir_path( __FILE__ ).'/beaverbuilder/*' ) as $filename ){
		include( get_theme_file_path( 'includes/shortcodes/beaverbuilder/'.basename( $filename ).'/'.basename( $filename ).'.php' ) );
	}

	$shortcodes = adifier_get_shortcodes();
	foreach( $shortcodes as $shortcode ){
		/*  first if we have group type we need to extract those */
		foreach( $shortcode[key($shortcode)]['params'] as $key => $param ){
			if( $param['type'] == 'group' ){
				foreach( $param['params'] as $subparam ){
					if( $subparam['type'] == 'text' ){
						$subparam['type'] = 'textarea';
						$subparam['description'] = ( !empty( $subparam['description'] ) ? $subparam['description'] : '' ).' '.esc_html__( 'Separate items with new line ( ENTER/RETURN )', 'adifier' );
					}
					$shortcode[key($shortcode)]['params'][] = $subparam;
				}

				unset( $shortcode[key($shortcode)]['params'][$key] );
			}
		}

		/* first we need to remap module fields to beaver frinedly array since basic ones are created for KC */
		$fields = array();
		foreach( $shortcode[key($shortcode)]['params'] as $param ){
			$type = adifier_beaverbuilder_type_remap( $param['type'] );
			$fields[$param['name'].'_fld'] = array(
				'type' 			=> $type,
				'label'			=> $param['label'],
				'description'	=> !empty( $param['description'] ) ? $param['description'] : '',
			);
			if( $type == 'select' ){
				$fields[$param['name'].'_fld']['options'] = $param['options'];
				$fields[$param['name'].'_fld']['default'] = key($param['options']);
			}
			if( $type == 'suggest' ){
				if( !empty( $param['options']['post_type'] ) ){
					$fields[$param['name'].'_fld']['data'] = $param['options']['post_type'];
					$fields[$param['name'].'_fld']['action'] = 'fl_as_posts';
				}
				else{
					$fields[$param['name'].'_fld']['data'] = $param['options']['taxonomy'];	
					$fields[$param['name'].'_fld']['action'] = 'fl_as_terms';
				}
			}

			if( !empty( $param['relation'] ) ){
				$parent = $param['relation']['parent'].'_fld';
				$show_when = (array)$param['relation']['show_when'];
				if( empty( $fields[$parent]['toggle'] ) ){
					$fields[$parent]['toggle'] = array();
					foreach( $show_when as $value ){
						$fields[$parent]['toggle'][$value]['fields'] = array();
					}
				}
				foreach( $show_when as $value ){
					$fields[$parent]['toggle'][$value]['fields'][] = $param['name'].'_fld';
				}
			}
		}

		FLBuilder::register_module('Adifier_'.key( $shortcode ), array(
		    'general'       => array( // Tab
		        'title'         => esc_html__('General', 'adifier'), // Tab title
		        'sections'      => array( // Tab Sections
		            'general'       => array( // Section
		                'title'         => esc_html__('Options', 'adifier'), // Section Title
		                'fields'        => $fields
		            )
		        )
		    )
		));
	}

}
add_action( 'init', 'adifier_beaverbuilder_shortcodes' );
}

/*
* Let's hook into column settings of the beaver builder so there can be shadow setting
*/
if( !function_exists( 'adifier_beaverbuilder_column_settings' ) ){
function adifier_beaverbuilder_column_settings( $form, $id ){
	if( $id == 'col' ){
		$form['tabs']['style']['sections']['box'] = array(
			'title'		=> esc_html__( 'Box', 'adifier' ),
			'fields'	=> array(
				'shadow'          => array(
					'type'          => 'text',
					'label'         => esc_html__( 'Shadow', 'adifier' )
				),
				'border_radius'   => array(
					'type'          => 'text',
					'label'         => esc_html__( 'Border Radius', 'adifier' ),
					'help'        	=> esc_html__( 'In form TOP RIGHT BOTTOM LEFT for example 10px 10px 0px 0px', 'adifier' ),
				)				
			)
		);
	}

	return $form;
}
add_filter( 'fl_builder_register_settings_form', 'adifier_beaverbuilder_column_settings', 10, 2 );
}

/*
* Now let's check if the shadow is populated and if so apply to inline style
*/
if( !function_exists('adifier_beaverbuilder_column_add_shadow') ){
function adifier_beaverbuilder_column_add_shadow( $nodes, $col ){
	$custom_css = array();
	if ( !empty( $col->settings->shadow ) ) {
		$custom_css[] = 'box-shadow: '.$col->settings->shadow.';';
	}

	if ( !empty( $col->settings->border_radius ) ) {
		$custom_css[] = 'border-radius: '.$col->settings->border_radius.';';
	}

	if( !empty( $custom_css ) ){
		?>
		<style>
			.fl-node-<?php echo esc_attr( $col->node ); ?> > .fl-col-content{
				<?php echo implode( ' ', $custom_css ) ?>
			}
		</style>
		<?php
	}
}
add_action( 'fl_builder_before_render_modules', 'adifier_beaverbuilder_column_add_shadow', 10, 2 );
}

/*
* Beaver builder convert KC param type to beaver
*/
if( !function_exists('adifier_beaverbuilder_type_remap') ){
function adifier_beaverbuilder_type_remap( $type ){
	switch( $type ){
		case 'color_picker' 	: return 'color'; break;
		case 'icon_picker' 		: return 'icon'; break;
		case 'autocomplete' 	: return 'suggest'; break;
		case 'attach_image' 	: return 'multiple-photos'; break;
		default 				: return $type; break;
	}
}
}

/*
* Convert beaver settings to KC atts
*/
if( !function_exists('adifier_beaverbuilder_get_atts') ){
function adifier_beaverbuilder_get_atts( $settings ){
	$atts = array();
	$settings = json_decode( json_encode( $settings ), true );
	
	if( in_array( $settings['type'], array( 'kc_categories_transparent', 'kc_af_locations' ) ) ){
		$atts['grouped_terms'] = array();
		if( !empty( $settings['term_id_fld'] ) ){
			$term_ids = explode( ',', $settings['term_id_fld'] );
			if( !empty( $term_ids ) ){
				foreach( $term_ids as $key => $value ){
					$atts['grouped_terms'][] = (object)array(
						'term_id' 	=> $value,
						'image' 	=> !empty( $settings['image_fld'][$key] ) ? $settings['image_fld'][$key] : '',
					);
				}
			}
		}
		unset( $settings['term_id_fld'] );
		unset( $settings['image_fld'] );
	}
	else if ( in_array( $settings['type'], array( 'kc_slider_bg_text', 'kc_text_slider' ) ) ){
		$atts['grouped_slides'] = array();
		$big_texts = explode( "\n", $settings['big_text_fld'] );
		$small_texts = explode( "\n", $settings['small_text_fld'] );
		if( !empty( $big_texts ) ){
			foreach( $big_texts as $key => $value ){
				$atts['grouped_slides'][] = (object)array(
					'big_text' 		=> $value,
					'small_text' 	=> !empty( $small_texts[$key] ) ? $small_texts[$key] : '',
					'image' 		=> !empty( $settings['image_fld'][$key] ) ? $settings['image_fld'][$key] : '',
				);
			}
		}
		unset( $settings['big_text_fld'] );
		unset( $settings['small_text_fld'] );
		unset( $settings['image_fld'] );
	}

	foreach( $settings as $key => $value ){
		if( strpos( $key, '_fld') !== false ){
			$atts[str_replace( '_fld', '', $key )] = $value;
		}
	}

	return $atts;
}
}


/*
* Get packs list for price table shortcode
*/
if( !function_exists('adifier_pt_packs') ){
function adifier_pt_packs(){
	$account_payment = adifier_get_option( 'account_payment' );
	$packs = adifier_get_packs( $account_payment, true );
	if( !empty( $packs ) ){
		return wp_list_pluck( $packs, 'name' );
	}
	else{
		return array();
	}
}
}

/*
* Autocomplete function for king composer
*/
if( !function_exists('adifier_autocomplete_post_type') ){
function adifier_autocomplete_post_type( $data ){

	$args = array(
		'post_type'			=> $_POST['post_type'],
		'posts_per_page'	=> '-1',
		'post_status'		=> 'publish',
		's'					=> $_POST['s']
	);

	if( !empty( $data['post_format'] ) ){
		$args['tax_query'] = array(
			array(
				'taxonomy'	=> 'post_format',
				'field'		=> 'slug',
				'terms'		=> $data['post_format']
			)
		);
	}

	$posts = get_posts( $args );

	$result = array();
	if( !empty( $posts ) ){
		foreach( $posts as $post ){
			$result[] = $post->ID.':'.$post->post_title;
		}
	}

    return array( 'Results' => $result ); 
}
add_filter( 'kc_autocomplete_post_ids', 'adifier_autocomplete_post_type' );
}

if( !function_exists('adifier_autocomplete_taxonomy') ){
function adifier_autocomplete_taxonomy( $data ){

	$terms = get_terms(array(
		'taxonomy'		=> $_POST['taxonomy'],
		'name__like'	=> $_POST['s'],
		'hide_empty'	=> false
	));

	$result = array();
	if( !empty( $terms ) ){
		foreach( $terms as $term ){
			$result[] = $term->term_id.':'.$term->name;
		}
	}

    return array( 'Results' => $result ); 
}
add_filter( 'kc_autocomplete_category_ids', 'adifier_autocomplete_taxonomy' );
add_filter( 'kc_autocomplete_location_ids', 'adifier_autocomplete_taxonomy' );
for( $i=0; $i<20;$i++ ){
	add_filter( 'kc_autocomplete_grouped_terms['.$i.'][term_id]', 'adifier_autocomplete_taxonomy' );	
}
}

?>