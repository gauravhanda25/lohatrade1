jQuery(document).ready(function($){

	var order;

	$(document).on('click', '.profile-promote-advert', function(e){
		e.preventDefault();

		var $this = $(this);
		var $parent = $this.parents('.profile-advert');
		var parentData = $parent.data();
		var $promotion;
		$('.promotion input[type="checkbox"]').prop('checked', false).prop('disabled', false);
		$('.promotion input[type="radio"]').prop('checked', false).prop('disabled', true);
		$('.promotion').removeClass('disabled').addClass('inactive');
		$('.active-promo').html('');
		for( var key in parentData ){
			if( parentData[key] ){
				$promotion = $('.promotion.promo_'+key);
				$promotion.addClass('disabled');
				$promotion.find('input[type="checkbox"]').prop('disabled', true);
				$promotion.find('.active-promo').html( parentData[key] );
			}
		}

		order = {
			advert_id: $this.data('id')
		};

		$('#promote').modal('show');
	});


	$(document).on('change', '.promotion input[type="checkbox"]', function(e){
		var $this = $(this);
		var $parent = $this.parents('.promotion');
		if( $this.prop('checked') === true ){
			$parent.find('input[type="radio"]').prop('disabled', false);
			$parent.find('.styled-radio:first input').prop('checked', true);
			$parent.removeClass('inactive');
		}
		else{
			$parent.find('input[type="radio"]').prop('checked', false).prop('disabled', true);
			$parent.addClass('inactive');
		}
	});

	$(document).on('click', '.purchase-promotion', function(e){
		e.preventDefault();
		var promotions = {};
		$('.promotion input[type="checkbox"]').each(function(){
			var $this = $(this);
			if( $this.prop('checked') ){
				promotions[$this.val()] = $('input[name="'+$this.val()+'_pack"]:checked').val();
			}
		});
		if( !$.isEmptyObject( promotions ) ){
			$('#promote').modal('hide');
			order.type = 'promotion';
			order.list = promotions;
			purchaseModal();
		}
		else{
			alert( $(this).data('empty') );
		}
	});	

	$(document).on('click', '.purchase-pack', function(e){
		e.preventDefault();
		order = {
			type: 'acc_pay',
			list: $(this).data('pack')
		}
		purchaseModal();
	});

	function purchaseModal(){
		order.userId = $('.order_user').val();
		$('.purchase-response').html('');
		$('#purchase textarea').val( JSON.stringify( order ) );
		$('.payments-list').removeClass('hidden');
		$('#purchase').modal('show');
	}

	$(document).on('adifier_payment_completed', function(e, response){
		if( typeof response.promotion !== 'undefined' ){
			for( var key in response.promotion ){
				$('.advert-'+order.advert_id).data(key.replace('promo_', ''), response.promotion[key]);
			}
		}
		if( typeof response.success !== 'undefined' ){
			$('.purchase-response').html( response.success );
			$('.payments-list').addClass('hidden');
		}
		else{
			$('.purchase-response').html( response.error );
		}
	});

	$(document).on('adifier_payment_return', function(e, response){
		$('.purchase-response').html( '<div class="alert-info">'+response.message+'</div>' );
		$('.payments-list').addClass('hidden');
		$('#purchase').modal('show');
	});
});