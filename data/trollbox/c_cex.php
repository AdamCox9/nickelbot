
<?PHP

	/*
	AdamCox9, add c-cex, bitcoin-dev, and bitcoin-assets
	*/


	$chat = file_get_contents('https://c-cex.com/c.html');
	$chat = str_replace("<img src=\"", "<img src=\"https://c-cex.com/", $chat);
	echo $chat;

?>