	var wsUri = "wss://ws.blockchain.info/inv"; 
	var output;  
	function init_btc_sounds() { 
		output = document.getElementById("div_btc_tx_sound");
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
		$( "div#div_btc_tx_sound" ).prepend( "<div>" + message + "<div>" );
	}  
	//window.addEventListener("load", init, false);
