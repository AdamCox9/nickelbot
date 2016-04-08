<?PHP

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );

	$html = file_get_contents('http://log.bitcoin-assets.com/');

	echo $html;

	//$matches = Array();

	//$regex = '/<div id=\'nChat\'>(.+?)<\/div>/s';
	//preg_match($regex, $html, $matches);

	//print_r($matches[0]);
	//echo $html;


?>