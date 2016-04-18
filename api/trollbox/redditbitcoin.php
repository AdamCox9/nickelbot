<?PHP

	//TODO fix me

	$rss = new DOMDocument();
	$rss->load('https://www.reddit.com/r/bitcoin/new/.rss?after=t3_');

	echo $rss->textContent;

?>