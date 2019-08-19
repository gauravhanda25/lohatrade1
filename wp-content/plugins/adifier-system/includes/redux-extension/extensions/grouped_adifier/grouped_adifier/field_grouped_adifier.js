jQuery(document).ready(function($) {
	$(document).on('click', '.grouped-adifier-remove', function(e){
		e.preventDefault();
		var $parent = $(this).parents('fieldset');
		var $thisParent = $(this).parent();

		if( $parent.find('.grouped-adifier-group').length > 1 ){
			$thisParent.remove();
		}
		else{
			$thisParent.find('input').val('');
		}

		updateValue( $parent );
	});

	$(document).on('click', '.grouped-adifier-add', function(e){
		e.preventDefault();
		var $parent = $(this).parents('fieldset');
		var $element = $parent.find('.grouped-adifier-group:last');
		var $clone = $element.clone();
		$clone.find('input').val('');
		$element.after( $clone );		
	});

	$(document).on('keyup', '.grouped-adifier-group input', function(e){
		var $parent = $(this).parents('fieldset');
		updateValue( $parent );
	});

	function updateValue( $parent ){
		var val = [];
		$parent.find('.grouped-adifier-group').each(function(){
			var list = [];
			$(this).find('input').each(function(){
				if( $(this).val() !== '' ){
					list.push( $(this).val() );
				}
			});
			val.push( list.join('|') )
		});
		$parent.find('.grouped-adifier-value').val( val.join('||') );
	}
});
