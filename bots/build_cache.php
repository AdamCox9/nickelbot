<?PHP

	/*
		This bot will save all the data from the REST API and save it to a cache folder followed by exchange name: cache/{exchange}/{file_name}
		This is not to be confused with the caching mechanism built into the Adapter classes.
		This is good to collect data and save it to disk for future usage.
		This is required to be ran prior to other bots that depend on market_summaries data.
	*/

	function build_cache( $Adapters ) 
	{
		foreach( $Adapters as $Adapter ) {
			echo "*** build_cache: " . get_class( $Adapter ) . " ***\n";

			$file = "cache";
			if( ! file_exists( $file ) )
				mkdir( $file, 0755 );

			$exchange = strtolower( str_replace( "Adapter", "", get_class( $Adapter ) ) );

			//_____make dirs for each exchange in cache dir if not already there:
			$file = "cache/$exchange";
			if( ! file_exists( $file ) )
				mkdir( $file, 0755 );

			//get currencies:
			$currency_file = $file . "/currencies.txt";
			$currencies = $Adapter->get_currencies();
			if( ! file_exists( $currency_file ) )
				file_put_contents( $currency_file, json_encode( $currencies ) );

			//get markets:
			$market_file = $file . "/markets.txt";
			$markets = $Adapter->get_markets();
			if( ! file_exists( $market_file ) )
				file_put_contents( $market_file, json_encode( $markets ) );

			//_____make dir for market_summaries if not there already
			$market_summary_file = "$file/market_summaries";
			if( ! file_exists( $market_summary_file ) )
				mkdir( $market_summary_file, 0755 );

			//get market summaries:
			foreach( $markets as $market ) {
				$market_summary_file = $file . "/market_summaries/$market.txt";
				if( ! file_exists( $market_summary_file ) ) {
					$market_summary = $Adapter->get_market_summary( $market );
					file_put_contents( $market_summary_file, json_encode( $market_summary ) );
				}
			}

			//_____make dir for ohlc if not there already
			$ohlc_file = "$file/ohlc";
			if( ! file_exists( $ohlc_file ) )
				mkdir( $ohlc_file, 0755 );

			//get OHLC:
			foreach( $markets as $market ) {
				$ohlc_file = $file . "/ohlc/$market.txt";
				if( ! file_exists( $ohlc_file ) ) {
					$ohlc = $Adapter->get_ohlc( $market );
					file_put_contents( $ohlc_file, json_encode( $ohlc ) );
				}
			}
			
			//_____make dir for ohlc if not there already
			$orderbook_file = "$file/orderbook";
			if( ! file_exists( $orderbook_file ) )
				mkdir( $orderbook_file, 0755 );

			//get orderbooks:
			foreach( $markets as $market ) {
				$orderbook_file = $file . "/orderbook/$market.txt";
				if( ! file_exists( $orderbook_file ) ) {
					$orderbook = $Adapter->get_orderbook( $market );
					file_put_contents( $orderbook_file, json_encode( $orderbook ) );
				}
			}
			
			//_____make dir for trades if not there already
			$trades_file = "$file/trades";
			if( ! file_exists( $trades_file ) )
				mkdir( $trades_file, 0755 );

			//get trades:
			foreach( $markets as $market ) {
				$trades_file = $file . "/trades/$market.txt";
				if( ! file_exists( $trades_file ) ) {
					$trades = $Adapter->get_trades( $market );
					file_put_contents( $trades_file, json_encode( $trades ) );
				}
			}
			
			//_____make dir for spread if not there already
			$spread_file = "$file/spread";
			if( ! file_exists( $spread_file ) )
				mkdir( $spread_file, 0755 );

			//get spread:
			foreach( $markets as $market ) {
				$spread_file = $file . "/spread/$market.txt";
				if( ! file_exists( $spread_file ) ) {
					$spread = $Adapter->get_spread( $market );
					file_put_contents( $spread_file, json_encode( $spread ) );
				}
			}
			
			//get balances:
			$balances_file = $file . "/balances.txt";
			if( ! file_exists( $balances_file ) ) {
				$balances = $Adapter->get_balances();
				file_put_contents( $balances_file, json_encode( $balances ) );
			}

			//get open orders:
			$open_orders_file = $file . "/open_orders.txt";
			if( ! file_exists( $open_orders_file ) ) {
				$open_orders = $Adapter->get_open_orders( );
				file_put_contents( $open_orders_file, json_encode( $open_orders ) );
			}

			//get all completed orders:
			$completed_orders_file = $file . "/completed_orders.txt";
			if( ! file_exists( $completed_orders_file ) ) {
				$completed_orders = $Adapter->get_completed_orders( );
				file_put_contents( $completed_orders_file, json_encode( $completed_orders ) );
			}

		}
	}

?>
