jQuery(document).ready(function($) {
		
	/*
	 * Print button
	 */	 
	$('.banda .button.print').printLink();
	$('.banda .button.print').on('printLinkError', function(event) {
		window.open(event.currentTarget.href);
	});
	
});