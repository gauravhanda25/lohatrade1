jQuery(document).ready(function($){
	/* MESSAGES */
	var isMobile = false; //initiate as false
	// device detection
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
		isMobile = true;
	}

	var conAjaxing 				= false;
	var sendAjaxing 			= false;

	var messageInterval;
	var $messagesWrap 			= $('.messages-window-wrap');	
	var $messagesListingWrap 	= $('.message-listing-wrap');	
	var $messagesListing 		= $('.message-listing');	
	var $conversationsWrap		= $('.conversations-window-wrap');	
	var $conversationListingWrap= $('.conversations-listing-wrap');	
	var $conversationsListing	= $('.ajax-conversations');	
	var $conversationsPag		= $('.ajax-conversations-pagination');	
	var $conversationsList 		= $('.conversations-list');	
	var $messLoading 			= $('.mess-loading');
	var $authorNoListing 		= $('.author-no-listing');	

	var conInterval;
	var $conForm				= $('.conversation-filter');
	var $keyword 				= $('[name="keyword"]');
	

	var $conId 					= $('input[name="con_id"]');
	var conData					= { con_id: $conId.val(), action: 'adifier_ajax_conversations' };


	/*
	* Adjust height
	*/
	function adjustHeight(){
		var height;
		var conHeight;
		if( $(window).width() > 736 ){
			height = $(window).height() - $('header').height() - $('.messages-header').height() - $('.messages-footer').height() - 180;	
			conHeight = height;
		}
		else{
			height = 350;
			conHeight = 371;
		}
		
		$messagesWrap.height( height );
		height = height - $conversationsPag.height();
		$conversationsWrap.height( conHeight );
	}
	adjustHeight();
	$(window).resize(function(){
		adjustHeight();
	});

	/*
	* Toggle conversations on small screens
	*/
	$(document).on('click', '.toggle-conversations', function(){
		$('.messages-left').addClass( 'open' );
	});

	/*
	* Retrieve ajax conversation list
	*/
	function fetchConversations( callback ){
		if( conAjaxing == false ){
			conData.con_id = $conId.val();
    		conAjaxing = true;
			$.ajax({
				url: adifier_data.ajaxurl,
				data: conData,
				dataType: 'JSON',
				success: function(response){
					updateUnreadCount( response.unread );
					$conversationsListing.html( response.conversations );
					$conversationsPag.html( response.pagination );
					if( callback ){
						callback();
					}
				},
				complete: function(){
					conAjaxing = false;
					startConInterval();
				}
			});
		}
	}

	/*
	*	Start interval for fetching conversations
	*/
	function startConInterval(){
		clearInterval(conInterval);
		conInterval = setInterval(function(){
			fetchConversations();
		}, 10000);
	}

	/*
	* On toggle change
	*/
	$(document).on('click', '.toggle-list', function(){
		var $parent = $(this).parent();
		$('.conversations-list input').prop('checked', !$parent.hasClass('active'));
		$parent.toggleClass('active');
	});

	/*
	* ON pagination click fetc conversation
	*/
	$(document).on('click', '.pagination a', function(e){
		e.preventDefault();
		conData.page = $(this).data('page');
		fetchConversations();
	});

	/*
	* Delete selected conversation
	*/
	$(document).on('click', '.delete-conversations', function(e){
		e.preventDefault();
		if( confirm( $(this).data('confirm') ) ){
			var selected = [];
			$('[name="con_ids[]"]:checked').each(function() {
			    selected.push( $(this).val() );
			});
			if( selected ){

				conData.action = 'adifier_delete_conversations';
				conData.con_ids = selected.join(',');

				fetchConversations(function(){
    				if( selected.indexOf( $conId.val() ) > -1 ){
    					$messagesWrap.addClass('hidden');
    					$authorNoListing.removeClass('hidden');
    					$conId.val('');
    				}
				});
			}
		}
	});

	/*
	* On conversation filter submit
	*/
    $conForm.on('submit', function(e){
    	e.preventDefault();
		conData.keyword = $(this).find('input[name="keyword"]').val();
	    conData.page = 1;
    	fetchConversations();
    });

	/*
	* Common function for fetching messages
	*/
	function fetchMesages( con_id ){
		newCon = con_id ? true : false;
		con_id = con_id ? con_id : $conId.val();
		if( con_id ){
			$.ajax({
				url: adifier_data.ajaxurl,
				method: 'POST',
				data: {
					action: 'adifier_ajax_messages',
					con_id: con_id
				},
				dataType: 'JSON',
				success: function(response){
					if( !$messagesWrap.is(':visible') ){
						$messagesWrap.removeClass('hidden');
						$authorNoListing.addClass('hidden');
					}
					updateUnreadCount( response.unread );
					populateMessages( response.messages, newCon );
					$('.paste-advert-title').html( response.title );
					$('.conversation-review').html( response.review );
					$('.message-form').removeClass('disabled');
					startMessagesInterval();

					$('.messages-left').removeClass( 'open' );
					$('.con-loading-'+con_id).addClass('hidden');
				}
			});
		}
	}

	/*
	* Populating messages window
	*/
	function populateMessages( messages, newCon ){
		newCon = newCon ? newCon : false;
		var oldHeight = $messagesListingWrap.height() / 2;
		var oldPosition = $messagesListingWrap.scrollTop() + oldHeight;
		var oldListingHeight = $messagesListing.height();

		$messagesListing.html(messages);

		if( newCon || ( oldPosition >= ( oldListingHeight - oldHeight) && oldPosition <= oldListingHeight ) ){
			$messagesListingWrap.scrollTop( $messagesListing.prop('scrollHeight') );
		}
	}

	/*
	*	Interval for messages fetching
	*/
	function startMessagesInterval(){
		if( $messagesWrap.is(':visible') ){
			clearInterval(messageInterval);
			messageInterval = setInterval(function(){
				fetchMesages();
			}, 10000);
		}
	}

	/*
	* Update unread badge
	*/
	function updateUnreadCount( unread ){
		$('.unread-badge').remove();
		if( unread !== '' ){
			$('.messages-unread-count').append( unread );
		}
	}

	/*
	* Start scrollbars  fo message window
	*/
	if( $messagesListingWrap.length > 0 ){
		$messagesListingWrap.scrollbar();
		$conversationListingWrap.scrollbar();
		startMessagesInterval();
		startConInterval();
	}

	/*
	* Open messages window for the selected conversation
	*/
	$(document).on('click', '.start-messages', function(e){
		e.preventDefault();
		var $this = $(this);
		var $parent = $this.parent();
		var con_id = $(this).data('con_id');

		$('.paste-advert-title').html( $this.data('advert') );

		$('.conversation-wrap.current').removeClass('current');
		$parent.removeClass('unread').addClass('current');

		$parent.find('.con-loading').removeClass('hidden');
		
		$('.message-form textarea[name="message"]').val('');
		$conId.val(con_id);
		fetchMesages( con_id );
	});

	function sendMessage( $this ){
		var formData = new FormData($this[0]);
		if( $this.find('textarea[name="message"]').val() !== '' ){
			if( !sendAjaxing ){
				sendAjaxing = true;
	    		$.ajax({
	    			url: adifier_data.ajaxurl,
	    			method: 'POST',
	    			data: formData,
	    			dataType: 'JSON',
					processData: false,
					contentType: false,
	    			success: function(response){
	    				$this.find('textarea[name="message"]').val('');
						updateUnreadCount( response.unread );
						populateMessages( response.messages );
						startMessagesInterval();
	    			},
	    			complete: function(){
	    				sendAjaxing = false;
	    			}
	    		});
	    	}
    	}
	}

	/*
	* trgger send on textarea enter only
	*/
	$(document).on('keypress', 'textarea[name="message"]', function(e){
		if(e.which == 13 && !e.shiftKey) {        
		    $(this).closest("form").submit();
		    e.preventDefault();
		    return false;
		}
    });	

	/*
	* Sending message on enter
	*/
	$(document).on('submit', 'form.message-form', function(e){
		e.preventDefault();
		sendMessage( $(this) );
    });	
	

    /* sending message on mobile phones */
    $(document).on('click', '.send-message', function(e){
    	e.preventDefault();
		sendMessage( $('form.message-form') );
    });

    /* REVIEWS */
    $(document).on( 'click', '.launch-review', function(e){
    	e.preventDefault();
    	var $this = $(this);
    	if( !$this.hasClass('disabled') ){
    		$('.modal input[name="con_id"]').val( $this.data('con_id') );
    		$('#write-review').modal('show');
    	}
    });

	$(document).on( 'mouseenter', '.rate-user span', function(){
		var pos = $(this).index();
		var $parent = $(this).parents('.rate-user');
		var icon, is_clicked;
		for( var i=0; i<=pos; i++ ){
			icon = $parent.find('span:eq('+i+')');
			is_clicked = icon.hasClass('clicked') ? 'clicked' : '';
			icon.attr('class', 'aficon-star '+is_clicked );
		}
	});
	$(document).on( 'mouseleave', '.rate-user span', function(){
		$(this).parents('.rate-user').find('span').each(function(){
			if( !$(this).hasClass('clicked') ){
				$(this).attr('class', 'aficon-star-o');
			}
		});
	});

	$(document).on('click', '.rate-user span', function(){
		var value = $(this).index();
		var $parent = $(this).parents('.rate-user');
		$('.rating-value').val( value + 1 );
		$parent.find('span').removeClass('clicked');
		for( var i=0; i<=value; i++ ){
			$parent.find('span:eq('+i+')').attr('class', 'aficon-star').addClass('clicked');
		}
	});
});