once = false;
function updateChats(){
	/*$.ajax({
	  url: "api/trollbox/btc_e.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#btc_e_box" ).html( data );
	  if( ! once )
		  $( "div#btc_e_box" ).scrollTop($( "div#btc_e_box" )[0].scrollHeight);
	});*/
	/*$.ajax({
	  url: "api/trollbox/poloniex.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#poloniex_box" ).html( data );
	  if( ! once )
		  $( "div#poloniex_box" ).scrollTop($( "div#poloniex_box" )[0].scrollHeight);
	});*/
	$.ajax({
	  url: "api/trollbox/yobit.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#yobit_box" ).html( data );
	  if( ! once )
		  $( "div#yobit_box" ).scrollTop($( "div#yobit_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "api/trollbox/bitcointalk.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#bitcointalk_box" ).html( data );
	  if( ! once )
		  $( "div#bitcointalk_box" ).scrollTop($( "div#bitcointalk_box" )[0].scrollHeight);
	});
	$.ajax({
	  url: "api/trollbox/redditbitcoin.php",
	  context: document.body
	}).done(function(data) {
	  $( "div#redditbitcoin_box" ).html( data );
	  if( ! once )
		  $( "div#redditbitcoin_box" ).scrollTop($( "div#redditbitcoin_box" )[0].scrollHeight);
	});
}
updateChats();
function startUpdatingChats() {
	once = true;
	updateChats();
}
setInterval(startUpdatingChats, 15000);