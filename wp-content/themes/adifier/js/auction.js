jQuery(document).ready(function($){
	"use strict";
	var bidHistoryTimer = false;
	/* BIDDING RESPONSE */
	$(document).on('bidding-response', function(e, response){
		if( response.price ){
			var $price = $('.single-price-wrap .price');
			$price.after( response.price );
			$price.remove();

			$('input[name="bid"]').attr( 'placeholder', response.min_bid_text );

			$('input[name="bid"]').attr( 'min', response.min_bid ).val('');

			$('input[name="bidpage"]').val(1);
			setTimeout(function(){
				clearTimeout( bidHistoryTimer );
				$('.bidding-history-form').submit();
			}, 100);
		}
	});

	$(document).on('bidding-history-response', function(e, response){
		var $btn = $('.bidding-history');
		if( response.next_page && !$btn.hasClass('bidding-excerpt') ){
			$('.bidding-history').text( response.btn_text );
			$('input[name="history_page"]').val( response.next_page );
			$('.bidding-history').show();
		}
		else{
			$('.bidding-history').hide();
		}
		if( $btn.hasClass('bidding-excerpt') ){
			bidHistoryTimer = setTimeout(function(){
				$('.bidding-history-form').submit();
			}, 10000);
		}
	});

	/* CONTACT BUYER AFTER AUCITON ENDS */
	$(document).on('click', '.contact-buyer', function(e){
		if( $(this).attr('href').indexOf('http') == -1 ){
	    	e.preventDefault();
	    	$('#contact-buyer input[name="buyer_id"]').val( $(this).data('buyer_id') );
	    	$('#contact-buyer').modal('show');
		}
	});
});