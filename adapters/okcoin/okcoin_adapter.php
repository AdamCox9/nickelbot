<?PHP

	/*****
		TODO
			- the next most popular exchange and create an adapter for it.
			- make sure to create an issue and mention that you are working on it so no wasted effort
	 *****/

	class OkcoinAdapter extends CryptoBase implements CryptoExchange {

		public function __construct( $Exch ) {
			$this->exch = $Exch;
		}

		private function get_market_symbol( $market )
		{
			return strtoupper( str_replace( '_', '-', $market ) );
		}

		private function unget_market_symbol( $market )
		{
			return strtolower( str_replace( '-', '_', $market ) );
		}

		public function get_info() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_currency_summary( $currency = "BTC" ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function get_currency_summaries( $currency = "BTC" ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function get_order( $orderid = "1" ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function cancel( $orderid="1", $opts = array() ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_deposits_withdrawals() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_deposits() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_deposit( $deposit_id="1", $opts = array() ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_withdrawals() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function cancel_all() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function buy( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function sell( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_open_orders() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_completed_orders( $market="BTC-USD", $limit=100 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_markets() {
			return array( 'BTC-USD', 'LTC-USD' );
		}

		public function get_currencies() {
			return array( 'BTC', 'LTC', 'USD' );
		}

		public function deposit_address( $currency = "BTC", $wallet_type = "exchange" ){
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function deposit_addresses(){
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_balances() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_balance( $currency="BTC", $opts = array() ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_market_summary( $market = "ETH-BTC" ) {
			$market_summary = $this->exch->tickerApi( $this->unget_market_symbol( $market ) );
			$ticker = $market_summary->ticker;

			$result = [];

			$result['market'] = $market;
			$result['timestamp'] = $market_summary->date;
			$result['ask'] = $ticker->sell;
			$result['base_volume'] = $ticker->vol;
			$result['bid'] = $ticker->buy;
			$result['btc_volume'] = null;
			$result['created'] = null;
			$result['display_name'] = null;
			$result['exchange'] = null;
			$result['expiration'] = null;
			$result['frozen'] = null;
			$result['high'] = $ticker->high;
			$result['initial_margin'] = null;
			$result['last_price'] = $ticker->last;
			$result['low'] = $ticker->low;
			$result['market_id'] = null;
			$result['maximum_order_size'] = null;
			$result['mid'] = null;
			$result['minimum_margin'] = null;
			$result['minimum_order_size_base'] = '0.01';
			$result['minimum_order_size_quote'] = null;
			$result['open_buy_orders'] = null;
			$result['open_sell_orders'] = null;
			$result['percent_change'] = null;
			$result['price_precision'] = null;
			$result['quote_volume'] = $ticker->vol;
			$result['result'] = null;
			$result['verified_only'] = null;
			$result['vwap'] = null;

			return $result;
		}

		public function get_market_summaries() {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_trades( $market = "BTC-USD", $time = 0 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_all_trades( $time = 0 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_orderbooks( $depth = 20 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function get_orderbook( $market = 'BTC-USD', $depth = 20 ) {
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
	}
?>