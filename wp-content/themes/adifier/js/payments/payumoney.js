jQuery(document).ready(function($){
	"use strict";

	$(document).on( 'click', '#payumoney-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'payumoney_create_payment',
				url: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			method: 'POST',
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
					var ajaxing = false;
					bolt.launch(
						response,
						{
							responseHandler: function(boltResponse){
								if( boltResponse.response.txnStatus == 'SUCCESS' && !ajaxing ){
									ajaxing = true;
									$('.purchase-loader').show();
									$.ajax({
										url: adifier_data.ajaxurl,
										data:{
											action: 'payumoney_execute_payment',
											paymentData: boltResponse.response
										},
										method: 'POST',
										dataType: "JSON",
										success: function(res){
											$(document).trigger( 'adifier_payment_completed', [res] );
										},
										complete: function(){
											ajaxing = false;
											$('.purchase-loader').hide();
										}
									});
								}
							}
						},
						{
							catchException: function(boltResponse){
								alert( boltResponse.message );
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
});