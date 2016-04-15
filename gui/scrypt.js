	$.ajax({
		url: "/api/index.php?action=exchanges",
		context: document.body
	}).done(function(data) {
		$( 'div#div_exchanges_save' ).html( data );
	});

	$( "span#a_exchanges" ).click(function() {
		exchanges = $( 'div#div_exchanges_save' ).html();
		var obj = JSON.parse( exchanges );
		$.each(obj, function( index, value ) {
			$( 'div#div_exchanges' ).append( "<div class='exchange'>" + value + "</div>" );
		});
	});

	$( "span#a_currencies" ).click(function() {
		exchanges = $( 'div#div_exchanges_save' ).html();
		var obj = JSON.parse( exchanges );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=get_currencies&exchange="+value,
			 context: document.body
			}).done(function(data) {
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_currencies' ).append( "<div class='currency'>" + index + " - " + value[i] + "</div>" );
					}
				});
			});
		});
	});

	$( "span#a_markets" ).click(function() {
		exchanges = $( 'div#div_exchanges_save' ).html();
		var obj = JSON.parse( exchanges );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=get_markets&exchange="+value,
			 context: document.body
			}).done(function(data) {
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_markets' ).append( "<div class='market'>" + index + " - " + value[i] + "</div>" );
					}
				});
			});
		});
	});

	$( "span#a_deposit_addresses" ).click(function() {
		exchanges = $( 'div#div_exchanges_save' ).html();
		var obj = JSON.parse( exchanges );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=deposit_addresses&exchange="+value,
			 context: document.body
			}).done(function(data) {
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_deposit_addresses' ).append( "<div class='deposit_address'>" + index + "<br/>" + "<span class='deposit_address'>" + value[i]['currency'] + ": " + value[i]['address'] + "</span><br/>(" + value[i]['wallet_type'] + ")</div>" );
					}
				});
			});
		});
	});

	$( "span#a_open_orders" ).click(function() {
		exchanges = $( 'div#div_exchanges_save' ).html();

		//currency = this.currency
		//exchange = this.exchange

		var obj = JSON.parse( exchanges );
		//console.log( obj );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=get_open_orders&exchange="+value,
			 context: document.body
			}).done(function(data) {
				//console.log( data );
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						console.log( JSON.stringify( value[i] ) );
						$( 'div#div_open_orders' ).append( "<div class='open_orders'>" + index + "<br/>" + value[i]['type'] + " " + "<span class='open_orders'> at " + value[i]['price'] + " for " + value[i]['amount'] + " " + value[i]['market'] + "</span><br/>(" + value[i]['timestamp_created'] + ")" + " (id: " + value[i]['id'] + ")</div>" );
					}
				});
			});
		});
	});

	$( "span#a_completed_orders" ).click(function() {

		exchanges = $( 'div#div_exchanges_save' ).html();
		var obj = JSON.parse( exchanges );
		$.each(obj, function( index, value ) {
			$( 'div#div_completed_orders' ).append( "<div style='text-decoration:underline;' class='exchange' id='" + value + "'>" + value + "</div>" );
			$( "div#" + value ).click(function(event) {
				$.ajax({
				 url: "/api/index.php?action=get_markets&exchange="+event.target.id,
				 context: document.body
				}).done(function(data) {
					var obj = JSON.parse( data );
					$.each( obj, function( index, value ) {
						for( var i = 0; i < value.length; i++ ) {
							$( 'div#div_completed_orders' ).append( "<div style='text-decoration:underline;' class='market' id='" + index + "_" + value[i] + "'>" + index + " - " + value[i] + "</div>" );
							$( "div#" + index + "_" + value ).click(function(event) {
								var arr = event.target.id.split("_");
								var exchange = arr[0].replace("Adapter", "");
								var market = arr[1];
								$.ajax({
								 url: "/api/index.php?action=get_completed_orders&exchange="+exchange+"&market="+market,
								 context: document.body
								}).done(function(data) {
									console.log( data );
									$( 'div#div_completed_orders' ).append( data );
								});
							});
						}
					});
				});
			});
		});

		/*$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=get_markets&exchange="+value,
			 context: document.body
			}).done(function(data) {
				//console.log( data );
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_open_orders' ).append( "<div class='open_orders'>" + index + "<br/>" + value[i]['type'] + " " + "<span class='open_orders'> at " + value[i]['price'] + " for " + value[i]['amount'] + " " + value[i]['market'] + "</span><br/>(" + value[i]['timestamp_created'] + ")</div>" );
					}
				});
			});
		});*/
	});

	$( "span#a_btc_tx_sound" ).click(function() {
		//funny for now
		init_btc_sounds();
	});

	/*$( "span#a_btc_visualizations" ).click(function() {
		//so much to do
		//either find a 3rd party visualizer or create on with webgl...
		$( 'div#div_btc_visualizations' ).html( "<iframe width='33%' src='https://gappleto97.github.io/visualizer/'></iframe>" );
	});*/