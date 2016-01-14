<html>
	<head>
		<meta charset="utf-8" />
		<title>What does a Bitcoin transaction sound like?</title>

		<script src="riffwave.js"></script>
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

		<script language="javascript" type="text/javascript">
			var wsUri = "wss://ws.blockchain.info/inv"; 
			var output;  
			function init() { 
				output = document.getElementById("output");
				testWebSocket(); 
			}  
			function testWebSocket() { 
				websocket = new WebSocket(wsUri); 
				websocket.onopen = function(evt) { onOpen(evt) };
				websocket.onclose = function(evt) { onClose(evt) }; 
				websocket.onmessage = function(evt) { onMessage(evt) }; 
				websocket.onerror = function(evt) { onError(evt) }; 
			}  
			function onOpen(evt) { 
				writeToScreen("CONNECTED");
				doSend('{"op":"unconfirmed_sub"}'); 
			}  
			function onClose(evt) { 
				writeToScreen("DISCONNECTED"); 
			}  
			function onMessage(evt) { 
				var data = [],
					unicode = '';

				mdata = JSON.parse(evt.data);

				writeToScreen( '<a target="_blank" href="https://blockchain.info/tx/' + mdata.x.hash + '" style="color: blue;">' + mdata.x.hash + '</span>' );


				var audio = new Audio(); // create the HTML5 audio element
				var wave = new RIFFWAVE(); // create an empty wave file
				var data = []; // yes, it's an array

				wave.header.sampleRate = 44100; // set sample rate to 44KHz
				wave.header.numChannels = 2; // two channels (stereo)

				var z = 0;

				// got the text now transform it in unicode
				for(var i = 0; i < mdata.x.hash.length; i++)
				{

					for (var j=0; j < 100; j++ ) {
						data[z++] = 128+Math.round(127*Math.sin((j/10)*mdata.x.hash.charCodeAt(i))); // left speaker
						data[z++] = 128+Math.round(127*Math.sin((j/200)*mdata.x.hash.charCodeAt(i))); // right speaker
					}

					//console.log( data[i] );

					
				}

				wave.Make(data); // make the wave file
				audio.src = wave.dataURI; // set audio source
				audio.play(); // we should hear two tones one on each speaker


				//websocket.close(); 
			}  
			function onError(evt) { 
				writeToScreen('<span style="color: red;">ERROR:</span> ' + evt.data); 
			}  
			function doSend(message) { 
				//writeToScreen("SENT: " + message);  
				websocket.send(message); 
			}  
			function writeToScreen(message) { 
				$( "#output" ).prepend( "<div>" + message + "<div>" );
			}  
			window.addEventListener("load", init, false);
		</script>
	</head>
	<body>
		<h1>NickelBot</h1>
		<h2>What does a Bitcoin transaction sound like?</h2>  
		<div>
			<a href="/">Home</a> | <a href="/bitcointxsound/">What does a Bitcoin Transaction sound like?</a> | <a href="/supertrollbox.php">Super Trollbox</a><br>
		</div>
		<br><br>
		<div style='clear:both;'></div>

		<div>Watch the <a target="_blank" href="https://gappleto97.github.io/visualizer/">visualizations</a>!</div>
		<br>
		<div id="output"></div>

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

		<br><div>donate: 1G9UAUvhVqGDagtn1rms3VaK7EZtZJRzA7</div>

	</body>
</html>