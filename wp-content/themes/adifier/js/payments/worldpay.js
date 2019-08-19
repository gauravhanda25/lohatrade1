jQuery(document).ready(function($){
	"use strict";

	var $modal = $('#worldpay .modal-content');
	var modalContent = $modal.html();

	function startWorldpay(){
		Worldpay.useTemplateForm({
			'clientKey'			: $('#worldpay-button').data('clientkey'),
			'form'				: 'worldpay-form',
			'paymentSection'	: 'worldpay-section',
			'display'			: 'inline',
			'reusable'			: false,
			'saveButton'		: false,
			'callback'			: function(obj) {
				if (obj && obj.token) {
					$('.worldpay-loader').show();
					$.ajax({
						url: adifier_data.ajaxurl,
						data:{
							action: 	'worldpay_create_payment',
							order: 		$('#purchase textarea').val(),
							token: 		obj.token
						},
						method: 'POST',
						dataType: "JSON",
						success: function(response){
							if( !response.error ){
								$('#worldpay').modal('hide');
								$(document).trigger( 'adifier_payment_completed', [response] );
								$modal.html( modalContent );
								startWorldpay();
							}
							else{
								alert( response.error );
							}
						},
						complete: function(){
							$('.worldpay-loader').hide();
							$('.purchase-loader').hide();
						}
					});
				}
				else{
					alert( 'Token could not be generated' );
				}
			}
		});
	}

	startWorldpay();

	$(document).on('click', '#worldpay-button', function(){
		$('#worldpay').modal('show');
	});
});