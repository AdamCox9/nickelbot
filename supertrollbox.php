<html>
	<head>
	  <meta charset="UTF-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>NickelBot | Super Trollbox</title>
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<style>
			div.trollbox_wrapper {
				float:left;
			}
			div.trollbox {
				font-size:10pt;
				width:350px;
				height:350px;
				border: 1px solid #666;
				overflow: scroll;
			}
			span.handle {
				color:blue;
			}
		</style>
	</head>
	<body>
		<h1>NickelBot</h1>
		<h2>Super Trollbox</h2>
		<div>
			<a href="/">Home</a> | <a href="/bitcointxsound/">What does a Bitcoin Transaction sound like?</a> | <a href="/supertrollbox.php">Super Trollbox</a><br>
		</div>
		<br><br>
		<div style='clear:both;'></div>

		<div class='trollbox_wrapper'><a href="http://www.btc-e.com/">BTC-e</a><div class='trollbox' id="btc_e_box">Loading...</div></div>
		<div class='trollbox_wrapper'><a href="http://www.cryptsy.com/">Cryptsy</a><div class='trollbox' id="cryptsy_box">Loading...</div></div>
		<div class='trollbox_wrapper'><a href="http://www.poloniex.com/exchange">Poloniex</a><div class='trollbox' id="poloniex_box">Loading...</div></div>
		<div class='trollbox_wrapper'><a href="http://www.c-cex.com/">C-CEX</a><div class='trollbox' id="c_cex_box">Loading...</div></div>
		<div class='trollbox_wrapper'><a href="https://yobit.net/?bonus=DkmPL">YoBit</a><div class='trollbox' id="yobit_box">Loading...</div></div>
		<div class='trollbox_wrapper'><a href="https://bitcointalk.org/">BitcoinTalk</a><div class='trollbox' id="bitcointalk_box">Loading...</div></div>

		<!-- =============== -->
		<!-- Javascript code -->
		<!-- =============== -->
		<script type="text/javascript">
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

		</script>

		<!-- Go to www.addthis.com/dashboard to customize your tools -->
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=AdamCox9" async="async"></script>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-65928308-1', 'auto');
		  ga('send', 'pageview');

		</script>

		<div style="clear:both;"></div>
		<br><br><br><div>donate and I'll make it cooler: 1G9UAUvhVqGDagtn1rms3VaK7EZtZJRzA7</div>

	</body>
</html>