<?PHP

	// Utility functions:
	// These should be compatible with all of the data returned from Adapter objects.
	// They should not use the Adapter objects themselves, but they should accept arrays returned from Adapter APIs
	// This can be used to perform custom calculations on data.

	class Utilities {

		public static function get_min_order_size( $min_order_size_base, $min_order_size_quote, $price, $precision ) {
			if( $price == 0 )
				return 0;
			if( is_null( $min_order_size_base ) )
				return bcdiv( $min_order_size_quote, $price, 10 ); //fixes rounding error: 0.00000999999 ~> 0.00001000
			else
				return $min_order_size_base;
		}

		public static function get_total_volumes( $market_summaries ) {

			/*
				This MUST return volume for each of the quote currencies
				Then a specific market for that currency will have a percentage of that market only
				BTC is just another currency that could be used as a base or quote currency in a market

				This should also return such things as Buy and Sell volume
				It should also return orderbook volume, but the orderbook volume might be taken care of somewhere else
			*/


			//_____this don't even work right...

			$results = [];
			$total_volume = 0;
			foreach( $market_summaries as $market_summary ) {
				if( strstr( $market_summary['market'], "-BTC" ) !== FALSE ) {
					$total_volume += $market_summary['quote_volume'];
					continue;
				}
				if( strstr( $market_summary['market'], "BTC-" ) !== FALSE ) {
					$total_volume += $market_summary['base_volume'];
					continue;
				}
				array_push( $results, array( 'market' => $market_summary['market'], 'base_volume' => $total_volume, 'quote_volume' => $total_volume ) );
				//TODO calculate then non-BTC markets too...
				/*$base_volume = $market_summary['base_volume'];
				$quote_volume = $market_summary['quote_volume'];
				$curs = explode( "-", $market_summary['pair'] );
				$base_cur = $curs[0];
				$quote_cur = $curs[1];
				$market_summary = Utilities::surch( "market", $base_cur."-BTC", $market_summaries );
				if( $market_summary ) {
					$total_volume += $quote_volume / $market_summary['last_price'];
					continue;
				}
				$market_summary = Utilities::surch( "market", "BTC-".$quote_cur, $market_summaries );
				if( $market_summary ) {
					$total_volume += $base_volume / $market_summary['last_price'];
					continue;
				}*/
			}
			return $results;
		}

		public static function surch( $needles, $haystack ) {
			$response = [];
			foreach( $haystack as $bail ) {
				$test = true;
				foreach( $needles as $key => $val ) {
					if( ! isset( $bail[$key] ) || $bail[$key] !== $val )
						$test = false;
				}
				if( $test )
					array_push( $response, $bail );
			}
			return $response;
		}

		public static function get_worth( $balances, $market_summaries ) {
			$btc_worth = 0;
			foreach( $balances as $balance ) {
				if( $balance['currency'] === "BTC" ) {
					$btc_worth += $balance['total'];
					continue;
				}
				$market_summary = Utilities::surch( array( "market" => $balance['currency']."-BTC" ), $market_summaries );
				if( sizeof( $market_summary ) > 0 ) {
					$btc_worth += $balance['total'] * $market_summary[0]['last_price'];
					continue;
				}
				$market_summary = Utilities::surch( array( "market" => "BTC-".$balance['currency'] ), $market_summaries );
				if( sizeof( $market_summary ) > 0 ) {
					$btc_worth += $balance['total'] / $market_summary[0]['last_price'];
					continue;
				}
			}
			return array( "btc_worth" => $btc_worth );
		}

		//Calculate avg_buy_price, avg_sell_price, loss, profit, breakeven sale price, etc...
		//for all buys & sells & withdrawals & transfers & deposits & etc...
		public static function analysis( $trades = array(), $time = 0 ){}

		//This function will parse through trades since $time finding the highest & lowest sell price
		public static function get_high_low( $trades = array(), $time = 0 ){}

		//This function will parse through trades and calculate the ema for $time
		//Maybe this is already implemented in PHP???
		public static function get_ema( $trades = array(), $time = 0 ){}

	}
?>