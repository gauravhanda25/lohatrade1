jQuery(document).ready(function($){
	
	$(document).on( 'click', '#payhere-button', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'show_account_modal'
			},
			success: function(response){
				$('#payhere .modal-body').html(response);
				$('#purchase').modal('hide');
				$('#payhere').modal('show');
			},
			complete: function(){
				$('.purchase-loader').hide();
			}
		});
	});

	$(document).on('submit', '#payhere-form', function(e){
		e.preventDefault();
		$('.worldpay-loader').show();
		var formRawData = $(this).serializeArray();
		var formData = {};
		$.each(formRawData, function(i, field){
		    formData[field.name] = field.value;
		});
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: {
				action: 'verify_account_modal',
				redirectUrl: window.location.href.split("#")[0],
				order: $('#purchase textarea').val(),
				data: formData
			},
			dataType: 'JSON',
			success: function(response){
				if( typeof response.error == 'undefined' ){
					$('#payhere-button').after(response.form);
					$('.payhere-form').submit();
				}
				else{
					alert( response.error );
				}
			},
			complete: function(){
				$('.worldpay-loader').hide();
			}
		});
	});

	if( window.location.hash && window.location.hash == '#payhere-return' ){
		var res = {
			message: $('#payhere-button').data('returnmessage')
		};

		$(document).trigger('adifier_payment_return', [res]);
	}
});