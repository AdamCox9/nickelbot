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

			//get all currencies:
			$currency_file = $file . "/currencies.txt";
			$currencies = $Adapter->get_currencies();
			if( ! file_exists( $currency_file ) )
				file_put_contents( $currency_file, json_encode( $currencies ) );

			//get all markets:
			$market_file = $file . "/markets.txt";
			$markets = $Adapter->get_markets();
			if( ! file_exists( $market_file ) )
				file_put_contents( $market_file, json_encode( $markets ) );

			//_____make dir for market_summaries if not there already
			$ms_file = "$file/market_summaries";
			if( ! file_exists( $ms_file ) )
				mkdir( $ms_file, 0755 );

			//get all market summaries:
			foreach( $markets as $market ) {
				$market_summary_file = $file . "/market_summaries/$market.txt";
				if( ! file_exists( $market_summary_file ) ) {
					$market_summary = $Adapter->get_market_summary( $market );
					file_put_contents( $market_summary_file, json_encode( $market_summary ) );
				}
			}
			
			//get all balances:
			$balances_file = $file . "/balances.txt";
			if( ! file_exists( $balances_file ) ) {
				$balances = $Adapter->get_balances();
				file_put_contents( $balances_file, json_encode( $balances ) );
			}

			//get all open orders:
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
