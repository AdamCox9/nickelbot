<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy and sell orders on the spread.
		It will set buy & sell orders for every trading pair available for each adapter where balances permit.

		TODO
		 - Allow to specify certain trading pairs instead of hitting all of them

	*/

	function make_min_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			echo "*** " . get_class( $Adapter ) . " ***\n";

			//_____get the markets to loop over:
			echo " -> getting market summaries \n";
			$market_summaries = $Adapter->get_market_summaries();
			sleep(1);

			echo " -> got " . count( $market_summaries ) . " markets \n";

			echo " -> getting balances \n";
			$balances = $Adapter->get_balances();
			sleep(1);

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

			//Sort by volume:
			$market_summaries = $filtered_market_summaries_by_btc;
			usort($market_summaries, fn($a, $b) => $a['quote_volume'] <=> $b['quote_volume']);
	
			//Get top 15 markets by volume:
			array_splice($market_summaries, 0, count($market_summaries)-15);

			echo " -> narrowed down to " . count( $market_summaries ) . " by top volume \n";
	
			//______show random market for view:
			print_r( $market_summaries[ array_rand( $market_summaries ) ] );

			//Sort by percent change
			usort($market_summaries, fn($a, $b) => $a['percentChange'] <=> $b['percentChange']);

			//Get top 5 markets by percent_change:
			array_splice($market_summaries, 0, count($market_summaries)-5);

			echo " -> narrowed down to " . count( $market_summaries ) . " by percent change \n";

			print_r( $market_summaries );

			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] ) { echo "\nfrozen\n"; continue; }

				//_____get currencies/balances:
				$market = $market_summary['symbol'];
				$curs_bq = explode( "-", $market );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];
				$base_bal = isset( $balances[ $base_cur ] ) ? $balances[ $base_cur ]['available'] : 0;
				$quote_bal = isset( $balances[ $quote_cur ] ) ? $balances[ $quote_cur ]['available'] : 0;
				$ask = $market_summary['ask'];
				$bid = $market_summary['bid'];
				$min_order_base = $market_summary['minimum_order_size_base'];
				$min_order_quote = $market_summary['minimum_order_size_quote'];
				$precision = $market_summary['price_precision'];			//_____significant digits for base currency - "1.12" has 2 as precision //TODO: find precision for quote currency
				$epsilon = 1 / pow( 10, $precision );					//_____smallest unit of base currency that exchange recognizes: if precision is 3, then it is 0.001.
				$buy_price = bcmul( $bid, 0.99, 8);					//$market_summary['bid'] + $epsilon;	//_____buy at one unit above highest bid.
				$sell_price = bcmul( $ask, 1.1, 8);					//$market_summary['ask'] - $epsilon;	//_____sell at one unit below lowest ask.
				$spread = $sell_price - $buy_price;					//_____difference between highest bid and lowest ask.

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
				echo " -> buy price: $buy_price \n";
				echo " -> sell price: $sell_price \n";
				echo " -> spread: $spread \n";
				echo "\n";

				if( $buy_price < $sell_price ) { //just in case...

					//_____Do the buy:

					$order_size = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $buy_price, $precision);

					echo " -> *** attempt to buy $order_size $base_cur in $market for $buy_price $quote_cur costing " . bcmul( $order_size, $buy_price, 8 ) . " $quote_cur with quote balance of $quote_bal \n";
					if( $order_size * $buy_price > $quote_bal )
						echo " -> *** quote balance of $quote_bal $quote_cur is too low for min buy order size of $order_size $base_cur at buy price of $buy_price $quote_cur \n";
					else {
						$buy = $Adapter->buy( $market, $order_size*1.2, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );

						sleep(1);
						if( isset( $buy['CODE'] ) ) { //error...
							print_r( $buy );
						} else {
							echo " -> *** buy order appears to have been placed succesfully \n";
							$balances[ $quote_cur ]['available'] = $balances[ $quote_cur ]['available'] - bcmul( $order_size, $buy_price, 8 );
						}
					}

					//_____Do the sell:

					$order_size = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $sell_price, $precision);

					echo " -> *** attempt to sell $order_size $base_cur in $market for $sell_price $quote_cur earning " . bcmul( $order_size, $sell_price, 8 ) . " $quote_cur with base balance of $base_bal\n";
					if( $order_size > $base_bal )
						echo " -> *** base balance of $base_bal $base_cur is too low for min sell order size of $order_size $base_cur at sell price of $sell_price $quote_cur \n";
					else {
						$sell = $Adapter->sell( $market, $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
						sleep(1);
						if( isset( $sell['message'] ) ){ //error...
							print_r( $sell );
						} else {
							echo " -> *** sell order appears to have been placed succesfully \n";
							$balances[ $base_cur ]['available'] = $balances[ $base_cur ]['available'] - $order_size;
						}
					}
				} else {
					echo " -> cancelled because spread of $spread is too low\n";
				}

				echo "\n\n -> ### fininishing buy/sell sequence\n\n";
				sleep(1);

			}
		}
	}

?>
