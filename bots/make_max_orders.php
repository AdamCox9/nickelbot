<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make a maximum sell order of each currency

		TODO
		 - Allow to specify certain trading pairs instead of hitting all of them

	*/

	function make_max_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			echo "*** " . get_class( $Adapter ) . " ***\n";

			//$Adapter->cancel_all();

			//_____get the markets to loop over:
			echo " -> getting market summaries \n";
			$market_summaries = $Adapter->get_market_summaries();
			sleep(3);

			$num_markets = sizeof( $market_summaries );
			echo " -> getting balances \n";
			$balances = $Adapter->get_balances();
			sleep(3);

			shuffle( $market_summaries ); // non-alphabetical!

			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] ) { echo "\nfrozen\n"; continue; }

				//_____get currencies/balances:
				$market = $market_summary['market'];
				$curs_bq = explode( "-", $market );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1]; //if( $quote_cur != 'XBT' && $quote_cur != 'BTC' ) continue;
				$base_bal = isset( $balances[ $base_cur ] ) ? $balances[ $base_cur ]['available'] : 0;
				$quote_bal = isset( $balances[ $quote_cur ] ) ? $balances[ $quote_cur ]['available'] : 0;
				$ask = $market_summary['ask'];
				$bid = $market_summary['bid'];
				$min_order_base = isset( $market_summary['minimum_order_size_base'] ) ? $market_summary['minimum_order_size_base'] : null;
				$min_order_quote = isset( $market_summary['minimum_order_size_quote'] ) ? $market_summary['minimum_order_size_quote'] : null;
				$precision = $market_summary['price_precision'];	
				$epsilon = 1 / pow( 10, $precision );				
				$buy_price = bcmul( number_format( $bid, 8 ), 0.5, 8);
				$sell_price = bcmul( number_format( $ask, 8 ), 2, 8);
				$spread = $sell_price - $buy_price;					

				echo " -> " . get_class( $Adapter ) . " \n";
				echo " -> $market \n";
				echo " -> base currency: $base_cur \n";
				echo " -> base currency balance: $base_bal \n";
				echo " -> quote currency: $quote_cur \n";
				echo " -> quote currency balance: $quote_bal \n";
				echo " -> bid: $bid \n";
				echo " -> ask: $ask \n";
				echo " -> min order size base: $min_order_base \n";
				echo " -> min order size quote: $min_order_quote \n";
				echo " -> precision: $precision \n";
				echo " -> epsilon: (min unit of base currency): $epsilon \n";
				echo " -> buy price: " . number_format( $buy_price, 8 ) . " \n";
				echo " -> sell price: " . number_format( $sell_price, 8 ) . " \n";
				echo " -> spread: $spread \n";

				if( number_format( $base_bal, 8 ) == 0 ) {
					echo " -> base balance of $base_bal needs to be greated than 0, continuing \n";
					echo "skipping\n\n";
					continue;
				}

				$order_size = $base_bal;
				$total_earnings = bcmul( $order_size, $sell_price, 8 );

				echo " -> *** attempt to sell $order_size $base_cur in $market for $sell_price $quote_cur earning $total_earnings $quote_cur with base balance of $base_bal\n";

				if( $total_earnings < 0.0005 ) {
					echo " -> *** base balance of $base_bal $base_cur with total earnings of $total_earnings $quote_cur at sell price of $sell_price $quote_cur is too low\n";
					echo "...skipping\n\n";
					continue;
				}

				$sell = $Adapter->sell( $market, $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				sleep(3);
				if( isset( $sell['message'] ) ){ //error...
					print_r( $sell );
				} else {
					echo " -> *** sell order appears to have been placed succesfully \n";
					$balances[ $base_cur ]['available'] = $balances[ $base_cur ]['available'] - $order_size;
				}

			}

			echo "\n -> ### fininishing buy/sell sequence\n\n";
			sleep(3);

		}
	}

?>
