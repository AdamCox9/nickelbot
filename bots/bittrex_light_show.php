<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make a star show on Bittrex in ETH market.
		See the light_show bot for version that works with all currencies on all exchanges.

		TODO
		 - a lot
	*/

	function bittrex_light_show( $Adapter ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get the markets to loop over:

		$eth_market = $Adapter->get_market_summary( "BTC-ETH" );
		$price_precision = 8;
		
		$buy_price = $eth_market['bid'];
		$sell_price = $eth_market['ask'];

		echo "buy price: $buy_price\n";
		echo "sell price: $sell_price\n";

		$epsilon = "0.00000005";

		$buy = array( 'message' => null ); 
		$sell = array( 'message' => null ); 

		//_____make 10 new visible orders:
		while( $sell_price - $buy_price > 0.00000001 ) {
			$buy_size = bcdiv( '0.0005', $buy_price, $price_precision );
			$sell_size = bcdiv( '0.0005', $sell_price, $price_precision );
			$buy_price = number_format( $buy_price, $price_precision, '.', '' );
			$sell_price = number_format( $sell_price, $price_precision, '.', '' );

			if( $buy_price < $sell_price ) {

				if( $buy['message'] !== 'INSUFFICIENT_FUNDS' ) {
					echo " -> buying $buy_size of ETH for $buy_price costing " . $buy_size * $buy_price . " \n";
					$buy = $Adapter->buy( $eth_market['market'], $buy_size, $buy_price, 'limit', array( 'market_id' => $eth_market['market_id'] ) );
					echo "buy:\n";
					print_r( $buy );
				}
				if( $sell['message'] !== 'INSUFFICIENT_FUNDS' ) {
					echo " -> selling $sell_size of ETH for $sell_price earning " . $sell_size * $sell_price . " \n";
					$sell = $Adapter->sell( $eth_market['market'], $sell_size, $sell_price, 'limit', array( 'market_id' => $eth_market['market_id'] ) );
					echo "\nsell:\n";
					print_r( $sell );
				}
				if( $buy['message'] == 'INSUFFICIENT_FUNDS' && $sell['message'] == 'INSUFFICIENT_FUNDS' ) {
					if( rand() % 99 == 88 )
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
