jQuery(document).ready(function($){
	"use strict";

    window.makecommerce_complete = function(data)
    {   
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'makecommerce_execute_payment',
				data: jQuery.parseJSON( data.json )
			},
			method: 'POST',
			dataType: "JSON",
			success: function( res ){
				if( !res.error ){
					$(document).trigger( 'adifier_payment_completed', [res] );
				}
				else{
					alert( res.error );
				}
				
			},
			complete: function(){
				$('.purchase-loader').hide();
			}
		});
    }

    window.makecommerce_cancel = function(){
    	$('.purchase-loader').hide();
    }

	$(document).on( 'click', '#makecommerce-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			data:{
				action: 'makecommerce_create_payment',
				order: $('#purchase textarea').val()
			},
			method: 'POST',
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
				    window.Maksekeskus.Checkout.initialize( response );
				    window.Maksekeskus.Checkout.open();
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