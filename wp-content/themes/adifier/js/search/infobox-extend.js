InfoBox.prototype.getCloseBoxImg_= function(){
	return '<div class="closeInfoBox"><i class="aficon-times"></i></div>';
};
InfoBox.prototype.addClickHandler_ = function () {
    var closeBox = this.div_.firstChild;
    this.closeListener_ = google.maps.event.addDomListener(closeBox, "click", this.getCloseClickHandler_() );
};
InfoBox.prototype.getCloseClickHandler_ = function () {
	var me = this;
	return function (e) {
		e.cancelBubble = true;
		if (e.stopPropagation) {
			e.stopPropagation();
		}
		google.maps.event.trigger(me, "closeclick");

		jQuery('.infoBox').trigger('mouseleave');
		me.close();
	};
};	