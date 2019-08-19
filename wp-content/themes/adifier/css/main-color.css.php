<?php
	
	/* MAIN COLOR */
	$main_color 				= adifier_get_option( 'main_color' );
	$main_color_hover 			= adifier_get_option( 'main_color_hover' );
	$main_color_font 			= adifier_get_option( 'main_color_font' );
	$main_color_font_hover 		= adifier_get_option( 'main_color_font_hover' );

	/*SEARCH BTN*/
	$search_btn_bg 				= adifier_get_option( 'search_btn_bg' );
	$search_btn_bg_hover 		= adifier_get_option( 'search_btn_bg_hover' );
	$search_btn_font 			= adifier_get_option( 'search_btn_font' );
	$search_btn_font_hover 		= adifier_get_option( 'search_btn_font_hover' );

	/*LOGO*/
	$logo_width 				= adifier_get_option( 'logo_width' );
	$logo_height 				= adifier_get_option( 'logo_height' );

	/*BREADCRUMBS*/
	$breadcrumbs_bg_color 		= adifier_get_option( 'breadcrumbs_bg_color' );
	$breadcrumbs_font_color 	= adifier_get_option( 'breadcrumbs_font_color' );
	$breadcrumbs_image_bg 		= adifier_get_option( 'breadcrumbs_image_bg' );

	/*TYPOGRAPHY*/
	$link_color 				= adifier_get_option( 'link_color' );
	$price_color 				= adifier_get_option( 'price_color' );
	$text_font 					= adifier_get_option( 'text_font' );
	$text_font_size 			= adifier_get_option( 'text_font_size' );
	$text_font_line_height 		= adifier_get_option( 'text_font_line_height' );
	$text_font_weight 			= adifier_get_option( 'text_font_weight' );
	$text_font_color 			= adifier_get_option( 'text_font_color' );
	$title_font 				= adifier_get_option( 'title_font' );
	$title_font_weight 			= adifier_get_option( 'title_font_weight' );
	$title_font_color 			= adifier_get_option( 'title_font_color' );
	$heading_line_height 		= adifier_get_option( 'heading_line_height' );
	$h1_font_size 				= adifier_get_option( 'h1_font_size' );
	$h2_font_size 				= adifier_get_option( 'h2_font_size' );
	$h3_font_size 				= adifier_get_option( 'h3_font_size' );
	$h4_font_size 				= adifier_get_option( 'h4_font_size' );
	$h5_font_size 				= adifier_get_option( 'h5_font_size' );
	$h6_font_size 				= adifier_get_option( 'h6_font_size' );

	/*FOOTER BG COLOR*/
	$footer_bg_color 			= adifier_get_option( 'footer_bg_color' );
	$footer_font_color 			= adifier_get_option( 'footer_font_color' );
	$footer_active_color 		= adifier_get_option( 'footer_active_color' );

	/* PRICE TABLE*/
	$pt_price_bg_color 			= adifier_get_option( 'pt_price_bg_color' );
	$pt_price_font_color 		= adifier_get_option( 'pt_price_font_color' );
	$pt_title_bg_color 			= adifier_get_option( 'pt_title_bg_color' );
	$pt_title_font_color 		= adifier_get_option( 'pt_title_font_color' );
	$pt_btn_bg_color 			= adifier_get_option( 'pt_btn_bg_color' );
	$pt_btn_font_color 			= adifier_get_option( 'pt_btn_font_color' );
	$pt_btn_bg_color_hover 		= adifier_get_option( 'pt_btn_bg_color_hover' );
	$pt_btn_font_color_hover 	= adifier_get_option( 'pt_btn_font_color_hover' );
	$pt_ac_price_bg_color 		= adifier_get_option( 'pt_ac_price_bg_color' );
	$pt_ac_price_font_color 	= adifier_get_option( 'pt_ac_price_font_color' );
	$pt_ac_title_bg_color 		= adifier_get_option( 'pt_ac_title_bg_color' );
	$pt_ac_title_font_color 	= adifier_get_option( 'pt_ac_title_font_color' );
	$pt_ac_btn_bg_color 		= adifier_get_option( 'pt_ac_btn_bg_color' );
	$pt_ac_btn_font_color 		= adifier_get_option( 'pt_ac_btn_font_color' );
	$pt_ac_btn_bg_color_hover 	= adifier_get_option( 'pt_ac_btn_bg_color_hover' );
	$pt_ac_btn_font_color_hover = adifier_get_option( 'pt_ac_btn_font_color_hover' );

	/*COYRIGHTS*/
	$copyrights_bg_color 		= adifier_get_option( 'copyrights_bg_color' );
	$copyrights_font_color 		= adifier_get_option( 'copyrights_font_color' );
	$copyrights_active_color 	= adifier_get_option( 'copyrights_active_color' );

	/*DARK NAVIGATION*/
	$dark_nav_bg_color 			= adifier_get_option( 'dark_nav_bg_color' );
	$dark_nav_font_color 		= adifier_get_option( 'dark_nav_font_color' );
	$dark_nav_font_color_active = adifier_get_option( 'dark_nav_font_color_active' );

	/*SUBSCRIPTION*/
	$subscription_bg_color 		= adifier_get_option( 'subscription_bg_color' );
	$subscription_font_color 	= adifier_get_option( 'subscription_font_color' );

	/*CTA*/
	$contact_phone_icon_bg_color 	= adifier_get_option( 'contact_phone_icon_bg_color' );
	$contact_phone_bg_color 		= adifier_get_option( 'contact_phone_bg_color' );
	$contact_phone_font_color 		= adifier_get_option( 'contact_phone_font_color' );
	$contact_msg_icon_bg_color 		= adifier_get_option( 'contact_msg_icon_bg_color' );
	$contact_msg_bg_color 			= adifier_get_option( 'contact_msg_bg_color' );
	$contact_msg_font_color 		= adifier_get_option( 'contact_msg_font_color' );


	$custom_css 				= adifier_get_option( 'custom_css' );
