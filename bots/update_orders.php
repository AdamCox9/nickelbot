<?PHP

	/*

		@Author Adam Cox

		This is a bot that will update the current open orders according to data in the $_CONFIG array.
		The bot can be run every 15 minutes on a cron and it will update the orders to +/- X% of bid/ask.
		This is better than deleting and creating orders on Kraken since they provide the update functionality and show cancelled orders in the history.
		Bittrex does not allow update functionality. They do not show cancelled orders in history... even if partially executed???

	*/

	function update_orders( $Adapters = array(), $_CONFIG = array() ) {

		foreach( $Adapters as $Adapter ) {
			echo "*** update_orders: " . get_class( $Adapter ) . " ***\n";

			echo " -> getting balances \n";
			$balances = $Adapter->get_balances( );

			//Print balances for debugging:
			//print_r( $balances );

			$open_orders = $Adapter->get_open_orders();
			
			foreach( $open_orders as $open_order ) {
				//print_r( $open_order );
				//die( "TEST" );
				
				$order_id = $open_order['id'];
				$market = $open_order['market'];
				$price = $open_order['price'];
				$amount = $open_order['amount'];
				$side = $open_order['side'];
				$market_summary = $Adapter->get_market_summary( $market );
				
				//print_r( array_keys( $market_summary ) );
				$market_summary['OHLC'] = null;
				$market_summary['Depth'] = null;
				$market_summary['Trades'] = null;
				$market_summary['Spread'] = null;
				$market_summary['fees'] = null;
				$market_summary['fees_maker'] = null;
				print_r( $market_summary );
				//die( "TEST" );

				if( $side == "SELL" ) {
					$price = $market_summary['ask'] * 1.02;
				} elseif( $side == "BUY" ) {
					$price = $market_summary['bid'] * 0.98;
				} else {
					continue;
				}

				$min_order_base = $market_summary['minimum_order_size_base'];
				$min_order_quote = $market_summary['minimum_order_size_quote'];
				$base_cur = $market_summary['base'];
				$quote_cur = $market_summary['quote'];

				//What is 'cost_decimals' compared to 'pair_decimals'?
				$price_precision = $market_summary['pair_decimals'];
				$amount = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $price, $price_precision );

				echo " -> Updating $side order $order_id to price $price and amount $amount $base_cur totaling " . $price * $amount . " $quote_cur in ($market).\n";
				//die( "TEST" );

				$results = $Adapter->update_order( $market, $order_id, $amount, number_format( $price, $price_precision ), array() );
				
				print_r( $results );				
				//die( "TEST" );
			}

			echo " -> finished executing " . get_class( $Adapter ) . "\n\n";
		}
	}

?>
