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
		cancel_oldest_orders( $Adapters, array( 'direction' => "BOTH" /*[BUY|SELL|BOTH]*/, 'count' => 5 ) );
		
		/*
			make_min_orders:	Sample bot to make minimum buy orders:
		*/
		
		/*make_min_orders(	array(	0 => $Adapters['Bittrex'] ),		//Array of Adapters
					array(	'BUY_AT_PERCENT_CHANGE' => 0.97,	//PRICE=BID-BID*BUY_AT_PERCENT_CHANGE
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC]  to filter by price change
						'FILTER_BY_TOP_VOLUME' => 20,		//Top X Volume
						'FILTER_BY_TOP_PRICE_CHANGE' => 5,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'QUOTE_CURRENCY' => "BTC" ) );*/

		/*make_min_orders(	array(	0 => $Adapters['Kraken'] ),		//Array of Adapters
					array(	'BUY_AT_PERCENT_CHANGE' => 0.97,	//PRICE=BID-BID*BUY_AT_PERCENT_CHANGE
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC]  to filter by price change
						'FILTER_BY_TOP_VOLUME' => 20,		//Top X Volume
						'FILTER_BY_TOP_PRICE_CHANGE' => 5,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'QUOTE_CURRENCY' => "XXBT" ) );		//XXBT is BTC on Kraken*/

		/*
			make_max_orders:	Sample bot to make maximum sell orders:
			TODO combine make_min_orders and make_max_orders into the same bot with DIRECTION, SIDE, ORDER_SIZE in CONFIG
		*/

		make_max_orders(	$Adapters, //Array of Adapters
					array( 'SELL_AT_PERCENT_CHANGE' => "1.06" ) );	//Sell at X percent above asking price*/


		//follow_walls( $Adapters );

		/*while( true ) {
			cancel_oldest_orders( $Adapters );
			light_show( $Adapters['Bittrex'], "BSV-BTC"  );
			sleep( 60 );
		}*/

		//make_deposit_addresses( $Adapters );
		//human_readable_summary( $Adapters );
		//disperse_funds( $Adapters );

	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 
