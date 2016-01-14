<?PHP

	function make_min_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			$market_summaries = $Adapter->get_market_summaries();
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] )
					continue;

				$price_precision = $market_summary['price_precision'];

				if( $market_summary['bid'] < $market_summary['low'] )
					if( $market_summary['low'] == 0 )
						$buy_price = bcmul( $market_summary['bid'], 0.95, $price_precision );
					else
						$buy_price = $market_summary['bid'];
				else
					$buy_price = $market_summary['low'];

				if( $market_summary['ask'] > $market_summary['high'] )
					if( $market_summary['high'] == 0 )
						$sell_price = bcmul( $market_summary['ask'], 1.1, $price_precision );
					else
						$sell_price = $market_summary['ask'];
				else
					$sell_price = $market_summary['high'];

				/*
					TODO make sure the spread leaves room for at least 5% or so...
					[low] => 2.60037475
					[high] => 2.60037475

				*/

				$curs_bq = explode( "-", $market_summary['pair'] );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];

				$buy_price = number_format( $buy_price, 32, '.', '' );
				$sell_price = number_format( $sell_price, 32, '.', '' );

				if( $buy_price > 0 ) {
					if( ! isset( $market_summary['minimum_order_size_base'] ) )
						$order_size = bcdiv( $market_summary['minimum_order_size_quote'], $buy_price, 32 );
					else {
						$order_size = $market_summary['minimum_order_size_base'];
						$order_size = number_format( $order_size, $price_precision, '.', '' );
					}

					$total_buy_cost = number_format( bcmul( $order_size, $buy_price, 32 ), $price_precision, '.', '' );
					echo " -> Buying $order_size of $base_cur in {$market_summary['pair']} on {$market_summary['exchange']} costing $total_buy_cost of $quote_cur at $buy_price of $quote_cur per $base_cur\n\n";
					$Adapter->buy( $market_summary['pair'], $order_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				}

				if( $sell_price > 0 ) {
					if( ! isset( $market_summary['minimum_order_size_base'] ) )
						$order_size = bcdiv( $market_summary['minimum_order_size_quote'], $sell_price, 32 );
					else {
						$order_size = $market_summary['minimum_order_size_base'];
						$order_size = number_format( $order_size, $price_precision, '.', '' );
					}

					$total_sell_gain = number_format( bcmul( $order_size, $sell_price, 32 ), $price_precision, '.', '');
					echo " -> Selling $order_size of $base_cur in {$market_summary['pair']} on {$market_summary['exchange']} gaining $total_sell_gain of $quote_cur at $sell_price of $quote_cur per $base_cur\n\n";
					$Adapter->sell( $market_summary['pair'], $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) );
				}

			}
		}
	}

?>