(function( $ ) {
	'use strict';


	 $( window ).load(function() {  //Whitelist | Blacklist save button visibility handler
		var button = jQuery("#saveLists");
		jQuery("#whiteListTXT").on('input',function(e){
			//   if(e.target.value === ''){
			// 	button.hide();
			//   } else {
				button.show();
			//   }
			});
			jQuery("#blackListTXT").on('input',function(e){
				// if(e.target.value === ''){
				//   button.hide();
				// } else {
				  button.show();
				// }
			  });
		
				 });
	

})( jQuery );



function ipCheck(bad_ip) { 
	window.open( 
		'https://bad-ip.iridiumintel.com/?ip='+bad_ip, '_blank');
 }

function ipUnlistRequest(bad_ip) {
	window.open( 
		'https://bad-ip.iridiumintel.com/unlist?ip='+bad_ip, '_blank');
 }

 function queryUnlistRequest(action,query) {
	document.getElementById("query_whitelist").value = query; 
	document.getElementById("query_action").value = action; 
	document.getElementById('form-query-whitelist').submit();
 }
 