jQuery(document).ready(function($){
	"use strict";

	$(document).on( 'click', '#paystack-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'paystack_create_payment',
				order: $('#purchase textarea').val()
			},
			method: 'POST',
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
					var handler = PaystackPop.setup({
						key: response.key,
						email: response.email,
						amount: response.amount,
						//currency: response.currency,
						callback: function( paystack_response ){
					    	$('.purchase-loader').show();
							$.ajax({
								url: adifier_data.ajaxurl,
								method: 'POST',
								dataType: "JSON",
								data: {
									action 		: 	'paystack_execute_payment',
									order_id 	: 	response.order_id,
									reference 	: 	paystack_response.reference,
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
					handler.openIframe();
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

});