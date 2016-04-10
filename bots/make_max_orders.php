<?PHP

	function make_max_orders( $Adapters ) {
		foreach( $Adapters as $Adapter ) {
			$balances = $Adapter->get_balances();
			$market_summaries = $Adapter->get_market_summaries();
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['frozen'] )
					continue;

				$price_precision = $market_summary['price_precision'];

				if( $market_summary['bid'] < $market_summary['low'] )
					$buy_price = $market_summary['bid'];
				else
					$buy_price = $market_summary['low'];

				if( $market_summary['ask'] > $market_summary['high'] )
					$sell_price = $market_summary['ask'];
				else
					$sell_price = $market_summary['high'];

				/*
					TODO make sure the spread leaves room for at least 5% or so...
					[low] => 2.60037475
					[high] => 2.60037475

				*/

				$curs_bq = explode( "-", $market_summary['market'] );
				$base_cur = $curs_bq[0];
				$quote_cur = $curs_bq[1];

				$buy_price = number_format( $buy_price * 0.9, 32, '.', '' );
				$sell_price = number_format( $sell_price * 1.1, 32, '.', '' );

				//Don't sell the currency we are buying with
				$quote_curs = array( /*"BTC", "LTC", "CNY", "NXT", "USD", "XRP"*/ );

				if( $buy_price > 0 && ! in_array( $base_cur, $quote_curs ) ) {
					$order_size = Utilities::surch( array( "currency" => $quote_cur, "type" => "exchange" ), $balances );
					if( sizeof( $order_size ) > 0 ) {
						$order_size = bcmul( $order_size[0]['total'], 0.5, 32 );
						$order_size = number_format( $order_size/$buy_price, $price_precision, '.', '' );

						if( $order_size < $market_summary['minimum_order_size_base'] )
							$order_size = $market_summary['minimum_order_size_base'];
	
						$total_buy_cost = number_format( bcmul( $order_size, $buy_price, 32 ), $price_precision, '.', '' );
						echo " -> Buying $order_size of $base_cur in {$market_summary['market']} on {$market_summary['exchange']} costing $total_buy_cost of $quote_cur at $buy_price $quote_cur per $base_cur\n";
						print_r( $Adapter->buy( $market_summary['market'], $order_size, $buy_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) ) );
						echo "\n\n";
					}
				}

				if( $sell_price > 0 && ! in_array( $base_cur, $quote_curs ) ) {
					$order_size = Utilities::surch( array( "currency" => $base_cur, "type" => "exchange" ), $balances );
					if( sizeof( $order_size ) > 0 ) {
						$order_size = bcmul( $order_size[0]['available'], 0.9999, 32 );
						$order_size = number_format( $order_size, $price_precision, '.', '' );

						if( $order_size < $market_summary['minimum_order_size_base'] )
							$order_size = $market_summary['minimum_order_size_base'];

						$total_sell_gain = number_format( bcmul( $order_size, $sell_price, 32 ), $price_precision, '.', '');
						echo " -> Selling $order_size of $base_cur in {$market_summary['market']} on {$market_summary['exchange']} gaining $total_sell_gain of $quote_cur at $sell_price $quote_cur per $base_cur\n";
						print_r( $Adapter->sell( $market_summary['market'], $order_size, $sell_price, 'limit', array( 'market_id' => $market_summary['market_id'] ) ) );
						echo "\n\n";
					}
				}

			}
		}
	}

?>