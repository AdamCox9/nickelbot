<?php

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "config_safe.php" );

	try{
		/*****
			Adapters are facades around wrapper library for native restful API's
		 *****/
		/*$currencies = [];
		$markets = [];
		$market_summaries = [];
		foreach( $Adapters as $Adapter ) { //this loop kind of for testing...
			$exchange_name = get_class( $Adapter );
			echo "******* $exchange_name ******\n";
			array_push( $exchanges, $exchange_name );
			
			echo " -> getting currencies\n";
			$currencies[ $exchange_name ] = $Adapter->get_currencies();
			$Tester->test( 'currencies', $currencies );

			echo " -> getting markets\n";
			$markets[ $exchange_name ] = $Adapter->get_markets();
			$Tester->test( 'markets', $markets );

			echo " -> getting market summaries\n";
			$market_summaries[ $exchange_name ] = $Adapter->get_market_summaries();
			$Tester->test( 'market_summaries', $market_summaries );

			echo " -> getting balances\n";
			$Tester->test( 'balances', $Adapter->get_balances() );

			echo " -> generating deposit addresses\n";
			$Tester->test( 'deposit_addresses', $Adapter->deposit_addresses() );
			
			echo " -> getting open orders\n";
			$Tester->test( 'open_orders', $Adapter->get_open_orders() );

			echo " -> getting completed orders\n";
			$Tester->test( 'completed_orders', $Adapter->get_completed_orders() );

			echo " -> getting all recent trades\n";
			$Tester->test( 'trades', $Adapter->get_all_trades( $time = 0 ) );

			echo " -> getting some depth of orderbook\n";
			$Tester->test( 'orderbooks', $Adapter->get_orderbooks( $depth = 20 ) );

			echo " -> cancelling all orders\n";
			$Tester->test( 'cancel_all', $Adapter->cancel_all() );

			//_____Utilities: they do not directly access native API libraries where as Adapters must access native API or self

			echo " -> getting volumes\n";
			$volumes[ get_class( $Adapter ) ] = Utilities::get_total_volumes( $Adapter->get_market_summaries() );

			echo " -> getting worths\n";
			$worths[ get_class( $Adapter ) ]= Utilities::get_worth( $Adapter->get_balances(), $Adapter->get_market_summaries() );

		}*/

		/*****
			Bots: like an app.
		 *****/

		//make_max_orders( $Adapters );
		//make_min_orders( $Adapters );
		//make_extreme_orders( $Adapters );
		//make_ema_orders( $Adapters );
		make_deposit_addresses( $Adapters, $Tester );
		//human_readable_summary( $exchanges, $currencies, $markets, $worths, $volumes );
		//disperse_funds( array( $Adapters['Btce'] ), array( $Adapters['Bitfinex'], $Adapters['Bitstamp'], $Adapters['Bittrex'], $Adapters['Bter'], $Adapters['Poloniex'] ), 'BTC', '0.02222222' ); //$from_arr, $to_arr, $curr_arr

	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 