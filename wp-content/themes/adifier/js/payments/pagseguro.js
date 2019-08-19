jQuery(document).ready(function($){
	"use strict";

	var order_id;

	$(document).on( 'click', '#pagseguro-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'pagseguro_create_payment',
				order: $('#purchase textarea').val()
			},
			method: 'POST',
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
					order_id = response.order_id;
					PagSeguroLightbox(
						{
					    	code: response.code
					    }, 
					    {
					    	success : function( transactionCode ) {
								$.ajax({
									url: adifier_data.ajaxurl,
									method: 'POST',
									dataType: "JSON",
									data: {
										action: 			'pagseguro_execute_payment',
										order_id: 			order_id,
										transaction_id: 	transactionCode,
									},
									success: function( res ){
										$(document).trigger( 'adifier_payment_completed', [res] );
									},
									complete: function(){
										$('.purchase-loader').hide();
									}
								});
					    	},
					    	abort : function() {
					    	}
						}
					);
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