<?PHP

	/*
		@Author NoobSaibot

		This is a simple example of a bot that will make a blink show on Poloniex.

		TODO
		 - a lot
	*/

	function poloniex_light_show( $Adapter, $market ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get the markets to loop over:

		$market_summary = $Adapter->get_market_summary( $market );

		//_____get currencies/balances:
		$market = $market_summary['market'];
		$curs_bq = explode( "-", $market );
		$base_cur = $curs_bq[0];
		$quote_cur = $curs_bq[1];
		$base_bal_arr = $Adapter->get_balance( $base_cur, array( 'type' => 'exchange' ) );
		$base_bal = isset( $bal[ $base_cur ] ) ? $bal[ $base_cur ] : $base_bal_arr['available'];
		$quote_bal_arr = $Adapter->get_balance( $quote_cur, array( 'type' => 'exchange' ) );
		$quote_bal = isset( $bal[ $quote_cur ] ) ? $bal[ $quote_cur ] : $quote_bal_arr['available'];

		echo " -> " . get_class( $Adapter ) . " \n";
		echo " -> base currency ($base_cur) \n";
		echo " -> base currency balance ($base_bal) \n";
		echo " -> quote currency ($quote_cur) \n";
		echo " -> quote currency balance ($quote_bal) \n";

		//_____calculate some variables that are rather trivial:
		$precision = $market_summary['price_precision'] + 2;	//_____significant digits - example 1: "1.12" has 2 as PP. example 2: "1.23532" has 5 as PP.
		$epsilon = 1 / pow( 10, $precision );					//_____smallest unit of base currency that exchange recognizes: if PP is 3, then it is 0.001.
		$buy_price = $market_summary['bid'];					//_____buy at same price as highest bid.
		$sell_price = $market_summary['ask'];					//_____sell at same price as lowest ask.
		$spread = $sell_price - $buy_price;						//_____difference between highest bid and lowest ask.

		echo " -> precision $precision \n";
		echo " -> epsilon $epsilon \n";
		echo " -> buy price: $buy_price \n";
		echo " -> sell price: $sell_price \n";
		echo " -> spread: $spread \n";

		$buy_price = number_format( $market_summary['bid'], $precision, '.', '' );
		$sell_price = number_format( $market_summary['ask'], $precision, '.', '' );

		echo " -> final formatted buy price: $buy_price \n";
		echo " -> final formatted sell price: $sell_price \n";

		$buy = array( 'message' => null ); 
		$sell = array( 'message' => null ); 

		//_____make 10 new visible orders:
		while( $sell_price - $buy_price > 0.00000001 ) {

			if( rand() % 88 < 3 )
				continue;

			$buy_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $epsilon, $buy_price, $precision);
			$sell_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $epsilon, $sell_price, $precision);

			$buy_price = number_format( $buy_price, $precision, '.', '' );
			$sell_price = number_format( $sell_price, $precision, '.', '' );

			if( $buy_price < $sell_price ) {
				if( ! isset( $buy['error'] ) ) {
					echo " -> buying $buy_size of ETH for $buy_price costing " . $buy_size * $buy_price . " \n";
					$buy = $Adapter->buy( $market_summary['market'], $buy_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
					echo "buy:\n";
					print_r( $buy );
				}
				if( ! isset( $sell['error'] ) ) {
					echo " -> selling $sell_size of ETH for $sell_price earning " . $sell_size * $sell_price . " \n";
					$sell = $Adapter->sell( $market_summary['market'], $sell_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
					echo "\nsell:\n";
					print_r( $sell );
				}
				if( isset( $buy['error'] ) && isset( $sell['error'] ) ) {
					if( rand() % 88 < 2 ) {
						$eth_open_orders = $Adapter->get_open_orders( "ETH-BTC" );
						usort($eth_open_orders, function($a, $b) {
							return $b['timestamp_created'] - $a['timestamp_created'];
						});
						//delete the last 44 or so orders every 44 or so buy/sell fails
						foreach( $eth_open_orders as $eth_open_order ) {
							print_r( $eth_open_order );
							if( rand() % 88 < 2 )
								continue;
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
