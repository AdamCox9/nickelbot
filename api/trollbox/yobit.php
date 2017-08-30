<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	if( file_exists( "../../cache/yobit.txt" )  && rand(0,10) > 1 )
		die( file_get_contents( "../../cache/yobit.txt" ) );

	$html = file_get_contents('https://www.yobit.net/en/');

	$matches = Array();

	$regex = '/<div class=\"overview\" id=\"chat-list\">(.+?)<\/div>/s';
	preg_match($regex, $html, $matches);

	$output = print_r( $matches[0], 1 );

	if( ! is_null( $output ) ) {
		file_put_contents( "../../cache/yobit.txt", $output );
		echo $output;
	}

?>