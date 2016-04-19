<?PHP

	class BitfinexAdapter extends CryptoBase implements CryptoExchange {

		public function __construct( $Exch ) {
			$this->exch = $Exch;
		}

		private function get_market_symbol( $market )
		{
			return strtoupper( substr_replace($market, '-', 3, 0) );
		}

		private function unget_market_symbol( $market )
		{
			return str_replace( "-", "", strtolower( $market ) );
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
			return $this->exch->order_cancel( (int)$orderid );
		}

		public function cancel_all() {
			$result = $this->exch->order_cancel_all();
			if( $result['result'] == "All orders cancelled" ) {
				return array( 'success' => true, 'error' => false, 'message' => $result );
			}
			return array( 'success' => false, 'error' => true, 'message' => $result );
		}

		public function buy( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$buy = $this->exch->order_new( $this->unget_market_symbol( $pair ), $amount, $price, "bitfinex", "buy", "exchange limit", true );
			if( isset( $sell['message'] ) )
				print_r( $buy );
			return $buy;
		}
		
		public function sell( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$sell = $this->exch->order_new( $this->unget_market_symbol( $pair ), $amount, $price, "bitfinex", "sell", "exchange limit", true );
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
					$open_order['market'] = $this->get_market_symbol( $open_order['symbol'] );
					$open_order['timestamp_created'] = $open_order['timestamp'];
					$open_order['amount'] = $open_order['original_amount'];

					unset( $open_order['timestamp'] );
					unset( $open_order['oco_order'] );
					unset( $open_order['symbol'] );
					array_push( $this->open_orders, $open_order );
				}
			return $this->open_orders;
		}

		public function get_completed_orders( $market="BTC-USD", $limit=100 ) {
			$completed_orders = [];
			$market_trades = $this->exch->mytrades( array( 'symbol' => $this->unget_market_symbol( $market ), 'timestamp' => 0, 'until' => time(), 'limit_trades' =>  $limit ) );
			foreach( $market_trades as $market_trade ) {
				$market_trade['market'] = $market;
				$market_trade['exchange'] = "bitfinex";
				$market_trade['id'] = null;
				$market_trade['fee'] = null;
				$market_trade['total'] = null;
				array_push( $completed_orders, $market_trade );
			}
			return $completed_orders;
		}

		public function get_markets() {
			$markets = $this->exch->symbols();
			$results = [];
			foreach( $markets as $market ) {
				array_push( $results, $this->get_market_symbol( $market ) );
			}
			return $results;
		}

		public function get_currencies() {
			return array( 'USD', 'BTC', 'LTC', 'ETH' );
		}

		public function deposit_address( $currency = "BTC", $wallet_type = "exchange" ){
			switch( $currency ) {
				case "BTC": $currency="bitcoin"; break;
				case "LTC": $currency="litecoin"; break;
				case "ETH": $currency="ethereum"; break;
				default: return array( "error" => "no" );
			}
			$wallet_address = $this->exch->deposit_new( $currency, $wallet_type, $renew = 0 );

			if( $wallet_address['result'] === "success" ) {
				$wallet_address['wallet_type'] = $wallet_type;
				unset( $wallet_address['result'] );
				unset( $wallet_address['method'] );
				return $wallet_address;
			}
			return array( "error" => "no" );
		}
		
		public function deposit_addresses(){
			$currencies = $this->get_currencies();
			$wallet_types = array( "exchange", "deposit", "trading" );
			$addresses = [];
			foreach( $currencies as $currency ) {
				foreach( $wallet_types as $wallet_type ) {
					$address = $this->deposit_address( $currency, $wallet_type );
					if( ! isset( $address['error'] ) ) {
						array_push( $addresses, $address );
					}
				}
			}
			return $addresses;
		}

		public function get_balances() {
			/*if( isset( $this->balances ) )//internal cache
				return $this->balances;*/

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

		public function get_market_summary( $market = "ETH-BTC" ) {
			if( isset( $this->market_summaries ) ) {
				foreach( $this->market_summaries as $market_summary ) {
					if( $market_summary['market'] == $market ) {
						return $market_summary;
					}
				}
			}
			$this->get_market_summaries();
			return $this->get_market_summary( $market );//(core dumped)
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;

			$market_summaries = $this->exch->symbols_details();
			$this->market_summaries = [];
			foreach( $market_summaries as $market_summary ) {
				$market_summary = array_merge( $market_summary, $this->exch->pubticker( $market_summary['pair'] ) );
				$market_summary['exchange'] = 'bitfinex';
				$market_summary['market'] = $this->get_market_symbol( $market_summary['pair'] );
				$market_summary['display_name'] = $market_summary['market'];
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
			return $this->exch->trades( $this->unget_market_symbol( $market ) );
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
			$book = $this->exch->book( $this->unget_market_symbol( $market ) );
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