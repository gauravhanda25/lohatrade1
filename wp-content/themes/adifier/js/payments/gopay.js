jQuery(document).ready(function($){
	/* PAY WITH GoPay */
	$(document).on( 'click', '#gopay-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'gopay_create_payment',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val()
			},
			dataType: 'JSON',
			success: function(response){
				if( response.gw_url ){
					window.location = response.gw_url;
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

	if( window.location.href.indexOf( 'gopay_return' ) > -1 ){
		var res = {
			message: $('#gopay-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}

});