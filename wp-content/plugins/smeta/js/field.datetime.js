
/**
 * Date & Time Fields
 */

CMB.addCallbackForClonedField( ['CMB_Date_Field', 'CMB_Time_Field', 'CMB_Date_Timestamp_Field', 'CMB_Datetime_Timestamp_Field' ], function( newT ) {

	// Reinitialize all the datepickers
	newT.find( '.cmb_datepicker' ).each(function () {
		jQuery(this).datetimepicker({ 
			dateFormat: 'mm/dd/yy',
			showTimepicker: false
		});
	});

	// Reinitialize all the timepickers
	newT.find( '.cmb_datepicker' ).each(function () {
		jQuery(this).datetimepicker({ 
			timeFormat: 'HH:mm:ss',
			showDatepicker: false
		});
	});

	// Reinitialize all the timepickers.
	newT.find('.cmb_datetimepicker' ).each(function () {
        jQuery(this).datetimepicker({
        	dateFormat: 'mm/dd/yy',
        	timeFormat: 'HH:mm:ss',
        	hour: 0,
        	minute: 0,
        	second: 0
        });
	});

} );

CMB.addCallbackForInit( function() {

	// Datepicker
	jQuery('.cmb_datepicker').each(function () {
		jQuery(this).datetimepicker({ 
			dateFormat: 'mm/dd/yy',
			showTimepicker: false
		});
	});
	
	// Datepicker
	jQuery('.cmb_timepicker').each(function () {
		jQuery(this).datetimepicker({ 
			timeFormat: 'HH:mm:ss',
			showDatepicker: false
		});
	});

	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	jQuery("#ui-datepicker-div").wrap('<div class="cmb_element" />');

	// Timepicker
	jQuery('.cmb_datetimepicker').each(function () {
        jQuery(this).datetimepicker({
        	dateFormat: 'mm/dd/yy',
        	timeFormat: 'HH:mm:ss',
        	hour: 0,
        	minute: 0,
        	second: 0
        });
	} );

});