<!DOCTYPE html>
<html>
	<head>
	  <meta charset="UTF-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	  <title>NickelBot | Home</title>
	</head>
	<body>
		<h1>NickelBot</h1>
		<h2>Home</h2>
		<div>
			<a href="/">Home</a> | <a href="/bitcointxsound/">What does a Bitcoin Transaction sound like?</a> | <a href="/supertrollbox.php">Super Trollbox</a><br>
		</div>
		<br><br>
		<div style='clear:both;'></div>

		<h1>NickelBot - Bitcoin trading bot in the cloud.</h1>
		<p>This is the home of NickelBot. NickelBot does stuff with Bitcoin such as trading and investing.</p>
		<p>Check back sometime to see if there are tools that will be open to the public.</p>
		<p>There will probably be be some Bitcoin bots living here in the cloud sometime soon that will be accessible to everyone.
		<p>Contact <a href="mailto:adam.cox9@gmail.com">Adam.Cox9@gmail.com</a> with questions.</p>

		<div id="container">Loading...</div>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
		  
		<script type="text/javascript">
			$.ajax({
				  url: "",
				  context: document.body
				}).done(function(data) {
				  console.log( JSON.stringify( data ) );
				  $( 'div#container' ).html( "" );
				  for( i = 0; i < data.length; i++ )
					//$( 'div#container' ).append( "<br>" + data[i] );
				});
		</script>
	</body>
</html>