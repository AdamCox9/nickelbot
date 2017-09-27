<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "../../config_safe.php" );

	print_r( $_POST );

	$post_data = [
		'secret' => $COINHIVE_SECRET, // <- Your secret key
		'token' => $_POST['coinhive-captcha-token'],
		'hashes' => 1000000
	];

	$post_context = stream_context_create([
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($post_data)
		]
	]);

	$url = 'https://api.coin-hive.com/token/verify';
	$response = json_decode(file_get_contents($url, false, $post_context));

	print_r( $response );

	if ($response && $response->success) {
		//initiate transfer:
		if( isset( $_POST['address'] ) ) {
			echo "attempting withdrawal";

			print_r( $Adapters['Poloniex']->withdraw( 'EXCHANGE', 'BURST', $_POST['address'], 3 ) );
		}
	}

?>