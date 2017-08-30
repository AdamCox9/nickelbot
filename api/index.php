<?PHP

/*****

	This should match the Adapter APIs and make them available through HTTP requests.

	index.php?action=exchanges
	index.php?action=deposit_addresses&exchange=Bitfinex
	index.php?action=open_orders&exchange=Bitfinex
	index.php?action=completed_orders&exchange=Bitfinex
	index.php?action=buy&exchange=Bitfinex&market=BTC-LTC&price=100&amount=5
	
	- or TODO update the htaccess? so we can do this:
	- /api/bitfinex/buy/?market=BTC-LTC&price=100&amount=5

	- TODO make me a class and other safety features

	Don't let Adapters object leak the API Keys/Secrets on the web with print_r!

 *****/

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	/*if( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] != '76.24.176.23' ) {
		if( ! isset( $_SERVER['USER'] ) || $_SERVER['USER'] !== "root" ) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}*/

	require_once( "../config_safe.php" );

	try {
		api_start();
	} catch( Exception $e ) {
		echo $e->getMessage() . "\n";
	}

	function api_start()
	{
		$action = isset( $_GET['action'] ) ? $_GET['action'] : die( "action required" );

		switch( $action ) {
			case "exchanges": $response = api_exchanges(); break;
			case "cancel": $response = api_cancel(); break;
			case "cancel_all": $response = api_cancel_all(); break;
			case "buy": $response = api_buy(); break;
			case "sell": $response = api_sell(); break;
			case "get_currencies": $response = api_currencies(); break;
			case "get_markets": $response = api_markets(); break;
			case "get_market_summary": $response = api_market_summary(); break;
			case "get_market_summaries": $response = api_market_summaries(); break;
			case "get_balance": $response = api_balance(); break;
			case "get_balances": $response = api_balances(); break;
			case "get_completed_orders": $response = api_completed_orders(); break;
			case "get_open_orders": $response = api_open_orders(); break;
			case "deposit_addresses": $response = api_deposit_addresses(); break;
			default: die( "error" );
		}

		if( is_array( $response ) ) {
			echo json_encode( $response );
		} else {
			print_r( $response );
			die( "" );
		}
	}

	function api_buy()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		$price = isset( $_GET['price'] ) ? $_GET['price'] : "error";
		if( $price == "error" ) return array( "error" => "price required" );
		$amount = isset( $_GET['amount'] ) ? $_GET['amount'] : "error";
		if( $amount == "error" ) return array( "error" => "amount required" );
		$market = isset( $_GET['market'] ) ? $_GET['market'] : "error";
		if( $market == "error" ) return array( "error" => "market required" );

		//let's not open up the trade api to the public:
		return "PERMISSION DENIED";

		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->buy( $market, $amount, $price ) );
	}
	function api_sell()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		$price = isset( $_GET['price'] ) ? $_GET['price'] : "error";
		if( $price == "error" ) return array( "error" => "price required" );
		$amount = isset( $_GET['amount'] ) ? $_GET['amount'] : "error";
		if( $amount == "error" ) return array( "error" => "amount required" );
		$market = isset( $_GET['market'] ) ? $_GET['market'] : "error";
		if( $market == "error" ) return array( "error" => "market required" );

		//let's not open up the trade api to the public:
		return "PERMISSION DENIED";

		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->sell( $market, $amount, $price ) );
	}
	function api_cancel()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		$id = isset( $_GET['id'] ) ? $_GET['id'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		if( $id == "error" ) return array( "error" => "id required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->cancel( $id ) );
	}
	function api_cancel_all()
	{
		return array( "error" => "nope" );
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->cancel_all() );
	}
	function api_currencies()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_currencies() );
	}
	function api_markets()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_markets() );
	}
	function api_market_summaries()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_market_summaries() );
	}
	function api_balance()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		$currency = isset( $_GET['currency'] ) ? $_GET['currency'] : "error";
		if( $currency == "error" ) return array( "error" => "currency required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_balance( $currency ) );
	}
	function api_balances()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_balances() );
	}
	function api_market_summary()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		$market = isset( $_GET['market'] ) ? $_GET['market'] : "error";
		if( $market == "error" ) return array( "error" => "market required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_market_summary( $market ) );
	}
	function api_exchanges()
	{
		global $Adapters;
		foreach( $Adapters as $Adapter ){
			$results[] = str_replace( "Adapter", "", get_class( $Adapter ) );
		}
		return $results;
	}
	//last 100
	function api_completed_orders()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		$market = isset( $_GET['market'] ) ? $_GET['market'] : "error";
		if( $market == "error" ) return array( "error" => "market required" );

		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_completed_orders( $market ) );
	}
	//all of them
	function api_open_orders()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->get_open_orders() );
	}
	function api_deposit_addresses()
	{
		global $Adapters;

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : "error";
		if( $exchange == "error" ) return array( "error" => "exchange required" );
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->deposit_addresses() );
	}


?>