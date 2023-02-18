<?PHP

	//_____Maybe we can organize these dependencies better.

	//_____Crypto tools, etc...

	require_once( "adapters/crypto_base.php" );
	require_once( "adapters/crypto_interface.php" );
	require_once( "adapters/crypto_tester.php" );
	require_once( "adapters/crypto_utilities.php" );

	//_____Bots:

	require_once( "bots/build_cache.php" );
	require_once( "bots/human_readable_summary.php" );
	require_once( "bots/disperse_funds.php" );
	require_once( "bots/make_deposit_addresses.php" );
	require_once( "bots/make_max_orders.php" );
	require_once( "bots/make_min_orders.php" );
	require_once( "bots/run_tests.php" );
	require_once( "bots/light_show.php" );

	//_____Wrapper libraries for native API's:

	require_once( "adapters/bitfinex/bitfinex_lib.php" );
	require_once( "adapters/bitstamp/bitstamp_lib.php" );
	require_once( "adapters/bittrex/bittrex_lib.php" );
	require_once( "adapters/bter/bter_lib.php" );
	require_once( "adapters/coinbase/coinbase_lib.php" );
	require_once( "adapters/kraken/kraken_lib.php" );
	require_once( "adapters/poloniex/poloniex_lib.php" );

	//_____Facades for wrapper libraries:

	require_once( "adapters/bitfinex/bitfinex_adapter.php" );
	require_once( "adapters/bitstamp/bitstamp_adapter.php" );
	require_once( "adapters/bittrex/bittrex_adapter.php" );
	require_once( "adapters/bter/bter_adapter.php" );
	require_once( "adapters/coinbase/coinbase_adapter.php" );
	require_once( "adapters/kraken/kraken_adapter.php" );
	require_once( "adapters/poloniex/poloniex_adapter.php" );

	//_____Globals are the best

	$bitfinex_api_key = "INSERT_API_KEY_HERE";
	$bitfinex_api_secret = "INSERT_API_SECRET_HERE";

	$bitstamp_api_key = "INSERT_API_KEY_HERE";
	$bitstamp_api_secret = "INSERT_API_SECRET_HERE";
	$bitstamp_api_number = "INSERT_API_NUMBER_HERE"; //customer number

	$bittrex_api_key = "INSERT_API_KEY_HERE";
	$bittrex_api_secret = "INSERT_API_SECRET_HERE";

	$bter_api_key = "INSERT_API_KEY_HERE";
	$bter_api_secret = "INSERT_API_SECRET_HERE";

	$coinbase_api_key = "INSERT_API_KEY_HERE";
	$coinbase_api_secret = "INSERT_API_SECRET_HERE";
	$coinbase_api_passphrase = 'INSERT_API_PASSPHRASE_HERE';

	$kraken_api_key = "INSERT_API_KEY_HERE";
	$kraken_api_secret = "INSERT_API_SECRET_HERE";

	$poloniex_api_key = "INSERT_API_KEY_HERE";
	$poloniex_api_secret = "INSERT_API_SECRET_HERE";

	$COINHIVE_SECRET = "INSERT_COINHIVE_SECRET_HERE";

	//_____Make a structure or use some design pattern?

	$Adapters = array();
	$Adapters['Bitfinex']	= new BitfinexAdapter( new Bitfinex( $bitfinex_api_key, $bitfinex_api_secret ) );
	$Adapters['Bitstamp']	= new BitstampAdapter( new Bitstamp( $bitstamp_api_key, $bitstamp_api_secret, $bitstamp_api_number ) );
	$Adapters['Bittrex']	= new BittrexAdapter( new Bittrex( $bittrex_api_key, $bittrex_api_secret ) );
	$Adapters['Bter']	= new BterAdapter( new Bter( $bter_api_key, $bter_api_secret ) );
	$Adapters['Coinbase']	= new CoinbaseAdapter( new Coinbase( $coinbase_api_key, $coinbase_api_secret, $coinbase_api_passphrase ) );
	$Adapters['Kraken']	= new KrakenAdapter( new Kraken( $kraken_api_key, $kraken_api_secret ) );
	$Adapters['Poloniex']	= new PoloniexAdapter( new Poloniex( $poloniex_api_key, $poloniex_api_secret ) );

	//_____This simple class should suffice for testing.

	$Tester = new Tester();

?>