?>

body,
.mapboxgl-popup-content .price{
	font-family: '<?php echo esc_html( $text_font ) ?>', Arial, Helvetica, sans-serif;
	font-size: <?php echo  esc_html( $text_font_size ) ?>;
	line-height: <?php echo esc_html( $text_font_line_height ) ?>;
	font-weight: <?php echo esc_html( $text_font_weight ) ?>;
	color: <?php echo esc_html( $text_font_color ) ?>;
}

.mapboxgl-popup-content .price{
	font-weight: 600;
}

input[type="submit"],
a, a:active, a:focus{
	color: <?php echo esc_html( $link_color ) ?>
}

/* FONT 500 */
.navigation li a,
.special-nav a,
.single-advert-title .breadcrumbs{
	font-family: '<?php echo esc_html( $title_font ) ?>', sans-serif;
}

/* FONT 400 */
.author-address em,
.contact-seller em,
.reveal-phone em,
.header-search select,
.header-search input{
	font-family: '<?php echo esc_html( $title_font ) ?>', sans-serif;
}

.pagination > span,
.pagination a,
body .kc_tabs_nav > li > a{
	font-family: '<?php echo esc_html( $title_font ) ?>', sans-serif;
	font-weight: <?php echo esc_html( $title_font_weight ) ?>;
	color: <?php echo esc_html( $title_font_color ) ?>;
}

.header-alike,
.af-title p,
.element-qs input{
	font-family: '<?php echo esc_html( $title_font ) ?>', sans-serif;
}

h1, h2, h3, h4, h5, h6{
	font-family: '<?php echo esc_html( $title_font ) ?>', sans-serif;
	line-height: <?php echo esc_html( $heading_line_height ) ?>;
}


h1, h2, h3, h4, h5, h6,
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,
h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus{
	color: <?php echo esc_html( $title_font_color ) ?>;
}

h1{
	font-size: <?php echo esc_html( $h1_font_size ) ?>;
}

h2{
	font-size: <?php echo esc_html( $h2_font_size ) ?>;
}

h3{
	font-size: <?php echo esc_html( $h3_font_size ) ?>;
}

h4{
	font-size: <?php echo esc_html( $h4_font_size ) ?>;
}

h5{
	font-size: <?php echo esc_html( $h5_font_size ) ?>;
}

h6{
	font-size: <?php echo esc_html( $h6_font_size ) ?>;
}

