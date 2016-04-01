<?PHP

	class BterAdapter implements CryptoExchange {

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

		public function get_all_trades( $time = 0 ) {
			if( isset( $this->trades ) )
				return $this->trades;
			$this->trades = [];
			foreach( $this->get_markets() as $market ) {
				$this->trades = array_merge( $this->trades, $this->get_trades( $market, $time ) );
			}
			return $this->trades;
		}

		public function get_trades( $market = "BTC-USD", $time = 0 ) {
			$results = [];
			$curs = explode( "-", $market );
			$trades = $this->exch->trade_history( $curs[0], $curs[1] );

			if( $trades['result'] ) {
				foreach( $trades['data'] as $trade ) {
					$trade['market'] = $market;
					$trade['timestamp'] = $trade['date'];
					$trade['exchange'] = null;

					unset( $trade['date'] );
					array_push( $results, $trade );
				}
			}
			return $results;
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 0 ) {
			$curs = explode( "-", $market );
			$orderbook = $this->exch->depth( $curs[0], $curs[1] );
			$results = [];
			$n_orderbook = [];

			if( $orderbook['result'] ) {
				foreach( $orderbook['bids'] as $order ) {
					$order['type'] = 'bid';
					array_push( $results, $order );
				}
				foreach( $orderbook['bids'] as $order ) {
					$order['type'] = 'ask';
					array_push( $results, $order );
				}
				foreach( $results as $order ) {
					$order['market'] = $market;
					$order['price'] = $order[0];
					$order['amount'] = $order[1];
					$order['timestamp'] = null;
					$order['exchange'] = null;

					unset( $order[0] );
					unset( $order[1] );

					array_push( $n_orderbook, $order );
				}
			}

			return $n_orderbook;
		}

		public function get_orderbooks( $depth = 20 ) {
			$results = [];
			foreach( $this->get_markets() as $market )
				$results = array_merge( $results, $this->get_orderbook( $market, $depth ) );

			return $results;
		}

		public function cancel( $orderid="1", $opts = array() ) {
			return $this->exch->cancelorder( array( 'order_id' => $orderid ) );
		}
		
		public function cancel_all() {
			$orders = $this->get_open_orders();
			$results = array();
			foreach( $orders as $order ) {
				$order['detailedInfo'] = $this->exch->cancelorder( array( 'order_id' => $order['id'] ) );
				array_push($results,$order);
			}
			return array( 'success' => true, 'error' => false, 'message' => $results );
		}

		public function buy( $pair='BTC-LTC', $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = str_replace( "-", "_", strtolower( $pair ) );
			$buy = $this->exch->placeorder( array('pair' => $pair, 'type' => 'BUY', 'rate' => $price, 'amount' => $amount ) );
			if( $buy['message'] != "Success" )
				print_r( $buy );
		}
		
		public function sell( $pair='BTC-LTC', $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = str_replace( "-", "_", strtolower( $pair ) );
			$sell = $this->exch->placeorder( array('pair' => $pair, 'type' => 'SELL', 'rate' => $price, 'amount' => $amount ) );
			if( $sell['message'] != "Success" )
				print_r( $sell );
		}

		public function get_open_orders( $market = "BTC-USD" ) {
			if( isset( $this->open_orders ) )
				return $this->open_orders;
			$orderlist = $this->exch->orderlist();

			$results = [];
			foreach( $orderlist['orders'] as $order ) {

				$order['market'] = $order['pair'];
				$order['price'] = $order['rate'];
				$order['timestamp_created'] = $order['time_unix'];
				$order['exchange'] = "bter";
				$order['avg_execution_price'] = null;
				$order['side'] = null;
				$order['is_live'] = null;
				$order['is_cancelled'] = null;
				$order['is_hidden'] = null;
				$order['was_forced'] = null;
				$order['original_amount'] = null;
				$order['remaining_amount'] = null;
				$order['executed_amount'] = null;

				unset( $order['oid'] );
				unset( $order['pair'] );
				unset( $order['time_unix'] );
				unset( $order['date'] );
				unset( $order['margin'] );

				array_push( $results, $order );

			}

			$this->open_orders = $results;
			return $this->open_orders;
		}

		public function get_completed_orders( $market = "BTC-USD" ) {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$markets = $this->get_markets();
			$this->completed_orders = [];
			foreach( $markets as $market ) {
				$market = str_replace( "-", "_", strtolower( $market ) );
				$trades24hours = $this->exch->mytrades( array( 'pair' => $market ) );
				array_merge( $this->completed_orders, $trades24hours['trades'] );
			}
			return $this->completed_orders;
		}

		public function get_markets() {
			$markets = $this->exch->pairs();
			$markets = str_replace('_', '-', $markets );
			return array_map( 'strtoupper', $markets );
		}

		public function get_currencies() {
			$currencies = $this->exch->marketlist();
			$response = [];
			foreach( $currencies['data'] as $currency ) {
				array_push( $response, $currency['symbol'] );
			}
			return array_unique( array_map('strtoupper',$response) );
		}

		public function deposit_address($currency="BTC"){
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}
		
		public function deposit_addresses(){
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_balances() {
			if( isset( $this->balances ) )//internal cache
				return $this->balances;

			$balances = $this->exch->getfunds();
			$response = [];
			$currencies = $this->get_currencies();
			foreach( $currencies as $currency ) {
				$balance = [];
				$balance['type'] = "exchange";
				$balance['currency'] = strtoupper($currency);
				$balance['available'] = isset( $balances['available_funds'][$currency] ) ? $balances['available_funds'][$currency] : 0;
				$balance['reserved'] = isset( $balances['locked_funds'][$currency] ) ? $balances['locked_funds'][$currency] : 0;
				$balance['total'] = $balance['available'] + $balance['reserved'];
				$balance['pending'] = 0;
				$balance['btc_value'] = 0;
				array_push( $response, $balance );
			}

			$this->balances = $response;
			return $this->balances;
		}

		public function get_balance( $currency="BTC" ) {
			$balances = $this->get_balances();
			foreach( $balances as $balance )
				if( $balance['currency'] == $currency )
					return $balance;
		}

		public function get_market_summary( $market = "BTC-LTC" ) {
			$market = explode( "-", strtolower( $market ) );
			return $this->exch->ticker( $market[0], $market[1] );;
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;

			$tickers = $this->exch->tickers();

			$this->market_summaries = [];

			$market_info = $this->exch->marketinfo();
			$market_info = $market_info['pairs'];
			$markets = [];
			foreach( $market_info as $market ) {
				$key = array_keys( $market );
				$key = $key[0];
				$markets[$key] = $market[$key];
			}

			foreach( $tickers as $key => $market_summary ) {
				$market_summary['market'] = strtoupper( str_replace( "_", "-", $key ) );
				$market_summary['exchange'] = "bter";
				$market_summary = array_merge( $market_summary, $markets[$key] );
				$curs = explode( "_", $key );
				$cur1 = $curs[0];
				$cur2 = $curs[1];
				$market_summary['mid'] = $market_summary['avg'];
				$market_summary['bid'] = is_null( $market_summary['buy'] ) ? 0 : $market_summary['buy'];
				$market_summary['ask'] = is_null( $market_summary['sell'] ) ? 0 : $market_summary['sell'];
				$market_summary['last_price'] = $market_summary['last'];
				$market_summary['display_name'] = $market_summary['market'];
				$market_summary['percent_change'] = $market_summary['rate_change_percentage'];
				$market_summary['base_volume'] = $market_summary['vol_'.$cur1];
				$market_summary['quote_volume'] = $market_summary['vol_'.$cur2];
				$market_summary['btc_volume'] = null;
				$market_summary['created'] = null;
				$market_summary['open_buy_orders'] = null;
				$market_summary['open_sell_orders'] = null;
				$market_summary['vwap'] = null;
				$market_summary['frozen'] = null;
				$market_summary['expiration'] = null;
				$market_summary['verified_only'] = null;
				$market_summary['initial_margin'] = null;
				$market_summary['maximum_order_size'] = null;
				$market_summary['minimum_margin'] = null;
				$market_summary['minimum_order_size_quote'] = $market_summary['min_amount'];
				$market_summary['minimum_order_size_base'] = null;
				$market_summary['price_precision'] = $market_summary['decimal_places'];
				$market_summary['timestamp'] = null;
				$market_summary['vwap'] = null;
				$market_summary['market_id'] = null;

				unset( $market_summary['fee'] );
				unset( $market_summary['min_amount'] );
				unset( $market_summary['avg'] );
				unset( $market_summary['decimal_places'] );
				unset( $market_summary['buy'] );
				unset( $market_summary['sell'] );
				unset( $market_summary['last'] );
				unset( $market_summary['rate_change_percentage'] );
				unset( $market_summary['vol_'.$cur1] );
				unset( $market_summary['vol_'.$cur2] );

				ksort( $market_summary );

				array_push( $this->market_summaries, $market_summary );
			}
			return $this->market_summaries;
		}

	}

?>