jQuery(document).ready(function($){
	"use strict";

	$(document).on( 'click', '.open-reponse-form', function(e){
		e.preventDefault();
		$($(this).data('target')).toggleClass('hidden');
	});

	$(document).on( 'click', '.send-response', function(e){
		e.preventDefault();
		var $this = $(this);
		var $parent = $this.parents('.send-response-wrap');
		$this.append('<i class="aficon-circle-notch aficon-spin"></i>');

		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data: $parent.find('form').serialize(),
			dataType: "JSON",
			success: function(response){
				if( !response.error ){
					$parent.before( response.success );
					$parent.remove();
				}
				else{
					$parent.find('.response-result').html( response.error );
				}
			},
			complete: function(){
				$this.find('i').remove();
			}
		})
	});

	/* TOGGLE REVIEW deTAils */
	$(document).on('click', '.toggle-review-details', function(){
		var $this = $(this);
		$this.toggleClass('open')
		var $parent = $this.parents('.user-review');
		$parent.find('.review-details').slideToggle();
	});	
});