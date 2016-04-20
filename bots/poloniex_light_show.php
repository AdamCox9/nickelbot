<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make a blink show on Poloniex.

		TODO
		 - a lot
	*/

	function poloniex_light_show( $Adapter ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get the markets to loop over:

		$eth_market = $Adapter->get_market_summary( "ETH-BTC" );

		$price_precision = 8;
		
		$buy_price = $eth_market['bid'];
		$sell_price = $eth_market['ask'];

		echo "buy price: $buy_price\n";
		echo "sell price: $sell_price\n";

		$epsilon = "0.00000001";

		$buy = array( 'message' => null ); 
		$sell = array( 'message' => null ); 

		//_____make 10 new visible orders:
		while( $sell_price - $buy_price > 0.00000001 ) {
			$buy_size = bcdiv( '0.0005', $buy_price, $price_precision );
			$sell_size = bcdiv( '0.0005', $sell_price, $price_precision );
			$buy_price = number_format( $buy_price, $price_precision, '.', '' );
			$sell_price = number_format( $sell_price, $price_precision, '.', '' );

			if( $buy_price < $sell_price ) {
				if( ! isset( $buy['error'] ) ) {
					echo " -> buying $buy_size of ETH for $buy_price costing " . $buy_size * $buy_price . " \n";
					$buy = $Adapter->buy( $eth_market['market'], $buy_size, $buy_price, 'limit', array( 'market_id' => $eth_market['market_id'] ) );
					echo "buy:\n";
					print_r( $buy );
				}
				if( ! isset( $sell['error'] ) ) {
					echo " -> selling $sell_size of ETH for $sell_price earning " . $sell_size * $sell_price . " \n";
					$sell = $Adapter->sell( $eth_market['market'], $sell_size, $sell_price, 'limit', array( 'market_id' => $eth_market['market_id'] ) );
					echo "\nsell:\n";
					print_r( $sell );
				}
				if( isset( $buy['error'] ) && isset( $sell['error'] ) ) {
					if( rand() % 99 == 88 ) {
						$eth_open_orders = $Adapter->get_open_orders( "ETH-BTC" );
						foreach( $eth_open_orders as $eth_open_order ) {
							print_r( $Adapter->cancel($eth_open_order['id'], array( 'market' => $eth_open_order['market'] ) ) );
						}
					}
					return;
				}

			}

			$buy_price = $buy_price + $epsilon;
			$sell_price = $sell_price - $epsilon;

		}

		echo "\n";

	}

?>
