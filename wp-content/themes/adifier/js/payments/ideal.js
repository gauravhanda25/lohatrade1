jQuery(document).ready(function($){
	/* PAY WITH IDEAL */
	$(document).on( 'click', '#ideal-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'ideal_create_payment',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.paymentUrl !== 'undefined' ){
					window.location.href = response.paymentUrl;
				}
				else{
					alert( response.error );
				}
			}
		})
	});

	if( window.location.hash && window.location.hash == '#ideal-return' ){
		var res = {
			message: $('#ideal-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});