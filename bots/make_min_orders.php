<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy orders on the spread.
		It will set buy orders for every trading pair available for each adapter where balances permit meeting criteria below.

		TODO
		 - Allow to specify certain trading pairs instead of hitting all of them

	*/

	function make_min_orders( $Adapters = array(), $_CONFIG = array() ) {

		foreach( $Adapters as $Adapter ) {
			echo "*** make_min_orders: " . get_class( $Adapter ) . " ***\n";

			//_____get the markets to loop over:
			echo " -> getting market summaries \n";
			$market_summaries = $Adapter->get_market_summaries();

			echo " -> got " . count( $market_summaries ) . " markets \n";

			//Print random sample market for debugging:
			print_r( $market_summaries[ array_rand( $market_summaries ) ] );
			//die( "TEST" );

			echo " -> getting balances \n";
			$balances = $Adapter->get_balances( );

			//Print balances for debugging:
			//print_r( $balances );

			//Only use $_CONFIG['QUOTE_CURRENCY'] pairs:
			$filtered_market_summaries_by_btc = [];
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['quote'] != $_CONFIG['QUOTE_CURRENCY'] ) continue;
				array_push( $filtered_market_summaries_by_btc, $market_summary );
			}

			echo " -> narrowed down to " . count( $filtered_market_summaries_by_btc ) . " BTC markets \n";

			//Sort by volume:
			$market_summaries = $filtered_market_summaries_by_btc;
			usort( $market_summaries, fn( $a, $b ) => $a['quote_volume'] <=> $b['quote_volume'] );

			//Get top markets by volume as specified by $_CONFIG['FILTER_BY_TOP_VOLUME']:
			if( count( $market_summaries ) > $_CONFIG['FILTER_BY_TOP_VOLUME'] )
				array_splice( $market_summaries, 0, count( $market_summaries ) - $_CONFIG['FILTER_BY_TOP_VOLUME'] );

			echo " -> narrowed down to " . count( $market_summaries ) . " by top volume \n";

			//Sort by percent change as specified by $_CONFIG['PRICE_CHANGE_DIRECTION']:
			if( $_CONFIG['PRICE_CHANGE_DIRECTION'] == "ASC" )
				usort( $market_summaries, fn( $a, $b ) => $b['percent_change'] <=> $a['percent_change'] );
			elseif( $_CONFIG['PRICE_CHANGE_DIRECTION'] == "ASC" )
				usort( $market_summaries, fn( $a, $b ) => $a['percent_change'] <=> $b['percent_change'] );

			//Get top markets by percent_change as specified by $_CONFIG['FILTER_BY_TOP_PRICE_CHANGE']:
			if( count( $market_summaries ) > $_CONFIG['FILTER_BY_TOP_PRICE_CHANGE'] )
				array_splice( $market_summaries, 0, count($market_summaries) - $_CONFIG['FILTER_BY_TOP_PRICE_CHANGE'] );

			echo " -> narrowed down to " . count( $market_summaries ) . " by percent change \n";
			echo "\n";

			//Loop over remaining markets for order placement:
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] ) { echo " -> market is frozen\n"; continue; } //TODO filter out frozen markets before filtering above

				//_____get currencies/balances:
				$market = $market_summary['market'];

				$base_cur = $market_summary['base'];
				$quote_cur = $market_summary['quote'];
				$base_bal = isset( $balances[ $base_cur ] ) ? $balances[ $base_cur ]['available'] : 0;
				$quote_bal = isset( $balances[ $quote_cur ] ) ? $balances[ $quote_cur ]['available'] : 0;
				$ask = $market_summary['ask'];
				$bid = $market_summary['bid'];
				$min_order_base = $market_summary['minimum_order_size_base'];
				$min_order_quote = $market_summary['minimum_order_size_quote'];
				$precision = $market_summary['price_precision'];						//_____significant digits for base currency - "1.12" has 2 as precision //TODO: find precision for quote currency
				$epsilon = 1 / pow( 10, $precision );								//_____smallest unit of base currency that exchange recognizes: if precision is 3, then it is 0.001.
				$buy_price = bcmul( $bid, $_CONFIG['BUY_ORDER_PERCENT_DIFF'], $precision);			//_____set order at X percent below bid //$buy_price = $market_summary['bid'] + $epsilon; //_____buy at one unit above highest bid.
				$sell_price = bcmul( $ask, $_CONFIG['SELL_ORDER_PERCENT_DIFF'], $precision);			//$market_summary['ask'] - $epsilon; //_____sell at one unit below lowest ask.
				$spread = $sell_price - $buy_price;								//_____difference between highest bid and lowest ask.

				echo " -> " . get_class( $Adapter ) . ":$market \n";
				echo " -> base currency: $base_cur, balance: $base_bal \n";
				echo " -> quote currency: $quote_cur, balance: $quote_bal \n";
				echo " -> bid: $bid \n";
				echo " -> ask: $ask \n";
				echo " -> min order size base: $min_order_base \n";
				echo " -> min order size quote: $min_order_quote \n";
				echo " -> precision: $precision \n";
				echo " -> epsilon: (min unit of base currency): $epsilon \n";
				echo " -> buy price: $buy_price \n";
				echo " -> sell price: $sell_price \n";
				echo " -> spread: $spread \n";

				if( $buy_price < $sell_price ) { //just in case...

					//_____Do the Buy:

					$order_size = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $buy_price, 8 );
					$order_size = bcmul( $order_size, $_CONFIG['ORDER_SIZE_MULTIPLIER'], 8 );
					echo " -> *** attempt to buy $order_size $base_cur in $market for $buy_price $quote_cur costing " . bcmul( $order_size, $buy_price, 8 ) . " $quote_cur with quote balance of $quote_bal \n";
					if( $order_size * $buy_price > $quote_bal )
						echo " -> *** quote balance of $quote_bal $quote_cur is too low for min buy order size of $order_size $base_cur at buy price of $buy_price $quote_cur \n";
					elseif( $_CONFIG['DIRECTION'] == "BUY" || $_CONFIG['DIRECTION'] == "BOTH" ) {
						$buy = $Adapter->buy( $market, $order_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
						if( isset( $buy['CODE'] ) || isset( $buy['message'] ) ) { //error codes
							print_r( $buy );
						} else {
							echo " -> *** buy order appears to have been placed succesfully \n";
							$balances[ $quote_cur ]['available'] = $balances[ $quote_cur ]['available'] - bcmul( $order_size, $buy_price, 8 );
						}
						echo " -> ### fininishing buy order attempt\n";
					} else {
						echo " -> skipping creating buy order for $market because config was set to " . $_CONFIG['DIRECTION'] . "\n";
					}

					//_____Do the Sell:

					$order_size = Utilities::get_min_order_size( $min_order_base, $min_order_quote, $sell_price, 8 );
					$order_size = bcmul( $order_size, $_CONFIG['ORDER_SIZE_MULTIPLIER'], 8 );
					echo " -> *** attempt to sell $order_size $base_cur in $market for $sell_price $quote_cur costing " . bcmul( $order_size, $sell_price, 8 ) . " $base_cur with base balance of $base_bal \n";
					if( $order_size * $sell_price > $base_bal )
						echo " -> *** base balance of $base_bal $base_cur is too low for min sell order size of $order_size $base_cur at sell price of $sell_price $quote_cur \n";
					elseif( $_CONFIG['DIRECTION'] == "SELL" || $_CONFIG['DIRECTION'] == "BOTH" ) {
						$sell = $Adapter->sell( $market, $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
						if( isset( $sell['CODE'] ) || isset( $sell['message'] ) ) { //error codes
							print_r( $sell );
						} else {
							echo " -> *** sell order appears to have been placed succesfully \n";
							$balances[ $base_cur ]['available'] = $balances[ $base_cur ]['available'] - bcmul( $order_size, $sell_price, 8 );
						}
						echo " -> ### fininishing sell order attempt\n";
					} else {
						echo " -> skipping creating sell order for $market because config was set to " . $_CONFIG['DIRECTION'] . "\n";
					}

				} else {
					echo " -> cancelled because spread of $spread is too low\n";
				}
				
				echo "\n";
			}

			echo " -> finished executing " . get_class( $Adapter ) . "\n\n";
		}
	}

?>
