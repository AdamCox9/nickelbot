<?PHP

/*****

	This bot will liquidate an exchange by first trying to see if any other exchanges can recieve the funds directly.
	If no other exchange takes that currency, then market sell the currency for a currency that is accepted somewhere else.

 *****/

function disperse_funds( $from_arr, $to_arr, $curr, $amount ) {

	foreach( $from_arr as $from_exch ) {
		foreach( $to_arr as $to_exch ) {
			echo get_class( $from_exch ) . " to " . get_class( $to_exch ) . "\n\n";
			$deposit_address = $to_exch->deposit_address( $curr );
			print_r( $deposit_address );
			if( isset( $deposit_address['address'] ) ) {
				$deposit_address = $deposit_address['address'];
				echo $deposit_address . "\n\n";
			}
			//$from_exch->withdraw( $curr, $amount, $deposit_address );
		}
	}

}

?>