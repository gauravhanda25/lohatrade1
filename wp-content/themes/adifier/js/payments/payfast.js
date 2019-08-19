jQuery(document).ready(function($){
	/* PAY WITH IDEAL */
	$(document).on( 'click', '#payfast-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'payfast_create_payment',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.form !== 'undefined' ){
					$('#payfast-button').after(response.form);
					$('.payfast-form').submit();
				}
				else{
					alert( response.error );
				}
			}
		})
	});

	if( window.location.hash && window.location.hash == '#payfast-return' ){
		var res = {
			message: $('#payfast-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});