a:hover,
.article-title a:hover,
h1 a:focus:hover, h2 a:focus:hover, h3 a:focus:hover, h4 a:focus:hover, h5 a:focus:hover, h6 a:focus:hover,
.styled-radio.active label:before,
.styled-radio input:checked + label:before,
.styled-checkbox.active label:before,
.styled-checkbox input:checked + label:before,
.owl-video-play-icon:hover:before,
.adverts-slider .owl-nav > div,
.account-btn,
.account-btn:focus,
.account-btn:active,
.navigation a:hover,
.navigation li.current-menu-ancestor > a,
.navigation li.current_page_ancestor > a,
.navigation li.current_page_ancestor > a:visited,
.navigation li.current_page_item > a,
.navigation li.current_page_item > a:visited,
.navigation li.current-menu-item > a,
.navigation li.current-menu-item > a:visited,
.bid-login,
.bid-login:active,
.bid-login:focus,
.bid-login:hover,
.error404 .white-block-content i,
.or-divider h6,
.cf-loader,
.layout-view a.active,
.no-advert-found i,
.advert-hightlight .white-block-content h5 a,
.single-advert-actions li a:hover,
.widget_adifier_advert_locations i,
body .kc_accordion_header.ui-state-active > a,
.author-no-listing i,
.adverts-filter ul li.active a,
.image-input-wrap a:hover i,
.mess-loading,
.con-loading,
.open-reponse-form,
.promotion-description-toggle,
.promotion-description-toggle:focus,
#purchase .loader,
.purchase-loader i,
.video-input-wrap a:hover,
.another-video:hover,
.user-rating,
.rate-user,
.reset-search:focus:hover,
.element-categories-tree li a:hover,
.element-categories-tree .view-more a:hover,
.advert-item .aficon-heart,
.random-author-ads .aficon-heart,
.advert-card .compare-add.active,
.compare-add.active,
.compare-add.active:hover,
.compare-add.active:active,
.toggle-conversations,
.toggle-conversations:hover,
.toggle-conversations:active,
.element-categories-table > a:hover h6,
.advert-hightlight .white-block-content .price, 
.advert-hightlight .white-block-content h5 a,
.user-details-list a, 
.user-details-list a:active, 
.user-details-list a:focus,
.element-categories-v-list a:hover h5
{
	color: <?php echo esc_html( $main_color ) ?>;
}

@media (max-width: 1024px){
	.small-sidebar-open, .special-nav a, .special-nav a:focus, .special-nav a:active{
		color: <?php echo esc_html( $main_color ) ?>;
	}
}

blockquote,
.owl-carousel .owl-video-play-icon:hover,
.owl-video-play-icon:hover:before,
.filter-slider.ui-slider .ui-state-default, 
.filter-slider.ui-slider .ui-widget-content .ui-state-default,
.filter-slider.ui-slider .ui-state-focus, 
.filter-slider.ui-slider .ui-state-hover, 
.filter-slider.ui-slider .ui-widget-content .ui-state-focus,
.filter-slider.ui-slider .ui-widget-content .ui-state-hover,
.promotion:not(.disabled):not(.inactive) .promo-price-item:hover,
.layout-view a:hover,
.adverts-filter ul li.active a,
input:focus, 
textarea:focus, 
select:focus,
.select2-container--open.select2-container--default .select2-selection--single
{
	border-color: <?php echo esc_html( $main_color ) ?>;
}

.rtl .conversation-wrap.current,
.rtl .conversation-wrap:hover{
	border-right-color: <?php echo esc_html( $main_color ) ?>;	
}

.author-sidebar li.active{
	border-left-color: <?php echo esc_html( $main_color ) ?>;
}

.scroll-element .scroll-element_track,
.scroll-element .scroll-bar,
.scroll-element:hover .scroll-bar
.scroll-element.scroll-draggable .scroll-bar,
.pagination > span:not(.dots),
.pagination a.current,
.af-interactive-slider a
{
	background-color: <?php echo esc_html( $main_color ) ?>;
}

.af-button,
input[type="submit"],
.af-button:focus,
.af-button:active,
.filter-slider.ui-slider .ui-slider-range,
.comment-avatar .icon-user,
.author-address,
.single-price,
.kc-search .af-button,
.kc-search .af-button:hover,
.kc-search .af-button:focus,
.kc-search .af-button:active,
body .kc_tabs_nav > .ui-tabs-active > a,
body .kc_tabs_nav > .ui-tabs-active > a:hover,
body .kc_tabs_nav > .ui-tabs-active > a,
body .kc_tabs_nav > li > a:hover,
.message-actions a:not(.disabled):hover,
.profile-advert .action a:nth-child(1):hover,
.profile-advert .action a:nth-child(4):hover,
.status.live
{
	background: <?php echo esc_html( $main_color ) ?>;
}

