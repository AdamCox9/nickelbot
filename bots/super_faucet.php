<?PHP

	/*****
		This will be a super faucet. That is, it will allow the user to choose any available currency that has a balance and withdraw minimum amount.
		It needs to be customizable.
		There should be a captcha, then a JS puzzle, then another captcha and possibly collect e-mails.
		The front end will be built in the GUI with JavaScript and AJAX.
	 *****/

	$last_drip = file_get_contents( 'super_faucet.php' );
	$now = time();

	/*****

	Maybe this should be in the API?
	Do something like this on the front end:

	if( $now - 60 > $last_drip ) {
		foreach( $Adapters as $Adapter ) {
			$return .= "*** " . get_class( $Adapter ) . " ***<br/>";
			foreach( $Adapter->get_currencies() as $currency ) {
				$return .= "$currency<br/>";				
			}
		}
	}

	*****/


?>