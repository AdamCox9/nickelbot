once = false;
function updateChats(){
	$.ajax({
	  url: "data/trollbox/btc_e.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#btc_e_box" ).html( data );
	  if( ! once )
		  $( "div#btc_e_box" ).scrollTop($( "div#btc_e_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "data/trollbox/poloniex.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#poloniex_box" ).html( data );
	  if( ! once )
		  $( "div#poloniex_box" ).scrollTop($( "div#poloniex_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "data/trollbox/cryptsy.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#cryptsy_box" ).html( data );
	  if( ! once )
		  $( "div#cryptsy_box" ).scrollTop($( "div#cryptsy_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "data/trollbox/c_cex.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#c_cex_box" ).html( data );
	  if( ! once )
		  $( "div#c_cex_box" ).scrollTop($( "div#c_cex_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "data/trollbox/yobit.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#yobit_box" ).html( data );
	  if( ! once )
		  $( "div#yobit_box" ).scrollTop($( "div#yobit_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "data/trollbox/bitcointalk.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#bitcointalk_box" ).html( data );
	  if( ! once )
		  $( "div#bitcointalk_box" ).scrollTop($( "div#bitcointalk_box" )[0].scrollHeight);
	});
}
updateChats();
function startUpdatingChats() {
	once = true;
	updateChats();
}
setInterval(startUpdatingChats, 5000);