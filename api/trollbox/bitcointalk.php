<?PHP

	

	$rss = new DOMDocument();
	$rss->load('http://bitcointalk.org/index.php?type=rss;action=.xml&limit=20');

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

	$output = null;
	foreach( $feed as $item ) {
		$output .= "<div><a href='{$item['link']}'>{$item['title']}</a>: {$item['desc']}</div>";
	}

	echo $output;

?>