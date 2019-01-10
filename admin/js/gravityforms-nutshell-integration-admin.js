(function( $ ) {
	'use strict';

	$( window ).on('load',function() {

	  $('#test').submit( function () {
	    var b =  $(this).serialize();

	    $.post('options.php', b ).error(
	        function() {
	            alert('error');
	        }).success( function() {
	            alert('success');
	        });
	        return false;
	    });

	    $('#toggle-0').click(function (e) {
	    	var _id = e.target.id;
	    	var t = $('#' + e.target.id);
	    	t.innerHTML = 'Off';
	    	t.attr('checked', true);
	    	var num = _id.split('-')[1];
	    	var text = t.closest('table').find('th')[num].innerHTML;
	    	var data = {
	    	 	'option_name': 'form_option_test',
	    	 	'option_value': 'new test'
	    	 }

	    	$.post( 'options.php', data).error(
	                    function() {
	                        alert('error');
	                    }).success( function() {
	                        alert('success');
	                    });
	                   return false;
	           });
		});
})( jQuery );
