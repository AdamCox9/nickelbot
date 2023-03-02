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
			LIBRARIES: communicate with exchange API's directly. Should only be called from ADAPTERS. (bitfinex_lib.php, kraken_lib.php, etc..._lib.php in nickelbot/adapters/.../ folders)
			ADAPTERS: facades that wrap around LIBRARIES. (bitfinex_adapter.php, kraken_adapter.php, etc..._adapter.php in nickelbot/adapters/.../ folders)
			BOTS: functions are an app for now. Bots communicate with ADAPTERS standardized interface but does not call LIBRARIES.
			CRYPTO_UTILITIES: functions that perform some type of calculations or transformations on data, but do not interact with ADAPTERS, LIBRARIES or BOTS
			GUI: it is a web app that communicates with ADAPTERS (similar to a bot but with a web UI). There can be many different GUI's.
			API: can make rest calls to them through HTTP requests like with AJAX from the default GUI.

			TODO: make a BOT class instead of using functions
			NOTE: do not print_r the $Adapter class because it may leak keys and secrets on the web.

			One way to run a bot would be to execute a php script in a cron job.
			Another way would be to make a while(1) loop with sleep(X) that continuously runs.

			------

			Run a PHP process for each exchange from the terminal like this:
			php start.php
			 - or -
			php start.php Bitfinex > bitfinex_out.txt & php start.php Bitstamp > bitstamp_out.txt & php start.php Bittrex > bittrex_out.txt & php start.php Btce > btce_out.txt & php start.php Coinbase > coinbase_out.txt & php start.php Poloniex > poloniex_out.txt & 

			Kill the processes from the terminal like this:
			pkill -9 php

			Here are some sample bots being used in such a simple way:

		 *****/

		//update_orders( array( 0=> $Adapters['Kraken'] ), array() );

		//build_cache( array( 0 => $Adapters['Kraken'] ) );

		//run_tests( $Adapters, $Tester ); //All exchanges
		//run_tests( array( 0 => $Adapters['Bittrex'] ), $Tester );
		
		run_tests( array( 0 => $Adapters['Kraken'] ), $Tester );
		die( "TEST" );
		
		/*
			cancel_oldest_orders:	Sample bot to cancel oldest orders.
		*/
		
		//cancel orders on all exchanges:
		/*cancel_oldest_orders(	$Adapters,				//Array of Adapters
					array(	'direction' => "BOTH",		//[BUY|SELL|BOTH]
						'count' => 3 ) );		//Number of orders to cancel
		cancel_oldest_orders(	$Adapters,				//Array of Adapters
					array(	'direction' => "BUY",		//[BUY|SELL|BOTH]
						'count' => 3 ) );		//Number of orders to cancel
		cancel_oldest_orders(	$Adapters,				//Array of Adapters
					array(	'direction' => "SELL",		//[BUY|SELL|BOTH]
						'count' => 3 ) );		//Number of orders to cancel*/

		//cancel orders on a single exchange:
		/*cancel_oldest_orders(	array(	0 => $Adapters['Kraken'] ),	//Array of Adapters
					array(	'direction' => "BOTH",		//[BUY|SELL|BOTH]
						'count' => 30 ) );		//Number of orders to cancel*/

		/*cancel_oldest_orders(	array(	0 => $Adapters['Bittrex'] ),	//Array of Adapters
					array(	'direction' => "BOTH",		//[BUY|SELL|BOTH]
						'count' => 5 ) );		//Number of orders to cancel*/

		/*
			make_min_orders:	Sample bot to make minimum orders.
		*/
		
		//Create BUY orders on Bittrex:
		make_min_orders(	array(	0 => $Adapters['Bittrex'] ),		//Array of Adapters
					array(	'DIRECTION' => "BUY",			//[BUY|SELL|BOTH] Requires BUY_ORDER_PERCENT_DIFF & SELL_ORDER_PERCENT_DIFF to be set
						'BUY_ORDER_PERCENT_DIFF' => 0.97,	//PRICE=BID*BUY_ORDER_PERCENT_DIFF
						'SELL_ORDER_PERCENT_DIFF' => 2,		//PRICE=ASK*SELL_ORDER_PERCENT_DIFF
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC] to filter markets by price change
						'FILTER_BY_TOP_PRICE_CHANGE' => 5,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'FILTER_BY_TOP_VOLUME' => 25,		//Top X Volume to filter markets by highest volume
						'QUOTE_CURRENCY' => "BTC",		//TODO Could be list of quote currencies [BTC,ETH,USD,ETC...]
						'ORDER_SIZE_MULTIPLIER' => 2 ) );	//Multiply the MIN_ORDER_SIZE by this variable. 100% if > balance

		//Create SELL orders on Bittrex:
		make_min_orders(	array(	0 => $Adapters['Bittrex'] ),		//Array of Adapters
					array(	'DIRECTION' => "SELL",			//[BUY|SELL|BOTH] Requires BUY_ORDER_PERCENT_DIFF & SELL_ORDER_PERCENT_DIFF to be set
						'BUY_ORDER_PERCENT_DIFF' => 0.5,	//PRICE=BID*BUY_ORDER_PERCENT_DIFF
						'SELL_ORDER_PERCENT_DIFF' => 1.03,	//PRICE=ASK*SELL_ORDER_PERCENT_DIFF
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC] to filter markets by price change
						'FILTER_BY_TOP_PRICE_CHANGE' => 500,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'FILTER_BY_TOP_VOLUME' => 500,		//Top X Volume to filter markets by highest volume
						'QUOTE_CURRENCY' => "BTC",		//Could be list of quote currencies [BTC,ETH,USD,ETC...]
						'ORDER_SIZE_MULTIPLIER' => 2 ) );	//Multiply the MIN_ORDER_SIZE by this variable. 100% if > balance

		//Create BUY orders on Kraken:
		/*make_min_orders(	array(	0 => $Adapters['Kraken'] ),		//Array of Adapters
					array(	'DIRECTION' => "BUY",			//[BUY|SELL|BOTH] Requires BUY_ORDER_PERCENT_DIFF & SELL_ORDER_PERCENT_DIFF to be set
						'BUY_ORDER_PERCENT_DIFF' => 0.97,	//PRICE=BID*BUY_ORDER_PERCENT_DIFF
						'SELL_ORDER_PERCENT_DIFF' => 2,		//PRICE=ASK*SELL_ORDER_PERCENT_DIFF
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC] to filter markets by price change
						'FILTER_BY_TOP_PRICE_CHANGE' => 5,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'FILTER_BY_TOP_VOLUME' => 50,		//Top X Volume to filter markets by highest volume (filter by volume happens before filter by price change)
						'QUOTE_CURRENCY' => "XXBT",		//Could be list of quote currencies [BTC,ETH,USD,ETC...]
						'ORDER_SIZE_MULTIPLIER' => 2 ) );	//Multiply the MIN_ORDER_SIZE by this variable. 100% if > balance

		//Create SELL orders on Kraken:
		make_min_orders(	array(	0 => $Adapters['Kraken'] ),		//Array of Adapters
					array(	'DIRECTION' => "SELL",			//[BUY|SELL|BOTH] Requires BUY_ORDER_PERCENT_DIFF & SELL_ORDER_PERCENT_DIFF to be set
						'BUY_ORDER_PERCENT_DIFF' => 0.5,	//PRICE=BID*BUY_ORDER_PERCENT_DIFF
						'SELL_ORDER_PERCENT_DIFF' => 1.03,	//PRICE=ASK*SELL_ORDER_PERCENT_DIFF
						'PRICE_CHANGE_DIRECTION' => "DESC",	//[ASC|DESC] to filter markets by price change
						'FILTER_BY_TOP_PRICE_CHANGE' => 200,	//X Largest Price Change based on PRICE_CHANGE_DIRECTION
						'FILTER_BY_TOP_VOLUME' => 200,		//Top X Volume to filter markets by highest volume
						'QUOTE_CURRENCY' => "XXBT",		//Could be list of quote currencies [BTC,ETH,USD,ETC...]
						'ORDER_SIZE_MULTIPLIER' => 10 ) );	//Multiply the MIN_ORDER_SIZE by this variable. 100% if > balance*/

		/*
			make_max_orders:	Sample bot to make maximum sell orders.
		*/

		/*make_max_orders(	$Adapters, //Array of Adapters
					array( 'SELL_AT_PERCENT_CHANGE' => "1.06" ) );	//Sell at X percent above asking price*/


		/*
			follow_walls:		Sample bot to place orders at walls.
		*/

		/*follow_walls( $Adapters );*/

		/*
			light_show:		Sample bot to jump in front of Highest BID and Lowest ASK orders.
		*/

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
