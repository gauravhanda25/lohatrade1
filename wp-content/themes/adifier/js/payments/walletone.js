jQuery(document).ready(function($){
	/* PAY WITH IDEAL */
	$(document).on( 'click', '#walletone-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'walletone_create_payment',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.form !== 'undefined' ){
					$('#walletone-button').after(response.form);
					$('.walletone-form').submit();
				}
				else{
					alert( response.error );
				}
			}
		})
	});

	if( window.location.hash && window.location.hash == '#walletone-return' ){
		var res = {
			message: $('#walletone-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});