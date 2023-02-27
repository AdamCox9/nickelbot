<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy orders on the spread.
		It will set buy orders for every trading pair available for each adapter where balances permit meeting criteria below.

		TODO
		 - There is a problem with the market names for Kraken. In Market Summaries, BTC is XXBT, but in Open Orders it is XBT. 
		  - Is it just add X in front like XXBT-XETH? This bot won't work on Kraken until this is fixed.
		 - Bittrex does not support updating orders.
		 - Have not tried with any other exchanges.

	*/

	function update_orders( $Adapters = array(), $_CONFIG = array() ) {

		foreach( $Adapters as $Adapter ) {
			echo "*** make_min_orders: " . get_class( $Adapter ) . " ***\n";


			echo " -> getting balances \n";
			$balances = $Adapter->get_balances( );

			//Print balances for debugging:
			//print_r( $balances );

			$open_orders = $Adapter->get_open_orders();
			
			foreach( $open_orders as $open_order ) {
				print_r( $open_order );
				//die( "TEST" );
				
				$order_id = $open_order['id'];
				$market = $open_order['market'];
				$price = $open_order['price'];
				$amount = $open_order['amount'];
				$side = $open_order['side'];
				$market_summary = $Adapter->get_market_summary( $market );
				
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
				$curs_bq = explode( "-", $market );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];
				$amount = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $price, 8 );

				echo " -> Updating $side order $order_id to price $price and amount $amount $base_cur totaling " . $price * $amount . " $quote_cur in ($market).\n";

				die( "TEST" );

				//$results = $Adapter->update_order( $market, $order_id, $amount, $price, array() );
				
				//print_r( $results );
				
				//die( "TEST" );
			}

			echo " -> finished executing " . get_class( $Adapter ) . "\n\n";
		}
	}

?>
