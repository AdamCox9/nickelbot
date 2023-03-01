<?php

	interface CryptoExchange
	{
		//This could have exchange info such as name, url, location, total volume, etc???
		public function get_info();

		//Withdraw funds to $address
		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 );

		//Make orders - $type: limit, market, stop, margin, trigger, etc...
		public function buy( $pair = 'BTC-USD', $amount = "0.01", $price = "0.01", $type = "LIMIT", $opts = array() );
		public function sell( $pair = 'BTC-USD', $amount = "0.01", $price = "0.01", $type = "LIMIT", $opts = array() );
		public function update_order( $order_id = 0, $amount = 0, $price = 0, $opts = array() );

		//Private Orders
		public function get_open_orders();
		public function get_completed_orders();
		public function get_order( $order_id );

		//Cancel one or many orders: TODO accept array of orderid's
		public function cancel( $orderid = "1", $opts = array() );

		//Cancel all orders:
		public function cancel_all();

		//Get a deposit address for a currency
		public function deposit_address( $currency = "BTC" );
		public function deposit_addresses();

		//BTC, LTC, USD, etc...
		public function get_currencies();
		public function get_currency_summary( $currency = "BTC" );
		public function get_currency_summaries();

		//BTC-USD, BTC-EUR, LTC-BTC
		public function get_markets();
		public function get_market_summary( $market = "BTC-USD" );
		public function get_market_summaries();
		public function get_ohlc();
		public function get_orderbook( $market = "BTC-USD", $depth = 25 );
		public function get_trades( $market = "BTC-USD", $opts = array() );
		public function get_spread();

		//Balances: confirmed, reserved, available, pending, total
		public function get_balance( $currency = "BTC" );
		public function get_balances();
		public function transfer_balance(); //between Exchange, Trading & Deposit wallets

		//Get deposits to and from the exchange:
		public function get_deposits_withdrawals();
		public function get_deposits();
		public function get_deposit( $deposit_id = "1", $opts = array() );
		public function get_withdrawals();

		//Return trollbox data from the exchange, otherwise get forum posts or twitter feed if must...
		public function get_trollbox();

		//Margin trading
		public function margin_history();
		public function margin_info();
		
		//lending:
		public function loan_offer();
		public function cancel_loan_offer();
		public function loan_offer_status();
		public function active_loan_offers();

		//borrowing:
		public function get_positions();
		public function claim_position();
		public function close_position();
		public function active_loan();
		public function inactive_loan();


	}
?>
