<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make orders on the spread at Bitfinex.

		TODO
		 - a lot
	*/

	function bitfinex_light_show( $Adapter ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get the markets to loop over:

		$eth_market = $Adapter->get_market_summary( "ETH-BTC" );
		$price_precision = 8;
		
		$buy_price = $eth_market['bid'];
		$sell_price = $eth_market['ask'];

		echo "buy price: $buy_price\n";
		echo "sell price: $sell_price\n";

		$min_order_size = "0.1"; // (base) must buy 0.01 ETH
		$epsilon = "0.00001";

		$buy = array(); 
		$sell = array(); 

		//_____make 10 new visible orders:
		while( $sell_price - $buy_price > $epsilon ) {
			$buy_price = number_format( $buy_price, $price_precision, '.', '' );
			$sell_price = number_format( $sell_price, $price_precision, '.', '' );

			if( $buy_price < $sell_price ) {

				if( ! isset( $buy['message'] ) ) {
					echo " -> buying $min_order_size of ETH for $buy_price costing " . $min_order_size * $buy_price . " \n";
					$buy = $Adapter->buy( $eth_market['market'], $min_order_size, $buy_price, 'limit' );
					echo "buy:\n";
					print_r( $buy );
				}
				if( ! isset( $sell['message'] ) ) {
					echo " -> selling $min_order_size of ETH for $sell_price earning " . $min_order_size * $sell_price . " \n";
					$sell = $Adapter->sell( $eth_market['market'], $min_order_size, $sell_price, 'limit' );
					echo "\nsell:\n";
					print_r( $sell );
				}


				if( isset( $buy['message'] ) && isset( $sell['message'] ) ) {
					if( rand() % 28 == 18 )
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
