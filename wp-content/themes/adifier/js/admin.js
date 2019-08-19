jQuery(document).ready(function($){
	"use strict";


	/* on new post categopries are first while on edit they are second */
	var catEQ = typeof adminpage !== 'undefined' && adminpage == 'post-new-php' ? 1 : 2;

	/* display number of not paid orders */
	if( adifier_admin_order ){
		if( adifier_admin_order['order-count'] > 0 ){
			$('#menu-posts-ad-order .wp-menu-name').html( $('#menu-posts-ad-order .wp-menu-name').text()+'<span class="update-plugins" style="margin-left: 5px; display: inline-block;"><span class="plugin-count">'+adifier_admin_order['order-count']+'</span></span>' );
		}
	}

	function get_advanced_data(){
		var terms = [];
		$('#advert-categorychecklist input, .components-panel__body:eq('+catEQ+') input').each(function(){
			if( $(this).prop( 'checked' ) ){
				terms.push( $(this).val() );
			}
		});

		$('#advert-categorydiv h2 span, .components-panel__body:eq('+catEQ+') .components-panel__body-toggle').before('<span class="adifier-fetch">[Fetching...] </span>');
		$('#advert-categorychecklist input, .components-panel__body:eq('+catEQ+') input').prop('disabled', true);
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'adifier_get_cf',
				terms: terms,
				post_id: $('#post_ID').val()
			},
			success: function(response){
				$('.adifier-advert-custom-fields').html( response );
				$('.adifier-fetch').remove();
				$('#advert-categorychecklist input, .components-panel__body:eq('+catEQ+') input').prop('disabled', false);

				$('.cf-field').adifierCustomFields();
			}
		});
	}

    $(document).on('click', '.custom-fields-table a.delete', function(e){
    	var $this = $(this);
    	if( window.confirm( $this.data('confirm') ) ){
    		return true;
    	}
    	else{
    		return false;
    	}
    });

    $(window).load(function(){
    	if( !$('.components-panel__body:eq('+catEQ+')').hasClass('is-opened') ){
    		$('.components-panel__body:eq('+catEQ+') .components-panel__body-toggle').trigger('click');
    	}
    });

    var fetchCategoryComplete = false;
	if( typeof pagenow !== 'undefined' && pagenow == 'advert' ){
		if( $('.components-panel__body').length == 0 ){
			get_advanced_data();
		}
		const constantMock = window.fetch;
		 window.fetch = function() {
		    return new Promise((resolve, reject) => {
		        constantMock.apply(this, arguments)
		            .then((response) => {
		                if(response.url.indexOf("/advert-category") > -1 && response.type != "cors"){
		                	if( $('.components-panel__body:eq('+catEQ+') input').length > 0 && !fetchCategoryComplete ){
		                		fetchCategoryComplete = true;
		                		get_advanced_data();
		                	}
		                }
		                resolve(response);
		            })
		            .catch((error) => {
		                reject(response);
		            })
		    });
		 }
		$(document).on('change', '#advert-categorychecklist input, .components-panel__body:eq('+catEQ+') input', function(){
			get_advanced_data();
		});
	}


	function handle_images( frameArgs, callback ){
		var SM_Frame = wp.media( frameArgs );

		SM_Frame.on( 'select', function() {

			callback( SM_Frame.state().get('selection').toJSON() );
			SM_Frame.close();
		});

		SM_Frame.open();
	}

	/* IMAGE */
	$('.af-image-select').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		var $parent = $this.parents('.af-image-selection');
		var frameArgs = {
			multiple: false,
			title: $this.text()
		};

		handle_images( frameArgs, function( selection ){
			var image = selection[0];
			$parent.find('input').val(image.id);
			$parent.find('.af-image-holder').html('<img src="'+image.url+'" style="width: 150px; height: 150px;">');
		});	
	});

	$('.af-image-remove').on('click', function(e){
		e.preventDefault();
		var $parent = $(this).parents('.af-image-selection');
		$parent.find('input').val('');
		$parent.find('img').remove();
	});


	/* BIDDING HISTORY */
	$(document).on('click', '.bidding-history', function(e){
		e.preventDefault();
		var $this = $(this);
		$this.append('<i class="aficon-circle-notch aficon-spin"></i>');
		$.ajax({
			url: ajaxurl ? ajaxurl : adifier_data.ajaxurl,
			data:{
				action: 'adifier_bid_history',
				advert_id: $this.data('advertid'),
				history_page: $this.data('page'),
				full: true,
				ip: true
			},
			dataType: 'JSON',
			method: 'POST',
			success: function( response ){
				$('.bidding-history-results').append( response.message );
		    	if( response.next_page ){
		    		$this.html( response.btn_text );
		    		$this.data( response.next_page );
		    	}
		    	else{
		    		$this.remove();
		    	}
			}
		})
	});	

	/* DATE PICKER FOR USER PROFILE */
	if( $('.af-subscribe').length > 0 ){
		$('.af-subscribe').datetimepicker({
			showTime: true,
			dateFormat: 'mm/dd/yy',
			timeFormat: 'HH:mm:ss',
			showSecond: true
		});
	}

	/* SORTING OF CUSTOM FIELDS */
	var $cfSortables = $( ".custom-fields-table tbody" );
	if( $cfSortables.length > 0 ){
		$cfSortables.sortable();

		$(document).on('click', '.cf-save-order', function(){
			var list = [];
			var $this = $(this);
			var text = $this.html();

			$('.cf_order').each(function(){
				list.push( $(this).val() );
			});

			$this.html( '<i class="dashicons dashicons-update"></i>' );

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'adifier_save_cf_order',
					list: list,
				},
				success: function(response){
				},
				complete: function(){
					$this.html( text );
				}
			});
		});
	}

	function ifPublished(){
		if( $('.components-notice.is-success').length > 0 && $('#original_post_status').val() !== 'publish'){
			location.reload();
		}
		else{
			setTimeout(function(){
				ifPublished();
			}, 100);
		}
	}
	if( typeof pagenow !== 'undefined' && pagenow == 'advert' && $('.block-editor').length > 0 ){
		$(document).on('click', '.editor-post-publish-button', function(){
			ifPublished();
		});
	}

});