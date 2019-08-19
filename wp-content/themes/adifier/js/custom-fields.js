;(function ($, window, document, undefined) {

    function ACF (element) {
        this.element = element;
        this.$element = $(this.element);
        this.init();
    }

    $(document).on('click','.cf-multiple-color-reset', function(){
    	var $parent = $(this).parent();
    	if( $parent.is(':last-child:first-child') ){
    		$parent.find('.wp-picker-clear').trigger('click');
    	}
    	else{
    		$parent.remove();
    	}
    });


    $(document).on('click','.cf-another-color', function(){
    	var $parent = $(this).parents('.cf-field');
    	var input = $parent.find('.cf-multiple-color-item:last-child input[class*="cf-colorpicker"]').clone();
    	input.val('');
    	var reset = $parent.find('.cf-multiple-color-item:last-child .cf-multiple-color-reset').clone();
    	$parent.find('.cf-multiple-color-item:last-child').after('<div class="flex-wrap cf-multiple-color-item"></div>');
    	$parent.find('.cf-multiple-color-item:last-child').append( input );
    	$parent.find('.cf-multiple-color-item:last-child').append( reset );
    	$parent.find('.cf-multiple-color-item:last-child input').wpColorPicker();
    });

    $.extend(ACF.prototype, {
        init: function () {
        	var obj = this;
        	var domElement = this.$element;
        	this.noTermsHolder = domElement.find('.cf-no-terms-wrap');
        	this.noTermsInputs = domElement.find('input[type="checkbox"], input[type="text"]');

			domElement.on('change', '.cf-no-terms input', function(){
				
				if( $(this).prop('checked') === false ){
					obj.noTermsInputs.val('').prop('checked', false);
					obj.noTermsHolder.hide();
					domElement.find('.cf-nested-hidden').hide();
					if( !domElement.hasClass('type_5') ){
						domElement.find('.cf-values-wrap input:not(.no-terms),.cf-values-wrap  select').prop('disabled', false).trigger('change');
					}
				}
				else{
					if( !domElement.hasClass('type_5') ){
						domElement.find('.cf-values-wrap  input:not(.no-terms-check),.cf-values-wrap  select').val('').prop('checked', false).prop('disabled', true).trigger('change');
					}
					obj.noTermsHolder.show();
				}
			});

			if( domElement.hasClass( 'type_3' ) ){
				domElement.find( ".cf-datepicker").datetimepicker({
					dateFormat: 'mm/dd/yy',
					showTimepicker: false
				});           	
			}
			else if( domElement.hasClass('type_5') ){

				domElement.on('change', 'select', function(){
					var $this = $(this);
					var $parent = $this.parents('.nested-field-wrap');
					var val = $this.val();
					var depth = $this.data('depth');
					var maxdepth = $this.data('maxdepth');
					var fieldid = $this.data('fieldid');
					for( var i=depth+1; i<=maxdepth; i++ ){
						domElement.find('.cf-nested.depth_'+i).remove();
					}
					domElement.find('.cf-new-hierarchy-label').hide();
					if( depth < maxdepth && val ){
						$.ajax({
							url: ajaxurl ? ajaxurl : adifier_data.ajaxurl,
							method: 'POST',
							data:{
								action: 'adifier_get_subfield',
								field_id: fieldid,
								value: val,
								depth: depth + 1
							},
							success: function( response ){
								$parent.after( response );
								$(document).trigger('adifier_loaded_nested');
							}
						});
					}

				});


				domElement.on('change', '.cf-no-terms input', function(){
					var $this = $(this);
					var check = $this.prop('checked');
					var depth = $this.data('depth');
					var maxdepth = $this.data('maxdepth');
					var $nested;
					domElement.find('select[data-depth="'+depth+'"]').val('').prop('disabled', check).trigger('change');
					for( var i=depth; i<=maxdepth; i++ ){
						if( check ){
							$nested = domElement.find('.cf-nested.depth_'+(i+1));
							$nested.hide();
							$nested.find('input,select').prop('checked', false).val('').prop('disabled', false).trigger('change');
							domElement.find('.cf-new-hierarchy-label.depth_'+i).show();
						}
						else{
							domElement.find('.cf-new-hierarchy-label.depth_'+i).hide();	
						}
					}
				});
			}
			else if( domElement.hasClass('type_6') ){
				domElement.find('.cf-colorpicker').wpColorPicker();
			}
			else if( domElement.hasClass('type_7') ){
				domElement.find('.cf-colorpicker').wpColorPicker();
			}
        },
    });

    $.fn.adifierCustomFields = function () {
        this.each(function() {
            new ACF( this );
        });
        return this;
    };
})( jQuery, window, document );