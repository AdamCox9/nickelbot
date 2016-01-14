<?php
	interface CryptoExchange
	{
		//This could have exchange info such as name, url, location, total volume, etc???
		public function get_info();

		//Withdraw funds to $address
		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 );

		//Analysis EMA, Change, Trades Per Minute, etc...
		//Try to calculate percentile so an order can be set behind 20% or 10% of other volume...

		//Make a buy order
		//$type: limit, market, stop, margin, trigger, etc...
		public function buy( $pair='BTC-USD', $amount="0.01", $price="0.01", $type="LIMIT", $opts=array() );

		//Make a sell order
		//$type: limit, market, stop, margin, trigger, etc...
		public function sell( $pair='BTC-USD', $amount="0.01", $price="0.01", $type="LIMIT", $opts=array() );
		
		//Private Orders
		public function get_open_orders();
		public function get_completed_orders();
		public function get_order( $order_id );

		//Cancel one or many orders: TODO accept array of orderid's
		public function cancel( $orderid="1", $opts = array() );

		//Cancel all orders:
		public function cancel_all();

		//Public Orders
		public function get_orderbooks( $depth = 20 );
		public function get_orderbook( $market = "BTC-USD", $depth = 20 );
		public function get_all_trades( $time = 0 );
		public function get_trades( $market = "BTC-USD", $time = 0 );

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

		//Balances: confirmed, reserved, available, pending, total, btc_worth, usd_worth etc???
		public function get_balance( $currency = "BTC" );
		public function get_balances();

		//supertrollbox would be a bot
		//public function get_trollbox(); //maybe forum if no trollbox?

	}
?>