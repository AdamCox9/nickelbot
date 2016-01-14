<?PHP

	$html = file_get_contents('https://btc-e.com/');

	$matches = Array();

	$regex = '/<div id=\'nChat\'>(.+?)<\/div>/s';
	preg_match($regex, $html, $matches);

	print_r($matches[0]);
	//echo $html;


?>