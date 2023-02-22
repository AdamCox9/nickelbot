<?PHP

/*****

	This bot will generate a cryptocurrency address for each currency on each exchange that supports this functionality.

 *****/

function make_deposit_addresses( $Adapters ) {

	foreach( $Adapters as $Adapter ) {
		echo "\n<br/>***** " . get_class( $Adapter ) . " *****<br/>\n";
		$deposit_addresses = $Adapter->deposit_addresses();

		//Do Nothing if $Adapter returns follow errors:
		if( isset( $deposit_addresses['ERROR'] ) && $deposit_addresses['ERROR'] == "METHOD_NOT_AVAILABLE" ) continue;
		if( isset( $deposit_addresses['ERROR'] ) && $deposit_addresses['ERROR'] == "METHOD_NOT_IMPLEMENTED" ) continue;

		foreach( $deposit_addresses as $deposit_address ) {
			echo get_class( $Adapter ) . "\t" . $deposit_address['currency'] . "\t" . $deposit_address['address'] . "\t" . $deposit_address['status'] . "\t" . $deposit_address['cryptoAddressTag'] . "\n<br/>";
		}
	}

}

?>
