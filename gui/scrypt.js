	$.ajax({
		url: "/api/index.php?action=exchanges",
		context: document.body
	}).done(function(data) {
		//console.log( JSON.stringify( data ) );
		$( 'div#div_exchanges' ).html( data );
	});

	$( "a#a_deposit_addresses" ).click(function() {
		exchanges = $( 'div#div_exchanges' ).html();
		var obj = JSON.parse( exchanges );
		//console.log( obj );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=deposit_addresses&exchange="+value,
			 context: document.body
			}).done(function(data) {
				//console.log( data );
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_deposit_addresses' ).append( "<div class='deposit_address'>" + index + "<br/>" + "<span class='deposit_address'>" + value[i]['currency'] + ": " + value[i]['address'] + "</span><br/>(" + value[i]['wallet_type'] + ")</div>" );
					}
				});
			});
		});
	});

	$( "a#a_open_orders" ).click(function() {
		exchanges = $( 'div#div_open_orders' ).html();
		var obj = JSON.parse( exchanges );
		//console.log( obj );
		$.each(obj, function( index, value ) {
			$.ajax({
			 url: "/api/index.php?action=open_orders&exchange="+value,
			 context: document.body
			}).done(function(data) {
				//console.log( data );
				var obj = JSON.parse( data );
				$.each( obj, function( index, value ) {
					for( var i = 0; i < value.length; i++ ) {
						$( 'div#div_open_orders' ).append( "<div class='open_orders'>" + index + "<br/>" + "<span class='open_orders'>" + value[i]['currency'] + ": " + value[i]['address'] + "</span><br/>(" + value[i]['wallet_type'] + ")</div>" );
					}
				});
			});
		});
	});

	$( "a#a_completed_orders" ).click(function() {
		$.ajax({
			url: "/api/index.php?action=completed_orders",
			context: document.body
		}).done(function(data) {
			//console.log( JSON.stringify( data ) );
			$( 'div#div_completed_orders' ).html( data );
		});
	});