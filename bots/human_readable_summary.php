<?PHP

	/*****
		Etc: print some human readable data.
	 *****/

	function human_readable_summary( $exchanges, $currencies, $markets, $worths, $volumes ) {
		sort( $exchanges );
		echo "\n\nAll Exchanges(".sizeof($exchanges).")\n";
		foreach( $exchanges as $exchange) {
			echo $exchange . ", ";
		}

		echo "\n\nAll Currencies(".sizeof($currencies).")\n";
		foreach( $currencies as $key => $currency_exchange) {
			echo "\n\n ***$key*** \n\n";
			foreach( $currency_exchange as $currency ) {
				echo $currency . ", ";
			}
		}

		echo "\n\n***All Markets(".sizeof($markets).")***\n";
		foreach( $markets as $key => $market_exchange ) {
			echo "\n\n ***$key*** \n\n";
			foreach( $market_exchange as $market ) {
				echo $market . ", ";
			}
		}

		echo "\n\n***All Worths***\n";
		$total_worth = 0;
		foreach( $worths as $key => $worth) {
			echo "$key BTC Balance: " . $worth['btc_worth'] . "\n";
			$total_worth += $worth['btc_worth'];
		}
		echo "\n\n#####Total Worth: $total_worth#####\n\n";

		echo "\n\n***All Volumes***\n";
		$total_volume = 0;
		foreach( $volumes as $key => $volume) {
			echo "$key BTC Volume: " . $volume['total_volume'] . "\n";
			$total_volume += $volume['total_volume'];
		}
		echo "\n\n#####Total Volume: $total_volume#####\n\n";

	}

?>