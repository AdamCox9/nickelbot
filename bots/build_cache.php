<?PHP

	function build_cache( $Adapters ) 
	{
		foreach( $Adapters as $Adapter ) {

			//_____make dir for each exchange in cache dir:
			$file = "cache/" . strtolower( str_replace( "Adapter", "", get_class( $Adapter ) ) );
			if( ! file_exists( $file ) )
				mkdir( $file, 0755 );

			//get all currencies:
			$currency_file = $file . "/currencies.txt";
			if( ! file_exists( $currency_file ) ) {
				$currencies = $Adapter->get_currencies();
				file_put_contents( $currency_file, json_encode( $currencies ) );
			}

			//get all markets:
			$market_file = $file . "/markets.txt";
			if( ! file_exists( $market_file ) ) {
				$markets = $Adapter->get_markets();
				file_put_contents( $market_file, json_encode( $markets ) );
			}

			//get all open orders for user:

			//get all completed orders for user:

			//get orderbook for each currency:

		}
	}

?>