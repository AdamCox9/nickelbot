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
					$( 'div#div_currencies' ).append( "<div class='currency'>" + index.replace("Adapter", "") + " - " + value[i] + "</div>" );
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
					$( 'div#div_markets' ).append( "<div class='market'>" + index.replace("Adapter", "") + " - " + value[i] + "</div>" );
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
					$( 'div#div_deposit_addresses' ).append( "<div class='deposit_address'>" + index.replace("Adapter", "") + " (" + value[i]['wallet_type'] + ")<br/>" + "<span class='deposit_address'>" + value[i]['currency'] + ": " + value[i]['address'] + "</span><br/></div>" );
				}
			});
		});
	});
});

$( "span#a_open_orders" ).click(function() {
	exchanges = $( 'div#div_exchanges_save' ).html();
	var obj = JSON.parse( exchanges );
	$.each(obj, function( index, value ) {
		$.ajax({
		 url: "/api/index.php?action=get_open_orders&exchange="+value,
		 context: document.body
		}).done(function(data) {
			var obj = JSON.parse( data );
			$.each( obj, function( index, value ) {
				for( var i = 0; i < value.length; i++ ) {
					$( 'div#div_open_orders' ).append( "<div class='open_orders' id='" + index + "_" + value[i]['id'] + "'>" + index.replace("Adapter", "") + "<br/>" + value[i]['type'] + " " + "<span class='open_orders'> at " + value[i]['price'] + " for " + value[i]['amount'] + " " + value[i]['market'] + "</span><br/>(" + value[i]['timestamp_created'] + ")" + " (id: " + value[i]['id'] + ")</div>" );
					$( "div#" + index + "_" + value[i]['id'] ).click(function(event) {
						var arr = event.target.id.split("_");
						var exchange = arr[0].replace("Adapter", "");
						var orderid = arr[1];
						$.ajax({
							url: "/api/index.php?action=cancel&exchange="+exchange+"&id="+orderid,
							context: document.body
						}).done(function(data) {
							$( 'div#div_open_orders' ).html( JSON.stringify( data ) );
						});
					});
				}
			});
		});
	});
});

$( "span#a_cancel" ).click(function() {
	$( 'span#a_cancel' ).html( 'See the open orders doc item and click any open order to cancel it' );
	$( "span#a_open_orders" ).trigger( "click" );
});

$( "span#a_cancel_all" ).click(function() {
	exchanges = $( 'div#div_exchanges_save' ).html();
	var obj = JSON.parse( exchanges );
	$.each(obj, function( index, value ) {
		$( 'div#div_cancel_all' ).append( "<div class='exchange' id='" + value + "'>" + value + "</div>" );
		$( "div#" + value ).click(function(event) {
			$.ajax({
				url: "/api/index.php?action=cancel_all&exchange="+event.target.id,
				context: document.body
			}).done(function(data) {
				$( 'div#div_cancel_all' ).html( JSON.stringify( data ) );
			});
		});
	});
});

$( "span#a_completed_orders" ).click(function() {
	exchanges = $( 'div#div_exchanges_save' ).html();
	var obj = JSON.parse( exchanges );
	$.each(obj, function( index, value ) {
		$( 'div#div_completed_orders' ).append( "<div class='exchange' id='" + value + "'>" + value + "</div>" );
		$( "div#" + value ).click(function(event) {
			$.ajax({
				url: "/api/index.php?action=get_markets&exchange="+event.target.id,
				context: document.body
			}).done(function(data) {
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_completed_orders' ).append( "<div class='market clicker' id='" + index + "_" + value[i] + "'>" + index.replace("Adapter", "") + " - " + value[i] + "</div>" );
						$( "div#" + index + "_" + value[i] ).click(function(event) {
							var arr = event.target.id.split("_");
							var exchange = arr[0].replace("Adapter", "");
							var market = arr[1];
							$.ajax({
							 url: "/api/index.php?action=get_completed_orders&exchange="+exchange+"&market="+market,
							 context: document.body
							}).done(function(data) {
								var obj = JSON.parse( data );
								$.each( obj, function( index, value ) {
									$( 'div#div_completed_orders' ).html( '' );
									for( var i = 0; i < value.length; i++ ) {
										$( 'div#div_completed_orders' ).append( "<div class='completed_orders'>" + index.replace("Adapter", "") + "<br/>" + value[i]['type'] + " " + "<span class='completed_orders'> at " + value[i]['price'] + " for " + value[i]['amount'] + " " + value[i]['market'] + "</span><br/>(" + value[i]['timestamp'] + ")" + " (id: " + value[i]['id'] + ")</div>" );
									}
								});
							});
						});
					}
				});
			});
		});
	});
});

$( "span#a_btc_tx_sound" ).click(function() {
	//funny for now
	init_btc_sounds();
});