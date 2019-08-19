jQuery(document).ready(function($){
	/* PAY WITH IDEAL */
	$(document).on( 'click', '#payulatam-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'payulatam_create_payment',
				responseUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.form !== 'undefined' ){
					$('#payulatam-button').after(response.form);
					$('.payulatam-form').submit();
				}
				else{
					alert( response.error );
				}
			}
		})
	});

	if( window.location.hash && window.location.hash.indexOf( '#payulatam-return' ) > -1 ){
		var res = {
			message: $('#payulatam-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});