jQuery(document).ready(function($){
	"use strict";

	function checkout(data) {
	    paysafe.checkout.setup(data.base64key, data.order, function(instance, error, result) {
		    if (result && result.token) {
		    	$('.purchase-loader').show();
				$.ajax({
					url: adifier_data.ajaxurl,
					data:{
						action			: 'paysafe_execute_payment',
						token			: result.token,
						order_id		: data.order.order_id,
						paymentMethod	: result.paymentMethod,
						zip				: data.zip
					},
					method: 'POST',
					dataType: "JSON",
					success: function(response){
						if( !response.error ){
							$(document).trigger( 'adifier_payment_completed', [response] );
						}
						else{
							alert( response.error );
						}
					},
					complete: function(){
						if( instance.isOpen() ){
							instance.close();
						}						
						$('.purchase-loader').hide();
					}
				});
                 
		    } 
		    else {
		     	alert( JSON.stringify(error) );
		    }        
	    });
	}

	$(document).on('click', '#paysafe-button', function(){
		var zip = prompt(adifier_paysafe.zip);		
		if (zip != null) {
			$('.purchase-loader').show();
			$.ajax({
				url: adifier_data.ajaxurl,
				data:{
					action: 	'paysafe_create_payment',
					order: 		$('#purchase textarea').val()
				},
				method: 'POST',
				dataType: "JSON",
				success: function(response){
					if( !response.error ){
						response.zip = zip;
						checkout( response );
					}
					else{
						alert( response.error );
					}
				},
				complete: function(){
					$('.purchase-loader').hide();
				}
			});
		}
	});
});