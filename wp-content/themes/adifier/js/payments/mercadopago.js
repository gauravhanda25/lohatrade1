jQuery(document).ready(function($){
	/* PAY WITH GoPay */
	$(document).on( 'click', '#mercadopago-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'mercadopago_create_payment',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( response.url ){
					window.location = response.url;
				}
				else{
					alert( response.error );
				}
			},
			complete: function(){
				$('.purchase-loader').hide();
			}
		})
	});

	if( window.location.href.indexOf( 'mercadopago_return' ) > -1 ){
		var res = {
			message: $('#mercadopago-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}

});