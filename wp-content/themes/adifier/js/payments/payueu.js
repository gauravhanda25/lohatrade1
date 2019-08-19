jQuery(document).ready(function($){
	/* PAY WITH IDEAL */
	$(document).on( 'click', '#payueu-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'payueu_create_payment',
				responseUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.error == 'undefined' ){
					window.location.href = response.redirectUri;
				}
				else{
					alert( response.error );
				}
			}
		})
	});

	if( window.location.hash && window.location.hash.indexOf( '#payueu-return' ) > -1 ){
		var res = {
			message: $('#payueu-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});