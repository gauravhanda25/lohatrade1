jQuery(document).ready(function($){
	"use strict";
	var isRTL = $('body').hasClass('rtl') ? true : false;
    /* AUTHOR REVIEWS */
    function startReviewsSlider(){
        $('.author-reviews .owl-carousel').owlCarousel({
            responsive:{
                0: {
                    items: 1
                },
                700: {
                    items: 2
                },
            },
            nav: true,
			rtl: isRTL,
            margin: 10,
            stagePadding: 10,
            navText: ['<i class="aficon-angle-left"></i>','<i class="aficon-angle-right"></i>'],
            navElement: "div"
        });
    }
    startReviewsSlider();


    var $reviewsAjax = $('.author-reviews-ajax');
    var author_id = $('.author-reviews').data('author');
    function fetchReviews( filter = '', page = '' ){
        filter = filter ? filter : $('.reviews-filter').val();
        page = page ? page : 1;
        $reviewsAjax.css('opacity', '0.5');
        $.ajax({
            url: adifier_data.ajaxurl,
            method: 'POST',
            data:{
                action: 'adifier_fetch_ajax_reviews',
                author_id: author_id,
                filter: filter,
                page: page
            },
            success: function( response ){
                $reviewsAjax.html( response );
                startReviewsSlider();
            },
            complete: function(){
                $reviewsAjax.css('opacity', '1');
            }
        })
    }
    $(document).on('change', '.reviews-filter', function(){
        fetchReviews(  $(this).val(), 1 );
    });

    $(document).on('click', '.author-reviews .pagination a', function(e){
        e.preventDefault();
        fetchReviews( '', $(this).data('page') );
    });
})