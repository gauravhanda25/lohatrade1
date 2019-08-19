jQuery(document).ready(function($){
    "use strict";

    var isRTL = $('body').hasClass('rtl') ? true : false;
    var isLoggedIn = $('body').hasClass('logged-in') ? true : false;
    var $slider = $('.single-slider');
    var $sliderThumbs = $('.single-slider-thumbs');

    $(window).load(function(){
        $slider.owlCarousel({
            items: 1,
            video: true,
            autoHeight:true,
            rtl: isRTL,
            videoHeight: ( ( $slider.width() - 30 ) * 450 ) / 750,
            nav: true,
            navText: ['<i class="aficon-angle-left"></i>','<i class="aficon-angle-right"></i>'],   
            navElement: "div",     
            videoWidth: $slider.width() - 30
        }).on('changed.owl.carousel', function (e) {
            $sliderThumbs.trigger('to.owl.carousel', [e.item.index, 300, true]);
            sliderChangeActive( e.item.index );
        });

        $sliderThumbs.owlCarousel({
            responsive: {
                0: {
                    items: 4
                },
                200: {
                    items: 5
                },
                400: {
                    items: 6
                },
                600: {
                    items: 7
                },
                800: {
                    items: 8
                }
            },        
            margin: 5,
            rtl: isRTL,
            nav: true,
            navText: ['<i class="aficon-angle-left"></i>','<i class="aficon-angle-right"></i>'],
            navElement: "div",
            onInitialized: function(){
                var $stage = $('.single-slider-thumbs .owl-stage');
                $stage.css('width', $stage.width() + 1);
            },
            onResized: function(){
                var $stage = $('.single-slider-thumbs .owl-stage');
                $stage.css('width', $stage.width() + 1);
            }
        }).on('click', '.owl-item', function () {
            $slider.trigger('to.owl.carousel', [$(this).index(), 300, true]);
            sliderChangeActive( $(this).index() );
        }).on('changed.owl.carousel', function (e) {
            $slider.trigger('to.owl.carousel', [e.item.index, 300, true]);
            sliderChangeActive( e.item.index );
        });
    });

    function sliderChangeActive( item ){
        $('.single-thumb-item.active').removeClass('active');
        $sliderThumbs.find('.owl-item:eq('+item+') .single-thumb-item').addClass('active');
        $(this).index()
    }

    /* MAGNIFIC POPUP FOR THE GALLERY */
    var items = [];
    var $imageLinks = $('.single-slider-href');
    $imageLinks.each(function () {
        var $this = $(this);
        if ($this.hasClass('owl-video')) {
            items.push({
                src: $(this).attr('href'),
                type: 'iframe',
            });
        }
        else {
            items.push({
                src: $(this).attr('href'),
                type: 'image',
            });
        }
    });
    
    $imageLinks.magnificPopup({
        mainClass: 'mfp-fade',
        items: items,
        type: 'image',
        gallery: {
            enabled: true,
            tCounter: '%curr% / %total%'
        },
        callbacks: {
            beforeOpen: function () {
                var index = $imageLinks.index(this.st.el);
                if (-1 !== index) {
                    this.goTo(index);
                }
            },
        }
    }); 

    /* LAUNCH SINGLE MODALS */
    $('.report-advert').on('click', function(e){
        e.preventDefault();
        $('#report-advert').modal('show');
    });

    $(document).on('adifier_contact_seller_modal', function(e, res){
        if( typeof res.con_id !== 'undefined' ){
            $('input[name="con_id"]').val( res.con_id );
        }
    }); 

    /* COUNTDOWN */
    var $countdown = $('.countdown');
    if( $countdown.length > 0 ){
        $('.countdown').kkcountdown({
            dayText             : $countdown.data('single'),
            daysText            : $countdown.data('multiple'),
            displayZeroDays     : true,
            rusNumbers          : false
        });
    }

    $('#lcf_register').on('change', function(){
        $('.lcf-toggle').toggleClass('hidden');
    });

    
});