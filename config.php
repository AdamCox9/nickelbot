<?PHP

	/*if( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] != '76.24.176.23' ) {
		if( ! isset( $_SERVER['USER'] ) || $_SERVER['USER'] !== "root" ) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}*/

	/*****

		TODO - something with these requires:

	 *****/

	//Crypto tools, etc...
	require_once( "adapters/crypto_interface.php" );
	require_once( "adapters/crypto_tester.php" );
	require_once( "adapters/crypto_utilities.php" );

	//Bots:
	require_once( "bots/human_readable_summary.php" );
	require_once( "bots/disperse_funds.php" );
	require_once( "bots/make_deposit_addresses.php" );
	require_once( "bots/make_ema_orders.php" );
	require_once( "bots/make_extreme_orders.php" );
	require_once( "bots/make_max_orders.php" );
	require_once( "bots/make_min_orders.php" );
	require_once( "bots/run_tests.php" );

	//Wrapper libraries for native REST API's:
	require_once( "adapters/bitfinex/bitfinex_lib.php" );
	require_once( "adapters/bitstamp/bitstamp_lib.php" );
	require_once( "adapters/bittrex/bittrex_lib.php" );
	require_once( "adapters/btc-e/btc-e_lib.php" );
	require_once( "adapters/bter/bter_lib.php" );
	require_once( "adapters/coinbase/coinbase_lib.php" );
	require_once( "adapters/poloniex/poloniex_lib.php" );

	//Facades for wrapper libraries:
	require_once( "adapters/bitfinex/bitfinex_adapter.php" );
	require_once( "adapters/bitstamp/bitstamp_adapter.php" );
	require_once( "adapters/bittrex/bittrex_adapter.php" );
	require_once( "adapters/btc-e/btc-e_adapter.php" );
	require_once( "adapters/bter/bter_adapter.php" );
	require_once( "adapters/coinbase/coinbase_adapter.php" );
	require_once( "adapters/poloniex/poloniex_adapter.php" );

	/*****
		Globals are the best - need to do something to make this safer.
		These can easily get leaked over the web with a print_r
	 *****/

	$bitfinex_api_key = "INSERT_API_KEY_HERE";
	$bitfinex_api_secret = "INSERT_API_SECRET_HERE";

	$bitstamp_api_key = "INSERT_API_KEY_HERE";
	$bitstamp_api_secret = "INSERT_API_SECRET_HERE";
	$bitstamp_api_number = "INSERT_API_NUMBER_HERE";

	$bittrex_api_key = "INSERT_API_KEY_HERE";
	$bittrex_api_secret = "INSERT_API_SECRET_HERE";

	$btce_api_key = "INSERT_API_KEY_HERE";
	$btce_api_secret = "INSERT_API_SECRET_HERE";

	$bter_api_key = "INSERT_API_KEY_HERE";
	$bter_api_secret = "INSERT_API_SECRET_HERE";

	$coinbase_api_key = "INSERT_API_KEY_HERE";
	$coinbase_api_secret = "INSERT_API_SECRET_HERE";
	$coinbase_api_passphrase = 'INSERT_API_PASSPHRASE_HERE';

	$poloniex_api_key = "INSERT_API_KEY_HERE";
	$poloniex_api_secret = "INSERT_API_SECRET_HERE";

	/*****
		IMPORTANT FOR YOU TO CHANGE, NOT ME!!!
		WE CAN'T GET DEPOSIT ADDRESS THROUGH API?
	 *****/

	$BTCE_BTC_DEPOSIT_ADDRESS = "1jPtEamiPHn2NaPXab29ruSAparsvrUre";
	$BTCE_LTC_DEPOSIT_ADDRESS = "LZrNNQtK4yDzwEjj2VszEm529UaDDDsdPH";
	$BTCE_NMC_DEPOSIT_ADDRESS = "NEtAMTUgqyD4w7DEA414PRSFjhoVJstP7W";
	$BTCE_NVC_DEPOSIT_ADDRESS = "4KwnoXR5nKxPebxryruugbuqP7SdiuWxP3";
	$BTCE_PPC_DEPOSIT_ADDRESS = "PVduuiWTCm3jPaPr9JTPyBhAVrhZuEER5D";

	$Adapters = array();
	$Adapters['Bitfinex'] = new BitfinexAdapter( new Bitfinex( $bitfinex_api_key, $bitfinex_api_secret ) );
	$Adapters['Bitstamp'] = new BitstampAdapter( new Bitstamp( $bitstamp_api_key, $bitstamp_api_secret, $bitstamp_api_number ) );
	$Adapters['Bittrex'] = new BittrexAdapter( new Bittrex( $bittrex_api_key, $bittrex_api_secret ) );
	$Adapters['Btce'] = new BtceAdapter( new Btce( $btce_api_key, $btce_api_secret ) );
	$Adapters['Bter'] = new BterAdapter( new Bter( $bter_api_key, $bter_api_secret ) );
	$Adapters['Coinbase'] = new CoinbaseAdapter( new Coinbase( $coinbase_api_key, $coinbase_api_secret, $coinbase_api_passphrase ) );
	$Adapters['Poloniex'] = new PoloniexAdapter( new Poloniex( $poloniex_api_key, $poloniex_api_secret ) );

	$Tester = new Tester();

?>
