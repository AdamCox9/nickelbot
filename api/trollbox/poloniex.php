
<?PHP

	$chat = file_get_contents('https://poloniex.com/public?command=getTrollboxMessages&messages=30');
	$chat = json_decode( $chat, true );

	$out = null;
	foreach( $chat as $str ) {
		echo "<div><span class='handle'>".$str['username'].":</span> <span class='text'>".$str['message']."</span><div><br>";
	}

?>