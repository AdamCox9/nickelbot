<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	if( file_exists( '../../cache/bitcointalk.txt' ) && rand(0,10) > 2 ) {
		die( file_get_contents( '../../cache/bitcointalk.txt' ) );
	}

	$rss = new DOMDocument();
	$rss->load('http://bitcointalk.org/index.php?type=rss;action=.xml&amp;limit=20');

	$feed = array();
	foreach ($rss->getElementsByTagName('item') as $node) {
		$item = array ( 
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
			'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
			'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
			);
		array_unshift($feed, $item);
	}

	$rss = null;

	$output = null;
	foreach( $feed as $item ) {
		$output .= "<div><a href='{$item['link']}'>{$item['title']}</a>: {$item['desc']}</div>";
	}

	if( ! is_null( $output ) )
		file_put_contents( '../../cache/bitcointalk.txt', $output );

	echo $output;

?>
