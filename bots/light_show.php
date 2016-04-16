<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make orders on the spread at any exchange.

		TODO
		 - accept an array of markets...
	*/

	function light_show( $Adapter ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get the markets to loop over:

		if( rand(0,100) == 3 )
			$Adapter->cancel_all();

		$markets = $Adapter->get_markets();

		$market = $markets[ rand( 0, sizeof( $markets ) - 1 ) ];
		$market_summary = $Adapter->get_market_summary( $market );
		$price_precision = 5;

		if( $market == "BTC-USD" || $market == "BTC-EUR" || $market == "BTC-RUR" ) {
			$min_order_size = "0.0125"; // (base) must buy 0.01 BTC
			$epsilon = "0.01";
		} else {
			$min_order_size = "0.1"; // (base) must buy 0.1 ETH or LTC
			$epsilon = "0.00001";
		}

		$buy_price = $market_summary['bid'];
		$sell_price = $market_summary['ask'];

		echo "buy price: $buy_price\n";
		echo "sell price: $sell_price\n";
		echo "epsilon: $epsilon\n";
		echo "spread: " . ($sell_price - $buy_price) . "\n";

		$buy = array(); 
		$sell = array(); 

		//_____make 10 new visible orders:
		while( $sell_price - $buy_price > $epsilon ) {
			$buy_price = number_format( $buy_price, $price_precision, '.', '' );
			$sell_price = number_format( $sell_price, $price_precision, '.', '' );

			if( $buy_price < $sell_price ) {

				if( ! isset( $buy['message'] ) ) {
					echo " -> buying $min_order_size in $market for $buy_price costing " . $min_order_size * $buy_price . " \n";
					$buy = $Adapter->buy( $market_summary['market'], $min_order_size, $buy_price, 'limit' );
					echo "buy:\n";
					print_r( $buy );
				}
				if( ! isset( $sell['message'] ) ) {
					echo " -> selling $min_order_size in $market for $sell_price earning " . $min_order_size * $sell_price . " \n";
					$sell = $Adapter->sell( $market_summary['market'], $min_order_size, $sell_price, 'limit' );
					echo "\nsell:\n";
					print_r( $sell );
				}


				if( isset( $buy['message'] ) && isset( $sell['message'] ) ) {
					if( rand(0,10) == 3 )
						$Adapter->cancel_all();
					if( $buy['message'] == 'You cannot place more than 100 limit orders per pair' || $sell['message'] == 'You cannot place more than 100 limit orders per pair' )
						$Adapter->cancel_all();

					return;
				}

			}

			$buy_price = $buy_price + $epsilon;
			$sell_price = $sell_price - $epsilon;

		}

		echo "\n";

	}

?>
