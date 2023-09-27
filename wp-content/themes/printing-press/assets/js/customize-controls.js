( function( api ) {

	// Extends our custom "printing-press" section.
	api.sectionConstructor['printing-press'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );