<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make a maximum sell order of each currency

		TODO
		 - Allow to specify certain trading pairs instead of hitting all of them

	*/

	function make_max_orders( $Adapters, $_CONFIG ) {

		foreach( $Adapters as $Adapter ) {
			echo "*** " . get_class( $Adapter ) . " ***\n";

			//$Adapter->cancel_all();

			//_____get the markets to loop over:
			echo " -> getting market summaries \n";
			$market_summaries = $Adapter->get_market_summaries();
			sleep(1);

			$num_markets = sizeof( $market_summaries );
			echo " -> got $num_markets markets \n";

			//Only use BTC pairs:
			$filtered_market_summaries_by_btc = [];			
			foreach( $market_summaries as $market_summary ) {
				$market = $market_summary['market'];
				$curs_bq = explode( "-", $market );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];
				if( $quote_cur != 'BTC' ) continue;				
				array_push( $filtered_market_summaries_by_btc, $market_summary );
			}

			echo " -> narrowed down to " . count( $filtered_market_summaries_by_btc ) . " BTC markets \n";
			$market_summaries = $filtered_market_summaries_by_btc;

			echo " -> getting balances \n";
			$balances = $Adapter->get_balances();
			echo " -> got " . count( $balances ) . " balances \n";

			//TODO loop through market_summaries and remove markets that have base balance of 0:
						
			//print_r( $balances );
			//die( 'test' );
			sleep(1);


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
				$min_order_base = isset( $market_summary['minimum_order_size_base'] ) ? $market_summary['minimum_order_size_base'] : null;
				$min_order_quote = isset( $market_summary['minimum_order_size_quote'] ) ? $market_summary['minimum_order_size_quote'] : null;
				$precision = $market_summary['price_precision'];	
				$epsilon = 1 / pow( 10, $precision );
				
				$sell_price = bcmul( $ask, $_CONFIG['SELL_AT_PERCENT_CHANGE'], 8); //Percent above asking price

				echo " -> " . get_class( $Adapter ) . " \n";
				echo " -> $market \n";
				echo " -> base currency: $base_cur \n";
				echo " -> base currency balance: $base_bal \n";
				echo " -> quote currency: $quote_cur \n";
				echo " -> quote currency balance: $quote_bal \n";
				echo " -> ask: $ask \n";
				echo " -> min order size base: $min_order_base \n";
				echo " -> min order size quote: $min_order_quote \n";
				echo " -> precision: $precision \n";
				echo " -> epsilon: (min unit of base currency): $epsilon \n";
				echo " -> sell price: " . number_format( $sell_price, 8 ) . " \n";

				if( number_format( $base_bal, 8 ) == 0 ) {
					echo " -> base balance of $base_bal needs to be greater than 0, continuing \n";
					echo "skipping\n\n";
					continue;
				}

				$order_size = $base_bal;
				$total_earnings = bcmul( $order_size, $sell_price, 8 );

				echo " -> *** attempt to sell $order_size $base_cur in $market for $sell_price $quote_cur earning $total_earnings $quote_cur with base balance of $base_bal\n";

				if( $total_earnings < $min_order_quote ) {
					echo " -> *** base balance of $base_bal $base_cur with total earnings of $total_earnings $quote_cur at sell price of $sell_price $quote_cur is too low\n";
					echo "...skipping\n\n";
					continue;
				}

				$sell = $Adapter->sell( $market, $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				sleep(1);
				if( isset( $sell['message'] ) ){ //error...
					print_r( $sell );
				} else {
					echo " -> *** sell order appears to have been placed succesfully \n";
					$balances[ $base_cur ]['available'] = $balances[ $base_cur ]['available'] - $order_size;
				}

			}

			echo "\n -> ### fininishing creating sell orders\n\n";
			sleep(1);

		}
	}

?>