@media (min-width: 1025px){
	.submit-btn,
	.submit-btn:focus,
	.submit-btn:active{
		background: <?php echo esc_html( $main_color ) ?>;
		color: <?php echo esc_html( $main_color_font ) ?>;
	}

	.submit-btn:hover{
		background: <?php echo esc_html( $main_color_hover ) ?>;
		color: <?php echo esc_html( $main_color_font_hover ) ?>;
	}
}

@media (max-width: 1024px){
	.submit-btn,
	.submit-btn:focus,
	.submit-btn:active{
		color: <?php echo esc_html( $main_color ) ?>;
	}
}

.af-button,
input[type="submit"],
.af-button:focus,
.af-button:active,
.pagination > span:not(.dots),
.pagination a.current,
.comment-avatar .icon-user,
.single-price,
.single-price .price,
.single-price .price span:not(.price-symbol):not(.text-price),
.kc-search .af-button,
.kc-search .af-button:hover,
.kc-search .af-button:focus,
.kc-search .af-button:active,
body .kc_tabs_nav > .ui-tabs-active > a,
body .kc_tabs_nav > .ui-tabs-active > a:hover,
body .kc_tabs_nav > .ui-tabs-active > a,
body .kc_tabs_nav > li > a:hover,
.message-actions a:not(.disabled):hover,
.profile-advert .action a:nth-child(1):hover,
.profile-advert .action a:nth-child(4):hover,
.af-interactive-slider a:hover
{
	color: <?php echo esc_html( $main_color_font ) ?>;
}

.af-button:hover,
.af-button.af-secondary:hover,
input[type="submit"]:hover,
.pagination a:hover,
.author-address i,
.kc-search .af-button:hover,
.bidding-history:hover,
.af-interactive-slider a:hover
{
	background: <?php echo esc_html( $main_color_hover ) ?>;
}

.element-categories-list svg,
.element-categories-v-list a:hover svg,
.element-categories-transparent-wrap svg,
.element-categories-table svg,
.widget_adifier_advert_categories a:hover svg,
.header-cats a:hover svg{
	fill: <?php echo esc_html( $main_color_hover ) ?>;
}


.af-button:hover,
.af-button.af-secondary:hover,
input[type="submit"]:hover,
.pagination a:hover,
.author-address i,
.kc-search .af-button:hover,
.bidding-history:hover,
.af-interactive-slider a:hover
{
	color: <?php echo esc_html( $main_color_font_hover ) ?>;
}

.modal-header a:hover,
.profile-advert-cats a:hover
{
	color: <?php echo esc_html( $main_color_hover ) ?>;
}


.header-search > a,
.header-search > a:hover,
.header-search > a:focus,
.af-button.af-cta
{
	background: <?php echo esc_html( $search_btn_bg ) ?>;
	color: <?php echo esc_html( $search_btn_font ) ?>;
}

.header-search > a:hover,
.af-button.af-cta:hover{
	background: <?php echo esc_html( $search_btn_bg_hover ) ?>;
	color: <?php echo esc_html( $search_btn_font_hover ) ?>;	
}

<?php
if( !empty( $logo_width ) ){
	?>
	.logo{
		width: <?php echo esc_html( $logo_width ) ?>;
	}
	<?php
}
if( !empty( $logo_height ) ){
	?>
	.logo{
		height: <?php echo esc_html( $logo_height ) ?>;
	}
	<?php
}
?>

.page-title{
	background-color: <?php echo esc_html( $breadcrumbs_bg_color ) ?>;
	background-image: url(<?php echo esc_url( $breadcrumbs_image_bg['url'] ) ?>);
}

.page-title,
.page-title h1,
.page-title a,
.breadcrumbs{
	color: <?php echo esc_html( $breadcrumbs_font_color ) ?>;
}

.bottom-advert-meta .price{
	color: <?php echo esc_html( $price_color ) ?>;
}

