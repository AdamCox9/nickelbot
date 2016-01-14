<?PHP

	$chat = file_get_contents('https://www.cryptsy.com/chat.php?time=1438556777337');
	$chat = json_decode( $chat, true );

	$out = null;
	foreach( $chat as $msg ) {
		$str = $msg." ";
		$str = json_decode( $str, true );
	//	var_dump( $str );
		echo "<div><span class='handle'>".$str['handle'].":</span> <span class='text'>".$str['text']."</span><div><br>";
	}

?>