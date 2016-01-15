<?php

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );

	require_once( "config.php" );

	try{
		/*****
			Adapters: facade around wrapper library for native restful API's
		 *****/
		foreach( $Adapters as $Adapter ) {
			echo "\n\n*******" . get_class( $Adapter ) . "******\n\n";

			echo " -> exchange name\n";
			$exchanges = array_merge( $exchanges, array( get_class( $Adapter ) ) );
			
			echo " -> getting currencies\n";
			$currencies = array_unique( array_merge( $currencies, $Adapter->get_currencies() ) );
			$Tester->test_currencies( $currencies );
			
			echo " -> getting markets\n";
			$markets = array_unique( array_merge( $markets, $Adapter->get_markets() ) );
			$Tester->test_markets( $markets );

			echo " -> getting market summaries\n";
			$market_summaries = array_merge( $market_summaries, $Adapter->get_market_summaries() );
			$Tester->test_market_summaries( $market_summaries );

			/*echo " -> getting balances\n";
			$balances = array_merge( $balances, $Adapter->get_balances() );
			$Tester->test_balances( $balances );

			echo " -> generating deposit addresses\n";
			$deposit_addresses = array_merge( $deposit_addresses, $Adapter->deposit_addresses() );
			$Tester->test_deposit_addresses( $deposit_addresses );
			
			echo " -> getting open orders\n";
			$open_orders = array_merge( $open_orders, $Adapter->get_open_orders() );
			$Tester->test_open_orders( $open_orders );

			echo " -> getting completed orders\n";
			$completed_orders = array_merge( $completed_orders, $Adapter->get_completed_orders() );
			$Tester->test_completed_orders( $completed_orders );

			echo " -> getting all recent trades\n";
			$trades = array_merge( $trades, $Adapter->get_all_trades( $time = 0 ) );
			$Tester->test_trades( $trades );

			echo " -> getting some depth of orderbook\n";
			$orderbooks = array_merge( $orderbooks, $Adapter->get_orderbooks( $depth = 20 ) );
			$Tester->test_orderbooks( $orderbooks );*/

			/*echo " -> cancelling all orders\n";
			$cancel_all = $Adapter->cancel_all();
			$Tester->test_cancel_all( $cancel_all );*/

			/*****
				Utilities: they do not directly access native API libraries where as Adapters must access native API or self
			 *****/

			/*echo " -> getting volumes\n";
			$volumes[ get_class( $Adapter ) ] = Utilities::get_total_volumes( $Adapter->get_market_summaries() );

			echo " -> getting worths\n";
			$worths[ get_class( $Adapter ) ]= Utilities::get_worth( $Adapter->get_balances(), $Adapter->get_market_summaries() );
			*/

			sleep(1);
		}

		/*****
			Bots: like an app.
		 *****/

		//make_max_orders( $Adapters );
		//make_min_orders( $Adapters );
		//make_extreme_orders( $Adapters );
		//make_ema_orders( $Adapters );
		//make_deposit_addresses( $Adapters );
		//liquidate_exchange( array( $Adapters['Btce'] ), array( $Adapters['Poloniex'] ) ); //$from_arr, $to_arr
		human_readable_summary( $currencies, $markets, $worths, $volumes );

	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 