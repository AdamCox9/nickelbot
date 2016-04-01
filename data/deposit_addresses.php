<?php

	error_reporting( E_ALL );
	ini_set( 'display_errors', 'on' );
	date_default_timezone_set( "UTC" );

	require_once( "../config_safe.php" );

	try{
		foreach( $Adapters as $Adapter ) {
			echo make_deposit_addresses( array( $Adapter ), $Tester );
		}
	} catch( Exception $e ){
		echo $e->getMessage() . "\n";
	}

?> 