.bottom-sidebar-wrap{
	background: <?php echo esc_html( $footer_bg_color ); ?>;
}

.bottom-sidebar-wrap,
.bottom-sidebar-wrap a,
.bottom-sidebar-wrap a:hover,
.bottom-sidebar-wrap a:focus{
	color: <?php echo esc_html( $footer_font_color ); ?>;	
}

.bottom-sidebar-wrap .widget .white-block-title h5,
.bottom-sidebar-wrap a:hover{
	color: <?php echo esc_html( $footer_active_color ); ?>;
}

.price-table-price{
	background: <?php echo esc_html( $pt_price_bg_color ) ?>;
	color: <?php echo esc_html( $pt_price_font_color ) ?>;
}

.price-table-title h5{
	background: <?php echo esc_html( $pt_title_bg_color ) ?>;
	color: <?php echo esc_html( $pt_title_font_color ) ?>;	
}

.price-table-element .af-button:focus,
.price-table-element .af-button:active,
.price-table-element .af-button{
	background: <?php echo esc_html( $pt_btn_bg_color ) ?>;
	color: <?php echo esc_html( $pt_btn_font_color ) ?>;	
}

.price-table-element .af-button:hover{
	background: <?php echo  esc_html( $pt_btn_bg_color_hover ) ?>;
	color: <?php echo esc_html( $pt_btn_font_color_hover ) ?>;
}


.active-price-table .price-table-price{
	background: <?php echo esc_html( $pt_ac_price_bg_color ) ?>;
	color: <?php echo esc_html( $pt_ac_price_font_color ) ?>;		
}

.active-price-table .price-table-title h5{
	background: <?php echo esc_html( $pt_ac_title_bg_color ) ?>;
	color: <?php echo esc_html( $pt_ac_title_font_color ) ?>;			
}

.active-price-table.price-table-element .af-button:focus,
.active-price-table.price-table-element .af-button:active,
.active-price-table.price-table-element .af-button{
	background: <?php echo  esc_html( $pt_ac_btn_bg_color ) ?>;
	color: <?php echo esc_html( $pt_ac_btn_font_color ) ?>;		
}

.active-price-table.price-table-element .af-button:hover{
	background: <?php echo esc_html( $pt_ac_btn_bg_color_hover ) ?>;
	color: <?php echo esc_html( $pt_ac_btn_font_color_hover ) ?>;
}

.copyrights{
	background: <?php echo esc_html( $copyrights_bg_color ) ?>;
}

.copyrights div,
.copyrights a,
.copyrights a:hover,
.copyrights a:focus{
	color: <?php echo esc_html( $copyrights_font_color ) ?>;
}

.copyrights a:hover{
	color: <?php echo esc_html( $copyrights_active_color ) ?>;	
}

.subscription-footer{
	background: <?php echo esc_html( $subscription_bg_color ); ?>
}

.subscription-footer,
.subscription-footer h4{
	color: <?php echo esc_html( $subscription_font_color ); ?>
}

.subscription-footer .submit-ajax-form,
.subscription-footer .submit-ajax-form:hover,
.subscription-footer .submit-ajax-form:active,
.subscription-footer .submit-ajax-form:visited{
	background: <?php echo esc_html( $subscription_font_color ); ?>;
	color: <?php echo esc_html( $subscription_bg_color ); ?>;
}

.subscription-footer input{
	border-color: <?php echo esc_html( $subscription_font_color ); ?>;
}

/* CTAs */
.reveal-phone,
.reveal-phone:focus,
.reveal-phone:hover{
	color: <?php echo esc_html( $contact_phone_font_color ) ?>;
	background: <?php echo esc_html( $contact_phone_bg_color ) ?>;
}

.reveal-phone i{
	background: <?php echo esc_html( $contact_phone_icon_bg_color ) ?>;
}

.contact-seller,
.contact-seller:focus,
.contact-seller:hover,
.bidding-history,
.bidding-history:focus,
.phone-code-send-again,
.phone-code-send-again:focus{
	color: <?php echo esc_html( $contact_msg_font_color ) ?>;
	background: <?php echo esc_html( $contact_msg_bg_color ) ?>;
}

<?php if( !function_exists('adifier_create_post_types') ): ?>
	body:not(.page-template-page-tpl_search):not(.page-template-page-tpl_search_map):not(.single-advert) .page-title{
		padding-bottom: 40px;
	}
}
<?php endif; ?>

