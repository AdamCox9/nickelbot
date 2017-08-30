<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	if( file_exists( "../../cache/redditbitcoin.txt" )  && rand(0,10) > 1 )
		die( file_get_contents( "../../cache/redditbitcoin.txt" ) );


	$rss = new DOMDocument();
	$rss->load('https://www.reddit.com/r/bitcoin/new/.rss?after=t3_');

	$output = $rss->textContent;

	if( ! is_null( $output ) ) {
		file_put_contents( '../../cache/redditbitcoin.txt', $output );
		echo $output;
	}

?>