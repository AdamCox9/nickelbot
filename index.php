<?PHP

error_reporting( E_ALL );
ini_set("display_errors", 1);

$template = file_get_contents( 'gui/index.html' );

echo $template;

?>