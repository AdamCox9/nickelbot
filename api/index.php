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


 *****/

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	if( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] != '76.24.176.23' ) {
		if( ! isset( $_SERVER['USER'] ) || $_SERVER['USER'] !== "root" ) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}

	require_once( "../config_safe.php" );

	try {
		start();
	} catch( Exception $e ) {
		echo $e->getMessage() . "\n";
	}

	function start()
	{
		$action = isset( $_GET['action'] ) ? $_GET['action'] : die( "action required" );

		switch( $action ) {
			case "exchanges": $response = api_exchanges(); break;
			case "buy": $response = api_buy(); break;
			case "sell": $response = api_sell(); break;
			case "currencies": $response = api_currencies(); break;
			case "markets": $response = api_markets(); break;
			case "completed_orders": $response = api_completed_orders(); break;
			case "open_orders": $response = api_open_orders(); break;
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
		echo "buy";
	}
	function api_sell()
	{
		echo "sell";
	}
	function api_currencies()
	{
		echo "currencies";
	}
	function api_markets()
	{
		echo "markets";
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
		echo "completed_orders";
	}
	//all of them
	function api_open_orders()
	{
		echo "open_orders";
	}
	function api_deposit_addresses()
	{
		global $Adapters;
		//print_r( $Adapters );

		$exchange = isset( $_GET['exchange'] ) ? $_GET['exchange'] : null;
		return array( get_class( $Adapters[$exchange] ) => $Adapters[$exchange]->deposit_addresses() );

	}


?>