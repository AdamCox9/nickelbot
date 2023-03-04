<?PHP

	/*****
		Etc: print some human readable data.
	 *****/

	function human_readable_summary( $Adapters ) {

		echo "\n\nAll Exchanges(" . sizeof( $Adapters ) . ")\n";
		foreach( $Adapters as $Adapter ) {

			echo "*** human_readable_summary: " . get_class( $Adapter ) . " ***\n";

			$currencies		= $Adapter->get_currencies( );
			$markets		= $Adapter->get_markets( );
			//$market_summaries	= $Adapter->get_market_summaries( );
			$balances		= $Adapter->get_balances( );

			echo "\n\nAll Currencies(" . sizeof( $currencies ) . ")\n";
			foreach( $currencies as $currency ) {
				echo $currency . ", ";
			}

			echo "\n\n***All Markets(" . sizeof( $markets ) . ")***\n";
			foreach( $markets as $market ) {
				echo $market . ", ";
			}

			echo "\n\n***All Balances***\n";
			foreach( $balances as $currency => $balance ) {
				if( $balance['total'] > 0 ) {
					echo "$currency Balance: " . $balance['total'] . "\n";

					//How to accurately get BTC market across all Adapters? 
					//$market_summary = $Adapter->get_market_summary( $market );
					//echo "Last Price: " . $market_summary['last'] . "\n";
					//echo "Bitcoin Value: " . $market_summary['last']
				}
			}

		}

	}

?>
