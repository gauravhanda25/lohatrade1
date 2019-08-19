jQuery(document).ready(function($){
	"use strict";

	var order_id;

	var handler = StripeCheckout.configure({
	    key: $('#stripe-button').attr('data-pk'),
	    token: function(token) {
	    	$('.purchase-loader').show();
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				dataType: "JSON",
				data: {
					action: 	'stripe_execute_payment',
					order_id: 	order_id,
					token: 		token,
				},
				success: function( res ){
					$(document).trigger( 'adifier_payment_completed', [res] );
				},
				complete: function(){
					$('.purchase-loader').hide();
				}
			});
	    }
	});
	$(document).on( 'click', '#stripe-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'stripe_create_payment',
				order: $('#purchase textarea').val()
			},
			method: 'POST',
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
					order_id = response.order_id;
					handler.open( response );
				}
				else{
					alert( response.error );
				}
			},
			complete: function(){
				$('.purchase-loader').hide();
			}
		});
	});	

	// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});
});