<?PHP

	/*
		@Author NoobSaibot

		This is a simple example of a bot that will trade on the spread.

		TODO
		 - Get Market Summaries, calculate spread, limit by 25 highest volume, then sort by spread and place orders on top spread
		 - Run bot every 5 minutes or so on an exchange
	*/

	function light_show( $Adapter, $market ) {
		echo "*** " . get_class( $Adapter ) . " Light Show ***\n";

		//_____get currencies/balances:
		$market_summary = $Adapter->get_market_summary( $market );
		$market = $market_summary['market'];
		$curs_bq = explode( "-", $market );
		$base_cur = $curs_bq[0];
		$quote_cur = $curs_bq[1];
		$base_bal_arr = $Adapter->get_balance( $base_cur, array( 'type' => 'exchange' ) );
		$base_bal = $base_bal_arr['available'];
		$quote_bal_arr = $Adapter->get_balance( $quote_cur, array( 'type' => 'exchange' ) );
		$quote_bal = $quote_bal_arr['available'];

		echo " -> " . get_class( $Adapter ) . " \n";
		echo " -> base currency ($base_cur) \n";
		echo " -> base currency balance ($base_bal) \n";
		echo " -> quote currency ($quote_cur) \n";
		echo " -> quote currency balance ($quote_bal) \n";

		//_____calculate some variables that are rather trivial:
		$precision = $market_summary['price_precision'];					//_____significant digits - example 1: "1.12" has 2 as PP. example 2: "1.23532" has 5 as PP.
		$epsilon = 1 / pow( 10, $precision );							//_____smallest unit of base currency that exchange recognizes: if PP is 3, then it is 0.001.
		$buy_price = $market_summary['bid'];							//_____buy at same price as highest bid.
		$sell_price = $market_summary['ask'];							//_____sell at same price as lowest ask.
		$spread = number_format( $sell_price - $buy_price, $precision, '.', '' );		//_____difference between highest bid and lowest ask.

		echo " -> precision $precision \n";
		echo " -> epsilon $epsilon \n";
		echo " -> buy price: $buy_price \n";
		echo " -> sell price: $sell_price \n";
		echo " -> spread: $spread \n";

		$buy = array( 'message' => null ); 
		$sell = array( 'message' => null ); 

		$buy_price = number_format( $buy_price + $epsilon, $precision, '.', '' );
		$sell_price = number_format( $sell_price - $epsilon, $precision, '.', '' );
		$buy_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $buy_price, $precision);
		$sell_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $sell_price, $precision);

		echo " -> final formatted buy price: $buy_price \n";
		echo " -> final formatted sell price: $sell_price \n";
		echo " -> final formatted buy size: $buy_size \n";
		echo " -> final formatted sell size: $sell_size \n";

		if( $spread > 0.000001) {

			//_____Buy & Sell epsilion into the spread:
			if( ! isset( $buy['error'] ) ) {
				echo " -> buying $buy_size of $base_cur for $buy_price $quote_cur costing " . $buy_size * $buy_price . " \n";

				$buy = $Adapter->buy( $market, $buy_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				
				sleep( 3 );
				echo "buy:\n";
				print_r( $buy );
			}
			if( ! isset( $sell['error'] ) ) {
				echo " -> selling $sell_size of $base_cur for $sell_price earning " . $sell_size * $sell_price . " \n";
				
				$sell = $Adapter->sell( $market, $sell_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				
				sleep( 3 );
				echo "\nsell:\n";
				print_r( $sell );
			}

		} else {

			//_____Buy & Sell 1% away from the spread:
			if( ! isset( $buy['error'] ) ) {
				echo " -> buying $buy_size of $base_cur for $buy_price $quote_cur costing " . $buy_size * $buy_price . " \n";
				$buy_price = bcmul($buy_price, 0.99, $precision);
				$buy_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $buy_price, $precision);
				$buy = $Adapter->buy( $market, $buy_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				sleep( 3 );
				echo "buy:\n";
				print_r( $buy );
			}
			if( ! isset( $sell['error'] ) ) {
				echo " -> selling $sell_size of $base_cur for $sell_price earning " . $sell_size * $sell_price . " \n";
				$sell_price = bcmul($sell_price, 1.01, $precision);
				$sell_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $sell_price, $precision);
				$sell = $Adapter->sell( $market, $sell_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				sleep( 3 );
				echo "\nsell:\n";
				print_r( $sell );
			}
		}

/*
		//_____Ran out of funds to buy or sell
		//_____TODO only cancel buy/sell orders if run out of base/quote currency, respectively...
		//if( isset( $buy['error'] ) && isset( $sell['error'] ) ) {
			$open_orders = $Adapter->get_open_orders( $market );
			usort($open_orders, function($a, $b) {
				return $b['timestamp_created'] - $a['timestamp_created'];
			});
			//delete open orders for this market:
			foreach( $open_orders as $open_order ) {
				print_r( $open_order );
				print_r( $Adapter->cancel($open_order['id'], array( 'market' => $open_order['market'] ) ) );
				sleep(3);
			}
			return;
		//}
*/

		echo "\n";

	}

?>
