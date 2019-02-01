(function( $ ) {
	'use strict';

 $('form#test').submit( function (e) {

 	 	console.log('Saved');

 	 	window.location.replace("/wp-admin/options.php");



 	//e.preventDefault();

 	console.log('Saved');

 // 	e.preventDefault();

	// $("#test :input").each(function(){
 // 		var input = $(this);
 // 		alert(input);
 // 		// This is the jquery object of the input, do what you will
	// });

});


	//$( window ).on('load',function() {

	  // $('#test').submit( function () {
	  //   alert('Options saved');
	  //       return false;
	  //   });
	//});
})( jQuery );
