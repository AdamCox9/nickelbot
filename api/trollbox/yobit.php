<?PHP

	$html = file_get_contents('https://www.yobit.net/en/');

	$matches = Array();

	$regex = '/<div class=\"overview\" id=\"chat-list\">(.+?)<\/div>/s';
	preg_match($regex, $html, $matches);

	print_r($matches[0]);
	//echo $html;


?>