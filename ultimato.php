<?php

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "config_safe.php" );

	try{

		/*****
			FYI: Adapters are facades around wrapper library for native restful API's
			BOTS: functions are an app!
			TODO: make a Bot class?
			NOTE: do not print_r the $Adapter class because it may leak keys and secrets on the web.

			Here are some sample bots being used in such a simple way:

		 *****/

		run_tests( $Adapters, $Tester );
		//make_max_orders( $Adapters );
		//make_min_orders( $Adapters );
		//make_extreme_orders( $Adapters );
		//make_ema_orders( $Adapters );
		//make_deposit_addresses( $Adapters, $Tester ); //todo: get the Tester object out of here and put in run_tests above...
		//human_readable_summary( $exchanges, $currencies, $markets, $worths, $volumes );
		//disperse_funds( array( $Adapters['Btce'] ), array( $Adapters['Bitfinex'], $Adapters['Bitstamp'], $Adapters['Bittrex'], $Adapters['Bter'], $Adapters['Poloniex'] ), 'BTC', '0.02222222' ); //$from_arr, $to_arr, $curr_arr

	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 