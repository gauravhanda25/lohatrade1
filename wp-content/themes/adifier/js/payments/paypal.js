jQuery(document).ready(function($){
	"use strict";

	paypal.Button.render({

	    env: adifier_data.payment_enviroment == "test" ? 'sandbox' : 'production', // sandbox | production

	    // Show the buyer a 'Pay Now' button in the checkout flow
	    commit: true,

	    // payment() is called when the button is clicked
	    payment: function() {
	    	$('.purchase-loader').show();
	    	var data = {
				action: 'paypal_create_payment',
				order: $('#purchase textarea').val()
			};

	        // Make a call to your server to set up the payment
	        return paypal.request.post(adifier_data.ajaxurl, data).then(function(res) {
                $('.purchase-loader').hide();
                return res.paymentID;
            });
	    },

	    // onAuthorize() is called when the buyer approves the payment
	    onAuthorize: function(data, actions) {
	    	$('.purchase-loader').show();
	        // Set up the data you need to pass to your server
	        var data = {
	            paymentID: data.paymentID,
	            payerID: data.payerID,
	            action: 'paypal_execute_payment'
	        };

	        // Make a call to your server to execute the payment
	        return paypal.request.post(adifier_data.ajaxurl, data).then(function (res) {
	        	$('.purchase-loader').hide();
                $(document).trigger( 'adifier_payment_completed', [res] );
            });
	    }

	}, '#paypal-button');	

});