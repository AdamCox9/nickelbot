<?PHP

	/*****

		@Author Adam Cox

		This bot is used to test the integrity of the Adapter for each exchange.

	 *****/

	function run_tests( $Adapters, $Tester )
	{
		foreach( $Adapters as $Adapter ) {
			echo "*** run_tests: " . get_class( $Adapter ) . " ***\n";

			echo " -> getting info\n";
			$info = $Adapter->get_info();
			print_r( $info );
			$Tester->test( 'info', $info );
			die( "TEST" );
			
			echo " -> getting currencies\n";
			$currencies = $Adapter->get_currencies();
			//print_r( $currencies );
			$Tester->test( 'currencies', $currencies );
			//die( "TEST" );
			
			echo " -> getting balance for sample currency\n";
			$currency = $currencies[ rand( 0, sizeof( $currencies ) - 1 ) ];//test a random currency...
			//print_r( $currency );
			$balance = $Adapter->get_balance( $currency );
			//print_r( $balance );
			$Tester->test( 'balance', $balance );
			//die( "TEST" );

			echo " -> getting markets\n";
			$markets = $Adapter->get_markets();
			//print_r( $markets );
			$Tester->test( 'markets', $markets );
			//die( "TEST" );

			echo " -> getting first market to test with\n";
			$market = $markets[ rand( 0, sizeof( $markets ) - 1 ) ];//test a random market...

			echo " -> getting market summary for random market to test with\n";
			$market_summary = $Adapter->get_market_summary( $market );
			//print_r( $market_summary );
			$Tester->test( 'market_summaries', array( $market_summary ) );
			//die( 'TEST' );

			//TODO run test on get_ohlc, get_trades, get_orderbook, get_spread

			echo " -> getting open orders for test market\n";
			$open_orders = $Adapter->get_open_orders( $market );
			//print_r( $open_orders );
			$Tester->test( 'open_orders', $open_orders );
			//die( 'TEST' );

			echo " -> getting completed orders for test market\n";
			$completed_orders = $Adapter->get_completed_orders( $market );
			//print_r( $completed_orders );
			$Tester->test( 'completed_orders', $completed_orders );
			//die( 'TEST' );

			echo " -> getting 5 entries from first orderbook\n";
			$Tester->test( 'orderbook', $Adapter->get_orderbook( $market, 5 ) );

			echo " -> getting deposit and withdrawal history\n";
			$Tester->test( 'deposits_withdrawals', $Adapter->get_deposits_withdrawals() );


			echo " -> getting deposit history\n";
			$Tester->test( 'deposits', $Adapter->get_deposits() );

			echo " -> getting withdrawal history\n";
			$Tester->test( 'withdrawals', $Adapter->get_withdrawals() );

			echo " -> getting balances for all currencies\n";
			$Tester->test( 'balances', $Adapter->get_balances() );

			//_____TOO SLOW: just test single entry

			//echo " -> getting market summaries\n";
			//$market_summaries = $Adapter->get_market_summaries();
			//$Tester->test( 'market_summaries', array( $market_summaries ) );

			//echo " -> cancelling all orders\n";
			//$Tester->test( 'cancel_all', $Adapter->cancel_all() );

			//_____TODO: test a buy order then cancel it
			//echo " -> making a buy order\n";
			//$buy = $Adapter->buy(  );
			//print_r( $buy );
			//$Tester->test( 'buy', array( $buy ) );

			//_____TODO: test a sell order then cancel it
			//echo " -> making a sell order\n";
			//$sell = $Adapter->sell(  );
			//$Tester->test( 'sell', array( $sell ) );
			//print_r( $sell );

			//_____TODO: make it so this only gets trades for one market
			echo " -> getting all recent trades for test market\n";
			$Tester->test( 'trades', $Adapter->get_trades( $market, array( 'time' => 60, 'limit' => 10  ) ) );

			echo " -> generating deposit addresses\n";
			$Tester->test( 'deposit_addresses', $Adapter->deposit_addresses() );
			
			echo " -> getting all positions\n";
			//$Tester->test( 'positions', $Adapter->get_positions() );

			//_____Utilities: they should have some utility

			//echo " -> getting volumes\n";
			//$Tester->test( 'volumes', Utilities::get_total_volumes( $Adapter->get_market_summaries() ) );

			//echo " -> getting worths\n";
			//$Tester->test( 'worth', Utilities::get_worth( $Adapter->get_balances(), $Adapter->get_market_summaries() ) );

		}
	}

?>
