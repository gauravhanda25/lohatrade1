jQuery(document).ready(function($){
	"use strict";

	$('#offline-button').on( 'click', function(e){
		e.preventDefault();
		$('.purchase-loader').show();
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			dataType: 'JSON',
			data:{
				action: 'offline_execute_payment',
				order: $('#purchase textarea').val()
			},
			success: function(res){
				$(document).trigger( 'adifier_payment_completed', [res] );
			},
			complete: function(){
				$('.purchase-loader').hide();
			}
		});
	});

	var fetching = false;
	$('.offline-invoice-modal').on('click', function(){
		if( !fetching ){
			fetching = true;
			var $this = $(this);
			$this.append('<i class="aficon-spin aficon-circle-notch"></i>');
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				data: {
					action: 'adifier_offline_invoice_modal',
					id: $this.data('id')
				},
				success: function(response){
					if( response !== '' ){
						$('#offlineinvoice .modal-body').html(response);
						$('#offlineinvoice').modal('show');
					}
				},
				complete: function(){
					$this.find('i').remove();
					fetching = false;
				}
			});			
		}
	});	
});