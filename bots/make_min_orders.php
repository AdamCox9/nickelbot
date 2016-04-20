<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy and sell orders for every currency across every exchange.

		A 1% gap is very large for BTC while very small for some ALTs

		TODO
		 - a lot
	*/

	function make_min_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			echo "*** " . get_class( $Adapter ) . " ***\n";

			//_____get the markets to loop over:
			$market_summaries = $Adapter->get_market_summaries();
			$num_markets = sizeof( $market_summaries );

			/*//_____get open orders, sort them by creation date and remove the oldest orders:
			$open_orders = $Adapter->get_open_orders();
			usort($open_orders, function($a, $b) {
				return $b['timestamp_created'] - $a['timestamp_created'];
			});

			//_____remove oldest orders for each valid market...
			$x = 0;
			while( $x++ < $num_markets )
				if( sizeof( $open_orders ) > 0 )
					if( get_class( $Adapter ) == "PoloniexAdapter" )
						$output = $Adapter->cancel( $order['id'], array( "market" => $order['market'] ) );
					else
						$output = $Adapter->cancel( $order['id'] );*/

			$bal = [];
			shuffle( $market_summaries );
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] )
					continue;

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

				if( floatval($buy_price) > 0 ) { //some currencies have big sell wall at 0.00000001...
					$order_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $epsilon, $buy_price, $precision);
					echo " -> buying $order_size in $market for $buy_price costing " . $order_size * $buy_price . " with quote balance of $quote_bal \n";
					if( floatval($order_size * $buy_price) > floatval($quote_bal) )
						echo "\n\n -> quote balance of $quote_bal is too low for min buy order size of $order_size at buy price of $buy_price \n\n";
					else {
						$buy = $Adapter->buy( $market_summary['market'], $order_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
						if( isset( $buy['message'] ) ) {
							print_r( $buy );
							die('test');
						} else {
							$bal[ $quote_cur ] = $quote_bal - $order_size * $buy_price;
						}
					}
				}

				if( floatval($sell_price) > floatval($buy_price) ) { //just in case...
					$order_size = Utilities::get_min_order_size( $market_summary['minimum_order_size_base'], $market_summary['minimum_order_size_quote'], $epsilon, $buy_price, $precision);
					echo " -> selling $order_size in $market for $sell_price earning " . $order_size * $sell_price . " with base balance of $base_bal\n";
					if( floatval($order_size) > floatval($base_bal) )
						echo "\n\n -> base balance of $base_bal is too low for min sell order size of $order_size at sell price of $sell_price \n\n";
					else {
						$sell = $Adapter->sell( $market_summary['market'], $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
						if( isset( $sell['message'] ) ){
							print_r( $sell );
							die('test');
						} else {
							$bal[ $base_cur ] = $base_bal - $order_size;
						}
					}
				}
				print_r( $bal );
				echo "\n";
			}
		}
	}

?>