@media (min-width: 415px){
	.header-3 .account-btn,
	.header-3 .account-btn:focus,
	.header-3 .account-btn:active,
	.header-3 .submit-btn,
	.header-3 .submit-btn:focus,
	.header-3 .submit-btn:active,
	.header-3 .small-sidebar-open,
	.header-3 .small-sidebar-open:focus,
	.header-3 .small-sidebar-open:active{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
	}
}

.header-2.sticky-header.header-3:not(.sticky-nav){
	background: <?php echo  adifier_hex2rgba( $dark_nav_bg_color, 0.4 ) ?>;
}

.header-2.sticky-header{
	background: <?php echo esc_html( $dark_nav_bg_color ) ?>;
}

@media (min-width: 1025px){

	.header-2 .navigation > li > a{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
	}

	.header-2 .navigation > li.current-menu-ancestor > a,
	.header-2 .navigation > li.current_page_ancestor > a,
	.header-2 .navigation > li.current_page_ancestor > a:visited,
	.header-2 .navigation > li.current_page_item > a,
	.header-2 .navigation > li.current_page_item > a:visited,
	.header-2 .navigation > li.current-menu-item > a,
	.header-2 .navigation > li.current-menu-item > a:visited,
	.header-2 .navigation > li > a:hover{
		color: <?php echo esc_html( $dark_nav_font_color_active ) ?>;
	}

	.header-2.sticky-header .account-btn,
	.header-2.sticky-header .account-btn:focus,
	.header-2.sticky-header .account-btn:active{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
	}

	.header-2.sticky-header:not(.header-3) .submit-btn,
	.header-2.sticky-header:not(.header-3) .submit-btn:focus,
	.header-2.sticky-header:not(.header-3) .submit-btn:active{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
		border: 2px solid <?php echo esc_html( $dark_nav_font_color ) ?>;
	}

	.header-2.sticky-header:not(.header-3) .submit-btn:hover{
		color: <?php echo esc_html( $dark_nav_font_color_active ) ?>;
	}
}

.header-5 .navigation-wrap,
body > header.header-5 .special-nav,
.header-5{
	background: <?php echo esc_html( $dark_nav_bg_color ) ?>;
}

.header-5 .navigation > li > a{
	color: <?php echo esc_html( $dark_nav_font_color ) ?>;
}

@media (max-width: 1024px){
	.header-5 .navigation > li  a{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
	}
}

.header-5 .navigation > li.current-menu-ancestor > a,
.header-5 .navigation > li.current_page_ancestor > a,
.header-5 .navigation > li.current_page_ancestor > a:visited,
.header-5 .navigation > li.current_page_item > a,
.header-5 .navigation > li.current_page_item > a:visited,
.header-5 .navigation > li.current-menu-item > a,
.header-5 .navigation > li.current-menu-item > a:visited,
.header-5 .navigation > li > a:hover{
	color: <?php echo esc_html( $dark_nav_font_color_active ) ?>;
}

.header-5.sticky-header .special-nav .show-on-414 ,
.header-5.sticky-header .special-nav .show-on-414 a:focus,
.header-5.sticky-header .special-nav .show-on-414 a:active,
.header-5.sticky-header .account-btn,
.header-5.sticky-header .account-btn:focus,
.header-5.sticky-header .account-btn:active,
.header-5.sticky-header .small-sidebar-open,
.header-5.sticky-header .small-sidebar-open:focus,
.header-5.sticky-header .small-sidebar-open:hover,
.header-5.sticky-header .submit-btn,
.header-5.sticky-header .submit-btn:focus,
.header-5.sticky-header .submit-btn:active{
	color: <?php echo esc_html( $dark_nav_font_color ) ?>;
	background: transparent;
}

@media (min-width: 1025px){
	.header-5.sticky-header:not(.header-3) .submit-btn,
	.header-5.sticky-header:not(.header-3) .submit-btn:focus,
	.header-5.sticky-header:not(.header-3) .submit-btn:active{
		color: <?php echo esc_html( $dark_nav_font_color ) ?>;
		border: 2px solid <?php echo  esc_html( $dark_nav_font_color ) ?>;
	}
}


<?php echo wp_strip_all_tags( $custom_css ); ?>