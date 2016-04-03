<?php

	/*******for testing concept of api folder***************/

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "../config_safe.php" );

	try {
		foreach( $Adapters as $Adapter ){
			echo "<br/>\n***** " . get_class( $Adapter ) . " *****<br/>\n";
			foreach( $Adapter->get_completed_orders() as $order ) {
				foreach( $order as $key => $val ) {
					echo $key . ": " . $val . " &nbsp;\t ";
				}	
				echo "\n<br/>";
			}
		}
	} catch( Exception $e ) {
		echo $e->getMessage() . "\n";
	}
?> 