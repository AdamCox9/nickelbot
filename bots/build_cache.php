<?PHP

	/*
		This bot will save all the data from the REST API and save it to a cache folder followed by exchange name: cache/{exchange}/{file_name}
		Need to create a cache folder and allow it write permissions.

	*/

	function build_cache( $Adapters ) 
	{
		foreach( $Adapters as $Adapter ) {

			$exchange = strtolower( str_replace( "Adapter", "", get_class( $Adapter ) ) );

			//_____make dirs for each exchange in cache dir if not already there:
			$file = "cache/$exchange";
			if( ! file_exists( $file ) )
				mkdir( $file, 0755 );

			$open_orders_file = "cache/$exchange/open_orders";
			if( ! file_exists( $open_orders_file ) )
				mkdir( $open_orders_file, 0755 );

			$completed_orders_file = "cache/$exchange/completed_orders";
			if( ! file_exists( $completed_orders_file ) )
				mkdir( $completed_orders_file, 0755 );

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

			//get all balances:
			$balances_file = $file . "/balances.txt";
			if( ! file_exists( $balances_file ) ) {
				$balances = $Adapter->get_balances();
				file_put_contents( $balances_file, json_encode( $balances ) );
			}

			//get all open orders for each market:
			foreach( $markets as $market )
				if( ! file_exists( $open_orders_file . "/open_orders_$market.txt" ) ) {
					$open_orders = $Adapter->get_open_orders( $market );
					file_put_contents( $open_orders_file . "/open_orders_$market.txt", json_encode( $open_orders ) );
				}

			//get all completed orders for each market:
			foreach( $markets as $market )
				if( ! file_exists( $completed_orders_file . "/completed_orders_$market.txt" ) ) {
					$completed_orders = $Adapter->get_completed_orders( $market );
					file_put_contents( $completed_orders_file . "/completed_orders_$market.txt", json_encode( $completed_orders ) );
				}

		}
	}

?>
