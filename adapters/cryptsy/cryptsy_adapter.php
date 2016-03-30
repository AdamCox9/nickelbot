<?PHP

	class CryptsyAdapter implements CryptoExchange {

		public function __construct($Exch) {
			$this->exch = $Exch;
		}

		public function get_info() {
			return [];
		}

		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 ) {
			return [];
		}

		public function get_currency_summary( $currency = "BTC" ) {
			return [];
		}
		
		public function get_currency_summaries( $currency = "BTC" ) {
			return [];
		}
		
		public function get_order( $orderid = "1" ) {
			return [];
		}

		public function get_trades( $market = "BTC-USD", $time = 0 ) {
			$results = [];
			array_push( $results, $this->exch->market_tradehistory( str_replace( "-", "_", $market ) ) );
			return $results;
		}

		public function get_all_trades( $time = 0 ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
			if( isset( $this->trades ) )
				return $this->trades;
			$this->trades = [];
			foreach( $this->get_markets() as $market ) {
				$trades = $this->get_trades( $market, $time );
				foreach( $trades as $trade ) {
					$trade['market'] = "$market";
					array_push( $this->trades, $trade );
				}
			}
			return $this->trades;
		}

		public function get_orderbook( $market = "LTC_BTC", $depth = 0 ) {
			return $this->exch->market_orderbook( str_replace( "-", "_", $market ) );
		}

		public function get_orderbooks( $depth = 20 ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
			$results = [];
			foreach( $this->get_markets() as $market )
				$results = array_merge( $results, $this->get_orderbook( $market, $depth ) );

			return $results;
		}

		public function cancel( $orderid="1", $opts = array() ) {
			return $this->exch->cancel_order( $orderid );
		}
		
		public function cancel_all() {
			$orders = $this->get_open_orders();
			$results = [];
			foreach( $orders as $order )
				array_push( $results, $this->cancel( $order['orderid'] ) );
			return array( 'success' => true, 'error' => false, 'message' => $results );
		}

		public function buy( $pair=null, $amount="0", $price="0", $type="LIMIT", $opts=array() ) {
			$pair = str_replace( "-", "_", strtolower( $pair ) );
			$buy = $this->exch->create_order( array( 'marketid' => $opts['market_id'], 'ordertype' => 'buy', 'quantity' => (float) $amount, 'price' => (float) $price ) );
			return $buy;
		}
		
		public function sell( $pair=null, $amount="0", $price="0", $type="LIMIT", $opts=array() ) {
			$pair = str_replace( "-", "_", strtolower( $pair ) );
			$sell = $this->exch->create_order( array( 'marketid' => $opts['market_id'], 'ordertype' => 'sell', 'quantity' => (float) $amount, 'price' => (float) $price ) );
			return $sell;
		}

		public function get_open_orders( $market = "BTC-USD" ) {
			if( isset( $this->open_orders ) )
				return $this->open_orders;
			$this->open_orders = [];
			$orders = $this->exch->get_orders( array( 'type' => 'all' ) );
			if( isset( $orders['data']['buyorders'] ) )
				$this->open_orders = array_merge( $this->open_orders, $orders['data']['buyorders'] );
			if( isset( $orders['data']['sellorders'] ) )
				$this->open_orders = array_merge( $this->open_orders, $orders['data']['sellorders'] );
			return $this->open_orders;
		}

		public function get_completed_orders( $market = "BTC-USD" ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			//TODO get 100 at a time and join them
			$trades = $this->exch->tradehistory( array( 'limit' => 100, 'start' => 0, 'stop' => time() ) );
			$this->completed_orders = $trades['data'];
			return $this->completed_orders;
		}

		public function get_markets() {
			$markets = $this->exch->markets();
			$response = [];
			foreach( $markets['data'] as $market ) {
				array_push( $response, $market['label'] );
			}
			$response = str_replace('/', '-', $response );
			return array_map( 'strtoupper', $response );
		}

		public function get_currencies() {
			$currencies = $this->exch->currencies();
			$response = [];
			foreach( $currencies['data'] as $currency ) {
				array_push( $response, $currency['code'] );
			}
			return array_map( 'strtoupper', $response );
		}
		
		public function deposit_address($currency="BTC"){
			//return $this->exch->address( $currency );
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}
		
		public function deposit_addresses(){
			/*$currencies = $this->get_currencies();

			foreach( $currencies as $currency ) {
				print_r( $this->deposit_address( $currency ) );
			}

			$addresses = $this->exch->addresses();*/

			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_balances() {
			$balances = $this->exch->balances();
			$balances = $balances['data'];
			$currencies = $this->exch->currencies();
			$response = [];
			foreach( $currencies['data'] as $currency ) {
				$id = $currency['id'];
				$balance['type'] = "exchange";
				$balance['currency'] = strtoupper( $currency['code'] );
				$balance['available'] = isset( $balances['available'][$id] ) ? $balances['available'][$id] : 0;
				$balance['reserved'] = isset( $balances['held'][$id] ) ? $balances['held'][$id] : 0;
				$balance['pending'] = 0;
				$balance['btc_value'] = 0;
				$balance['total'] = $balance['reserved'] + $balance['available'];
				array_push( $response, $balance );
			}
			return $response;
		}

		public function get_balance($currency="BTC") {
			return [];
		}

		public function get_market_summary( $market = "BTC-LTC" ) {
			$market_summary = $this->exch->market( $market );
			$market_summary = $market_summary['data'];
			return $market_summary;
		}

		public function get_market_summaries() {
			$market_summaries = $this->exch->markets();
			$market_summaries = $market_summaries['data'];

			$tickers = $this->exch->markets_ticker();
			$m_tickers = [];
			foreach( $tickers['data'] as $ticker ) {
				$m_tickers[$ticker['id']] = array( "bid" => $ticker['bid'], "ask" => $ticker['ask'] );
			}

			$response = [];
			foreach( $market_summaries as $market_summary ) {

				$market_summary['exchange'] = "cryptsy";
				$market_summary = array_merge( $market_summary, $m_tickers[$market_summary['id']] );
				$market_summary['market'] = strtoupper( str_replace( "/", "-", $market_summary['label'] ) );
				$market_summary['low'] = $market_summary['24hr']['price_low'];
				$market_summary['high'] = $market_summary['24hr']['price_high'];
				$market_summary['timestamp'] = $market_summary['last_trade']['timestamp'];
				$market_summary['ask'] = isset( $market_summary['ask'] ) ? $market_summary['ask'] : 0;
				$market_summary['bid'] = isset( $market_summary['bid'] ) ? $market_summary['bid'] : 0;
				$market_summary['mid'] = ( $market_summary['ask'] + $market_summary['bid'] ) / 2;
				$market_summary['base_volume'] = $market_summary['24hr']['volume'];
				$market_summary['quote_volume'] = bcmul( $market_summary['base_volume'], $market_summary['mid'], 32);
				$market_summary['btc_volume'] = $market_summary['24hr']['volume_btc'];
				$market_summary['last_price'] = $market_summary['last_trade']['price'];
				$market_summary['frozen'] = $market_summary['maintenance_mode'];
				$market_summary['verified_only'] = $market_summary['verifiedonly'];
				$market_summary['display_name'] = $market_summary['label'];
				$market_summary['result'] = true;
				$market_summary['created'] = null;
				$market_summary['percent_change'] = null;
				$market_summary['expiration'] = null;
				$market_summary['initial_margin'] = null;
				$market_summary['minimum_margin'] = null;
				$market_summary['vwap'] = null;
				$market_summary['price_precision'] = 8;
				$market_summary['maximum_order_size'] = null;
				$market_summary['minimum_order_size_quote'] = 0.00050000;
				$market_summary['minimum_order_size_base'] = null;
				$market_summary['open_buy_orders'] = null;
				$market_summary['open_sell_orders'] = null;
				$market_summary['market_id'] = $market_summary['id'];

				unset( $market_summary['24hr'] );
				unset( $market_summary['label'] );
				unset( $market_summary['last_trade'] );
				unset( $market_summary['verifiedonly'] );
				unset( $market_summary['maintenance_mode'] );
				unset( $market_summary['id'] );
				unset( $market_summary['market_currency_id'] );
				unset( $market_summary['coin_currency_id'] );

				ksort( $market_summary );

				array_push( $response, $market_summary );
			}
			return $response;
		}

	}

?>