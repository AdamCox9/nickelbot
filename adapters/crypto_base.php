<?PHP

	class CryptoBase {

		//This could have exchange info such as name, url, location, total volume, etc???
		public function get_info() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function get_currency_summary( $currency = "BTC" ) { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function get_currency_summaries() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }

		//Deposits & Withdrawals
		public function get_deposits_withdrawals() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function get_deposits() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function get_deposit( $deposit_id="1", $opts = array() ) { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function get_withdrawals() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }

		public function get_trollbox() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function transfer_balance() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }

		//Margin trading
		public function margin_history() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function margin_info() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		
		//lending:
		public function loan_offer() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function cancel_loan_offer() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function loan_offer_status() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function active_loan_offers() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }

		//borrowing:
		public function get_positions() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function claim_position() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function close_position() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function active_loan() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }
		public function inactive_loan() { return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' ); }

	}

?>