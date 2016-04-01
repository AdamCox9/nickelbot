<?PHP

	class BitfinexAdapter implements CryptoExchange {

		public function __construct( $Exch ) {
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
		
		public function cancel( $orderid="1", $opts = array() ) {
			return $this->exch->order_cancel( $orderid );
		}

		public function cancel_all() {
			$result = $this->exch->order_cancel_all();
			if( $result['result'] == "All orders cancelled" ) {
				return array( 'success' => true, 'error' => false, 'message' => $result );
			}
			return array( 'success' => false, 'error' => true, 'message' => $result );
		}

		public function buy( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = strtolower( $pair );
			$pair = str_replace( "-", "", $pair );
			$buy = $this->exch->order_new( $pair, $amount, $price, "bitfinex", "buy", "exchange limit", true );
			if( isset( $sell['message'] ) )
				print_r( $buy );
			return $buy;
		}
		
		public function sell( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = strtolower( $pair );
			$pair = str_replace( "-", "", $pair );
			$sell = $this->exch->order_new( $pair, $amount, $price, "bitfinex", "sell", "exchange limit", true );
			if( isset( $sell['message'] ) )
				print_r( $sell );
			return $sell;
		}

		public function get_open_orders() {
			if( isset( $this->open_orders ) )
				return $this->open_orders;
			$open_orders = $this->exch->orders();
			$this->open_orders = [];

			if( is_array( $open_orders ) )
				foreach( $open_orders as $open_order ) {
					$open_order['exchange'] = "bitfinex";
					$open_order['market'] = $open_order['symbol'];
					$open_order['timestamp_created'] = $open_order['timestamp'];
					$open_order['amount'] = null;

					unset( $open_order['symbol'] );
					array_push( $this->open_orders, $open_order );
				}
			return $this->open_orders;
		}

		public function get_completed_orders() {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$markets = $this->get_markets();
			$this->completed_orders = [];
			foreach( $markets as $market ) {
				$market = str_replace( "-", "", strtoupper( $market ) );
				$market_trades = $this->exch->mytrades( array( 'symbol' => $market, 'timestamp' => 0, 'until' => time(), 'limit_trades' => 10000 ) );
				foreach( $market_trades as $market_trade ) {
					$market_trade['market'] = $market;
					$market_trade['exchange'] = "bitfinex";
					$market_trade['id'] = null;
					$market_trade['fee'] = null;
					$market_trade['total'] = null;
					array_push( $this->completed_orders, $market_trade );
				}
			}
			return $this->completed_orders;
		}

		public function get_markets() {
			$markets = $this->exch->symbols();
			$results = [];
			foreach( $markets as $market ) {
				$market = strtoupper( $market );
				array_push( $results, substr_replace($market, '-', 3, 0) );
			}
			return $results;
		}

		public function get_currencies() {
			return array( 'USD', 'BTC', 'LTC', 'DRK' );
		}

		public function deposit_address( $currency = "BTC" ){
			$wallet_types = array( "exchange", "deposit", "trading" );
			$addresses = [];
			foreach( $wallet_types as $wallet ) {
				$wallet_address = $this->exch->deposit_new( "bitcoin", $wallet, $renew = 0 );
				print_r( $wallet_address );
				if( $wallet_address['result'] === "success" ) {
					$wallet_address['wallet_type'] = $wallet;
					unset( $wallet_address['result'] );
					array_push( $addresses, $wallet_address );
				}
			}
			return $addresses[0];
		}
		
		public function deposit_addresses(){
			$currencies = array( "bitcoin", "litecoin", "darkcoin", "mastercoin" );
			$addresses = [];
			foreach( $currencies as $currency ) {
				$addresses = array_merge( $addresses, $this->deposit_address( $currency ) );
			}
			return $addresses;
		}

		public function get_balances() {
			if( isset( $this->balances ) )//internal cache
				return $this->balances;

			$balances = $this->exch->balances();
			$response = [];
			foreach( $balances as $balance ) {
				$balance['total'] = $balance['amount'];
				$balance['reserved'] = $balance['total'] - $balance['available'];
				$balance['btc_value'] = 0;
				$balance['pending'] = 0;
				$balance['currency'] = strtoupper( $balance['currency'] );
				unset( $balance['amount'] );
				array_push( $response, $balance );
			}

			$this->balances = $response;
			return $this->balances;
		}

		public function get_balance( $currency="BTC", $opts = array() ) {
			$balances = $this->get_balances();
			foreach( $balances as $balance )
				if( $balance['currency'] == $currency )
					if( isset( $opts['type'] ) ) {
						if( $opts['type'] == $balance['type'] )
							return $balance;
					} else
						return $balance;
		}

		public function get_market_summary( $market = "BTC-LTC" ) {
			$market = strtolower( str_replace( "-", "", $market ) );
			if( isset( $this->market_summaries ) )
				foreach( $this->market_summaries as $market_summary )
					if( $market_summary['pair'] = $market )
						return $market_summary;
			$this->get_market_summaries();
			return $this->get_market_summary( $market );
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;

			$market_summaries = $this->exch->symbols_details();
			$this->market_summaries = [];
			foreach( $market_summaries as $market_summary ) {
				$market_summary = array_merge( $market_summary, $this->exch->pubticker( $market_summary['pair'] ) );
				$market_summary['exchange'] = 'bitfinex';
				$market_summary['market'] = substr_replace( strtoupper( $market_summary['pair'] ), '-', 3, 0);
				$market_summary['display_name'] = $market_summary['pair'];
				$market_summary['minimum_order_size_base'] = $market_summary['minimum_order_size'];
				$market_summary['minimum_order_size_quote'] = null;
				$market_summary['result'] = true;
				$market_summary['created'] = null;
				$market_summary['vwap'] = null;
				$market_summary['base_volume'] = $market_summary['volume'];
				$market_summary['quote_volume'] = bcmul( $market_summary['base_volume'], $market_summary['mid'], 32 );
				$market_summary['btc_volume'] = null;
				$market_summary['frozen'] = null;
				$market_summary['percent_change'] = null;
				$market_summary['verified_only'] = null;
				$market_summary['open_buy_orders'] = null;
				$market_summary['open_sell_orders'] = null;
				$market_summary['market_id'] = null;

				unset( $market_summary['pair'] );
				unset( $market_summary['volume'] );
				unset( $market_summary['minimum_order_size'] );

				ksort( $market_summary );

				array_push( $this->market_summaries, $market_summary );
			}
			return $this->market_summaries;
		}

		public function get_trades( $market = "BTC-USD", $time = 0 ) {
			return $this->exch->trades( str_replace( "-", "", strtolower( $market ) ) );
		}

		public function get_all_trades( $time = 0 ) {
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

		public function get_orderbooks( $depth = 20 ) {
			$results = [];
			foreach( $this->get_markets() as $market )
				$results = array_merge( $results, $this->get_orderbook( $market, $depth ) );

			return $results;
		}

		public function get_orderbook( $market = 'BTC-USD', $depth = 20 ) {
			$book = $this->exch->book( str_replace( "-", "", strtolower( $market ) ) );
			$results = [];
			foreach( $book['bids'] as $bid ) {
				$bid['exchange'] = "bitfinex";
				$bid['market'] = $market;
				$bid['type'] = "buy";
				array_push( $results, $bid );
			}
			foreach( $book['asks'] as $ask ) {
				$ask['exchange'] = "bitfinex";
				$ask['market'] = $market;
				$ask['type'] = "sell";
				array_push( $results, $ask );
			}
			return $results;
		}

	}
?>