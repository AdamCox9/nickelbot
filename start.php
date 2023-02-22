<?php

	/*if( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] != '76.24.176.23' ) {
		if( ! isset( $_SERVER['USER'] ) || $_SERVER['USER'] !== "root" ) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}*/

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "config_safe.php" );

	try{

		/*****
			LIBRARIES: communicate with exchange API's directly. Should only be called from ADAPTERS.
			ADAPTERS: facades that wrap around LIBRARIES
			BOTS: functions are an app for now. Bots communicate with ADAPTERS but not LIBRARIES.
			CRYPTO_UTILITIES: functions that perform some type of calculations or transformations on data, but do not interact with ADAPTERS, LIBRARIES or BOTS
			GUI: it is a web app that communicates with ADAPTERS (similar to a bot but with a web UI). There can be many different GUI's.
			API: can make rest calls to them through HTTP requests like with AJAX from the default GUI.

			TODO: make a BOT class instead of using functions
			NOTE: do not print_r the $Adapter class because it may leak keys and secrets on the web.

			One way to run a bot would be to execute a php script in a cron job.
			Another way would be to make a while(1) loop that continuously runs.

			------

			Run a PHP process for each exchange from the terminal like this:
			php start.php
			 - or -
			php start.php Bitfinex > bitfinex_out.txt & php start.php Bitstamp > bitstamp_out.txt & php start.php Bittrex > bittrex_out.txt & php start.php Btce > btce_out.txt & php start.php Coinbase > coinbase_out.txt & php start.php Poloniex > poloniex_out.txt & 

			Kill the processes from the terminal like this:
			pkill -9 php

			Here are some sample bots being used in such a simple way:

		 *****/

		//build_cache( $Adapters );
		//run_tests( $Adapters, $Tester );
		//cancel_oldest_orders( $Adapters );
		//make_min_orders( $Adapters );
		//make_max_orders( $Adapters );
		//while( true ) {
			//light_show( $Adapters['Bittrex'], "PIVX-BTC"  );
		//}
		//make_deposit_addresses( $Adapters, $Tester ); //todo: get the Tester object out of here and put in run_tests above...
		//human_readable_summary( $exchanges, $currencies, $markets, $worths, $volumes );//need to get these from Adapter & Utilities first like in run_tests bot...
		//disperse_funds( array( $Adapters['Btce'] ), array( $Adapters['Bitfinex'], $Adapters['Bitstamp'], $Adapters['Bittrex'], $Adapters['Bter'], $Adapters['Poloniex'] ), 'BTC', '0.02222222' ); //$from_arr, $to_arr, $curr_arr

	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 
