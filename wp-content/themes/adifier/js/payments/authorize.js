function authorizeNetResponse(response) {
	jQuery(document).trigger( 'authorizeNetResponse', [response] );
}
jQuery(document).ready(function($){
	"use strict";

	$(document).on( 'authorizeNetResponse', function(e, response){
	    if (response.messages.resultCode === "Error") {
	        var errorMessages = [];
	        for( var key in response.messages.message ){
	        	errorMessages.push( response.messages.message[key].code+': '+response.messages.message[key].text );
	        }
	        alert( errorMessages.join( ' | ' ) );
	    }
	    else{
	    	$('.purchase-loader').show();
	    	$.ajax({
	    		url: adifier_data.ajaxurl,
	    		method: 'POST',
	    		data: {
	    			action: 'authorize_execute_payment',
	    			order: $('#purchase textarea').val(),
	    			buyerData: response.opaqueData
	    		},
	    		dataType: 'JSON',
	    		success: function(res){
	    			$(document).trigger( 'adifier_payment_completed', [res] );
	    		},
	    		complete: function(){
	    			$('.purchase-loader').hide();
	    		}
	    	})
	    }
	});	
});