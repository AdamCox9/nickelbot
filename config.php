<?PHP

/*	if( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] != '76.24.176.23' ) {
		if( ! isset( $_SERVER['USER'] ) || $_SERVER['USER'] !== "root" ) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}*/

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

	//Wrapper libraries for native API's:
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
		Globals are the best!
	 *****/

	$bitfinex_api_key = "A4K0wCj6KLdChWkp2Xxd4xPLDWj9nYD7dCvdZ5Wj7jr";
	$bitfinex_api_secret = "xYKT35o3rJloES7GWkO2lWKYH9c5kwxCVV0W3XqqaP7";

	$bitstamp_api_key = "IFo7FPrSu97ufUbUS89CHz3uwqKyHFcX";
	$bitstamp_api_secret = "CehjYgU0JArV9vtO7hj6p1WL5Q7rdVtP";
	$bitstamp_api_number = "779882";

	$bittrex_api_key = "5d46d0942fea4f059d95c3bce1377f57";
	$bittrex_api_secret = "15aa417db72249d5831b402cab1aa289";

	$btce_api_key = "0AKAY3GL-9ZPEYWH6-LYTBCROP-LGVA1GRY-80LTUVSI";
	$btce_api_secret = "cdc6eef818df6c0edfc9647732cc68af96a25b9b554174b7c669a01e2607909a";

	$bter_api_key = "EAD04357-77CE-4A75-9B7D-CFAD0B481D09";
	$bter_api_secret = "1dd9b32a6a0a92ab70bc2944ad567db9b133593eee8c597d80e9d4392235f11b";

	$coinbase_api_key = '8aa0beaec603603e90411f28f17b902f';
	$coinbase_api_secret = '/2xL4nbN5jwjevO02WTyhRe3usqubtEJIlzL6+omWzls9cGHftdCtDfB7tQE1JwZkKGoaGtFrO6QhWxS47Kfgw==';
	$coinbase_api_passphrase = '8f0ac5kewjtw3ik9';

	$poloniex_api_key = "0LWESBZB-G42RZSC1-R1ODIXAS-D74LGT07";
	$poloniex_api_secret = "876ef684bd13d3505d1c8c70179cc27b925e171bf6a4109f554f7472776d16a8331156ed9bc50acd977ef470120d36f9e742a4f7a0f57a415149d330aa7ab31d";

	//_____IMPORTANT FOR YOU TO CHANGE!!! WE CAN'T GET DEPOSIT ADDRESS THROUGH API?
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

	$exchanges = [];
	$currencies = [];
	$markets = [];
	$market_summaries = [];
	$balances = [];
	$open_orders = [];
	$worths = [];
	$completed_orders = [];
	$deposit_addresses = [];
	$trades = [];
	$orderbooks = [];
	$volumes = [];

	$Tester = new Tester();

?>