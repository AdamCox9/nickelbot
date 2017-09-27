<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	if( file_exists( "../../cache/btce.txt" )  && rand(0,10) > 2 )
		die( file_get_contents( "../../cache/btce.txt" ) );

	$html = file_get_contents('https://btc-e.com/');

	$matches = Array();

	$regex = '/<div id=\'nChat\'>(.+?)<\/div>/s';
	preg_match($regex, $html, $matches);

	$html = $matches[0];

	echo $html;

	file_put_contents( "../../cache/btce.txt", $html );

?>