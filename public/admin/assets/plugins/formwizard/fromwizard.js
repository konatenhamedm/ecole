(function($) {
	"use strict";
	
	// Toolbar extra buttons
	var btnFinish1 = $('<button></button>').text('Finir')
		.addClass('btn btn-secondary')
	var btnFinish = $('<a href=""></a>').text('Finir')
		.addClass('btn btn-primary')
		.on('click', function(){ alert('Finish Clicked'); });
	/*var btnCancel = $('<button></button>').text('Cancel')
		.addClass('btn btn-secondary')*/
	/*	.on('click', function(){ $('#smartwizard-3').smartWizard("reset"); });
*/

	// Smart Wizard
	$('#smartwizard').smartWizard({
			selected: 0,
			theme: 'default',
			transitionEffect:'fade',
			showStepURLhash: true,
			toolbarSettings: {
							  toolbarButtonPosition: 'end',
							  toolbarExtraButtons: []
							}
	});
		
	// Arrows Smart Wizard 1
	$('#smartwizard-1').smartWizard({
			selected: 0,
			theme: 'arrows',
			transitionEffect:'fade',
			showStepURLhash: false,
			toolbarSettings: {
							  toolbarExtraButtons: [btnFinish]
							},

	});

			
	// Circles Smart Wizard 1
	$('#smartwizard-2').smartWizard({
			selected: 0,
			theme: 'circles',
			transitionEffect:'fade',
			showStepURLhash: false,
			toolbarSettings: {
							  toolbarExtraButtons: [btnFinish1]
							}
	});
			
	// Dots Smart Wizard 1
	$('#smartwizard-3').smartWizard({
			selected: 0,
			theme: 'dots',
			transitionEffect:'fade',
			showStepURLhash: false,
			toolbarSettings: {
							  toolbarExtraButtons: []
							}
	});
	refresh();
	function refresh() {
		let index = 0
		$('.btn-toolbar').each(function () {
			$(this).addClass('data-numberKey')
			$('.btn-toolbar1').hide();
			/* $(this).find('.numero:first').val(index);*/
		})
	}
})(jQuery);