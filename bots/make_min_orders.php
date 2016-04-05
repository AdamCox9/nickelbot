<?PHP

	/*
		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy and sell orders for every currency across every exchange.

		TODO
		 - a lot
	*/

	function make_min_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			echo "*** " . get_class( $Adapter ) . " ***\n";

			//_____get open orders, sort them by creation date and remove the oldest orders:
			$open_orders = $Adapter->get_open_orders();
			usort($open_orders, function($a, $b) {
				return $b['timestamp_created'] - $a['timestamp_created'];
			});
			//_____get the markets to loop over:
			$market_summaries = $Adapter->get_market_summaries();
			$num_markets = sizeof( $market_summaries );


			//_____remove 2 oldest orders (buy/sell) for each valid market...
			$x = 0;
			while( $x++ < $num_markets )
				if( sizeof( $open_orders ) > 0 )
					remove_oldest_order( $Adapter, array_pop( $open_orders ) );

			shuffle( $market_summaries );
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] )
					continue;

				//print_r( $market_summary );

				//_____base currency and quote currency - some examples only to clarify:
				//_____example 1: (if market is BTC-USD) buying BTC with USD - BTC is base and USD is quote
				//_____example 2: (if market is BTC-USD) selling BTC for USD - BTC is base and USD is quote
				//_____example 3: (if market is USD-BTC) buying USD with BTC - USD is base and BTC is quote
				//_____example 4: (if market is USD-BTC) selling USD for BTC - USD is base and BTC is quote
				//_____example 5: (if market is XMR-BTC) buying XMR with BTC - XMR is base and BTC is quote
				//_____example 6: (if market is XMR-BTC) selling XMR for BTC - XMR is base and BTC is quote
				$curs_bq = explode( "-", $market_summary['market'] );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];
				$base_bal_arr = $Adapter->get_balance( $base_cur, array( 'type' => 'exchange' ) );
				//print_r( $base_bal_arr );
				$base_bal = $base_bal_arr['available'];
				$quote_bal_arr = $Adapter->get_balance( $quote_cur, array( 'type' => 'exchange' ) );
				//print_r( $quote_bal_arr );
				$quote_bal = $quote_bal_arr['available'];

				echo " -> base currency ($base_cur) \n";
				echo " -> base currency balance ($base_bal) \n";
				echo " -> quote currency ($quote_cur) \n";
				echo " -> quote currency balance ($quote_bal) \n";

				//_____calculate some variables that are rather trivial:
				$price_precision = $market_summary['price_precision'];			//_____significant digits - example 1: "1.12" has 2 as PP. example 2: "1.23532" has 5 as PP.
				$epsilon = 1 / pow( 10, $market_summary['price_precision'] );	//_____smallest unit of base currency that exchange recognizes: if PP is 3, then it is 0.001.
				$buy_price = $market_summary['bid'] + $epsilon;					//_____try to jump smallest unit possible above highest bid.
				$sell_price = $market_summary['ask'] - $epsilon;				//_____try to jump smallest unit possible below lowest ask.
				$spread = $sell_price - $buy_price;								//_____difference between highest bid and lowest ask.
				$min_diff = 0.01 * $sell_price;									//_____orders should be at-least 1% far apart.

				echo " -> price precision $price_precision \n";
				echo " -> epsilon $epsilon \n";
				echo " -> buy price 1: $buy_price \n";
				echo " -> sell price 1: $sell_price \n";
				echo " -> spread 1: $spread \n";
				echo " -> minimum difference 1: $min_diff \n";

				//_____widen the spread if not wide enough:
				$z = 0;
				while ( $spread < $min_diff ) {
					$z++;
					//_____buy for z% less than min ask and sell for z% more than max bid:
					$buy_price = $market_summary['ask'] - ( 0.01 * $z * $market_summary['ask'] );
					$sell_price = $market_summary['bid'] + ( 0.01 * $z * $market_summary['bid'] );
					$spread = $sell_price - $buy_price;

					echo " -> buy price $z: $buy_price \n";
					echo " -> sell price $z: $sell_price \n";
					echo " -> spread $z: $spread \n";

					if($z > 101) break; //just in case don't want to loop more than 101 times...

					//_____make sure spread is at least two of epsilon far apart so not making market buy or market sell order:
					if ( $spread > 2 * $epsilon )
						break;
					else
						continue;

					if( $buy_price >= $sell_price )
						continue;

				}

				$buy_price = number_format( $buy_price, $price_precision, '.', '' );
				$sell_price = number_format( $sell_price, $price_precision, '.', '' );

				echo " -> final formatted buy price: $buy_price \n";
				echo " -> final formatted sell price: $sell_price \n";

				if( $buy_price > 0 ) {
					if( ! isset( $market_summary['minimum_order_size_base'] ) )
						$order_size = bcdiv( $market_summary['minimum_order_size_quote'] + 1000 * $epsilon, $buy_price, $price_precision );
					else
						$order_size = $market_summary['minimum_order_size_base'];

					if( floatval($order_size * $buy_price) > floatval($quote_bal) )
						echo " -> quote balance of $quote_bal is too low for min buy order size of $order_size at buy price of $buy_price\n";
					else
						$Adapter->buy( $market_summary['market'], $order_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				}

				if( $sell_price > 0 ) {
					if( ! isset( $market_summary['minimum_order_size_base'] ) )
						$order_size = bcdiv( $market_summary['minimum_order_size_quote'] + 1000 * $epsilon, $sell_price, $price_precision );
					else
						$order_size = $market_summary['minimum_order_size_base'];
					
					if( floatval($order_size) > floatval($base_bal) )
						echo " -> base balance of $base_bal is too low for min sell order size of $order_size at sell price of $sell_price\n";
					else
						$Adapter->sell( $market_summary['market'], $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				}
				echo "\n";
			}
		}
	}

	function remove_oldest_order( $Adapter, $order ) {
		if( get_class( $Adapter ) == "PoloniexAdapter" )
			$output = $Adapter->cancel( $order['id'], array( "market" => $order['market'] ) );
		else
			$output = $Adapter->cancel( $order['id'] );
	}

?>
