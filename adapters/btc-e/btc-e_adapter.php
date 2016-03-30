<?PHP

	class BtceAdapter implements CryptoExchange {

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
			$market = str_replace( "-", "_", strtolower( $market ) );
			$trades = $this->exch->trades( $market );
			foreach( $trades[$market] as $trade ) {
				$trade['market'] = $market;
				$trade['exchange'] = null;
				array_push( $results, $trade );
			}

			return $results;
		}

		public function get_all_trades( $time = 0 ) {
			if( isset( $this->trades ) )
				return $this->trades;
			$this->trades = [];
			foreach( $this->get_markets() as $market ) {
				$this->trades = $this->get_trades( $market, $time );
			}
			return $this->trades;
		}

		public function get_orderbooks( $depth = 20 ) {
			$results = [];
			foreach( $this->get_markets() as $market )
				$results = array_merge( $results, $this->get_orderbook( $market, $depth ) );

			return $results;
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 0 ) {
			$orderbooks = $this->exch->depth( str_replace( "-", "_", strtolower( $market ) ) );
			$n_orderbooks = [];
			$m_orderbooks = [];
			
			if( isset( $orderbooks['asks'] ) )
				foreach( $orderbooks['asks'] as $orderbook ) {
					array_push( $n_orderbooks, $orderbook );
				}
			if( isset( $orderbooks['bids'] ) )
				foreach( $orderbooks['bids'] as $orderbook ) {
					array_push( $n_orderbooks, $orderbook );
				}

			foreach( $n_orderbooks as $orderbook ) {
				array_push( $m_orderbooks, $orderbook );
			}

			return $m_orderbooks;
		}

		public function cancel($orderid="1", $opts = array() ) {
			return $this->exch->CancelOrder( $arr = array( 'order_id' => $orderid ) );
		}
		
		public function cancel_all() {
			$results = array();
			$orders = $this->get_open_orders();
			foreach( $orders as $order ) {
				array_push( $results, $this->cancel( $order['id'] ) );
			}
				
			return array( 'success' => true, 'error' => false, 'message' => $results );
		}

		public function buy($pair='BTC-USD',$amount="0",$price="0",$type="LIMIT",$opts=array()) {
			$pair = strtolower( $pair );
			$pair = str_replace( "-", "_", $pair );
			$buy = $this->exch->Trade( array( 'pair' => $pair, 'type' => 'buy', 'amount' => (float)$amount, 'rate' => (float)$price ) );

			if( isset( $buy['error'] ) )
				print_r( $buy );

			return $buy;
		}
		
		public function sell($pair='BTC-USD',$amount="0",$price="0",$type="LIMIT",$opts=array()) {
			$pair = strtolower( $pair );
			$pair = str_replace( "-", "_", $pair );
			$sell = $this->exch->Trade( array( 'pair' => $pair, 'type' => 'sell', 'amount' => (float)$amount, 'rate' => (float)$price ) );

			if( isset( $sell['error'] ) )
				print_r( $sell );

			return $sell;
		}

		public function get_open_orders( $market = 'BTC-USD' ) {
			//if( isset( $this->open_orders ) )
				//return $this->open_orders;
			$open_orders = $this->exch->ActiveOrders( $arr = array() );
			$this->open_orders = [];
			if( isset( $open_orders['return'] ) ) {
				foreach( $open_orders['return'] as $order_id => $open_order ) {
					$open_order['id'] = $order_id;
					array_push( $this->open_orders, $open_order );
				}
			}
			return $this->open_orders;
		}

		public function get_completed_orders( $market = 'BTC-USD' ) {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$completed_orders = $this->exch->TradeHistory( array( 'count' => 1000, 'order' => 'DESC', 'since' => 0, 'end' => time() ) );;
			$this->completed_orders = [];
			if( isset( $completed_orders['return'] ) ) {
				foreach( $completed_orders['return'] as $completed_order ) {
					$completed_order['market'] = $completed_order['pair'];
					unset( $completed_order['pair'] );
					unset( $completed_order['rate'] );
					unset( $completed_order['is_your_order'] );

					$completed_order['price'] = null;
					$completed_order['exchange'] = null;
					$completed_order['fee_currency'] = null;
					$completed_order['fee_amount'] = null;
					$completed_order['tid'] = null;
					$completed_order['id'] = null;
					$completed_order['fee'] = null;
					$completed_order['total'] = null;

					array_push( $this->completed_orders, $completed_order );
				}
			}
			return $this->completed_orders;
		}

		public function get_markets() {
			return array(	'BTC-USD', //not very dynamic
							'BTC-RUR', 
							'BTC-EUR', 
							'LTC-BTC', 
							'LTC-USD', 
							'LTC-RUR', 
							'LTC-EUR', 
							'NMC-BTC', 
							'NMC-USD', 
							'NVC-BTC', 
							'NVC-USD', 
							'USD-RUR', 
							'EUR-USD', 
							'EUR-RUR', 
							'PPC-BTC', 
							'PPC-USD' );
		}

		public function get_currencies() {
			return array(	'USD', 
							'RUR', 
							'EUR', 
							'BTC', 
							'LTC', 
							'NMC', 
							'NVC', 
							'PPC' );
		}
		
		public function deposit_address($currency="BTC"){
			switch( $currency ) {
				case "BTC":
					return array( 'currency' => "BTC", 'address' => "1jPtEamiPHn2NaPXab29ruSAparsvrUre", "wallet_type" => "exchange", "method" => null );
				case "LTC":
					return array( 'currency' => "LTC", 'address' => "LZrNNQtK4yDzwEjj2VszEm529UaDDDsdPH", "wallet_type" => "exchange", "method" => null );
				case "NMC":
					return array( 'currency' => "NMC", 'address' => "NEtAMTUgqyD4w7DEA414PRSFjhoVJstP7W", "wallet_type" => "exchange", "method" => null );
				case "NVC":
					return array( 'currency' => "NVC", 'address' => "4KwnoXR5nKxPebxryruugbuqP7SdiuWxP3", "wallet_type" => "exchange", "method" => null );
				case "PPC":
					return array( 'currency' => "PPC", 'address' => "PVduuiWTCm3jPaPr9JTPyBhAVrhZuEER5D", "wallet_type" => "exchange", "method" => null );
				default:
					return FALSE;
			}
		}
		
		public function deposit_addresses(){
			$addresses = [];
			$currencies = $this->get_currencies();
			foreach( $currencies as $currency ) {
				$address = $this->deposit_address( $currency );
				if( $address )
					array_push( $addresses, $address );
			}
			return $addresses;
		}

		public function get_balances() {
			if( isset( $this->balances ) )
				return $this->balances;

			$balances = $this->exch->getInfo();
			$balances = $balances['return']['funds'];
			$open_orders = $this->get_open_orders();
			if( isset( $open_orders['return'] ) )
				$open_orders = $open_orders['return'];
			else
				$open_orders = [];
			$res_balances = [];

			foreach( $open_orders as $open_order ) {
				$curs = explode( "_", $open_order['pair'] );
				$base_cur = $curs[0];
				$quote_cur = $curs[1];

				if( ! isset( $res_balances[$base_cur] ) )
					$res_balances[$base_cur] = 0;
				if( ! isset( $res_balances[$quote_cur] ) )
					$res_balances[$quote_cur] = 0;

				if( $open_order['type'] == "buy" )
					$res_balances[$quote_cur] += $open_order['amount'] * $open_order['rate'];
				if( $open_order['type'] == "sell" )
					$res_balances[$base_cur] += $open_order['amount'];
			}

			$response = [];
			foreach( $balances as $key => $avail ) {
				$balance = [];
				$balance['type'] = "exchange";
				$balance['pending'] = 0;
				$balance['currency'] = strtoupper( $key );
				$balance['available'] = $avail;
				$balance['btc_value'] = 0;
				$balance['reserved'] = isset( $res_balances[$key] ) ? $res_balances[$key] : 0;
				$balance['total'] = $balance['reserved'] + $balance['available'];

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
			$market = strtolower( str_replace( "-", "_", $market ) );
			return $this->exch->ticker( $market );
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;
			$this->market_summaries = [];
			foreach( $this->get_markets() as $market ) {
				$market_summary = $this->get_market_summary( $market );
				$key = array_keys( $market_summary );
				$key = $key[0];
				$market_summary = $market_summary[$key];
				$market_summary['market'] = strtoupper( str_replace( "_", "-", $key ) );
				$market_summary['exchange'] = "btc-e";
				$info = $this->exch->info( $market );
				$market_summary['timestamp'] = $info['server_time'];
				$info = $info['pairs'][$key];
				$market_summary = array_merge( $market_summary, $info );
				$market_summary['ask'] = is_null( $market_summary['sell'] ) ? 0 : $market_summary['sell'];
				$market_summary['bid'] = is_null( $market_summary['buy'] ) ? 0 : $market_summary['buy'];
				$market_summary['quote_volume'] = $market_summary['vol'];
				$market_summary['mid'] = $market_summary['avg'];
				$market_summary['base_volume'] = bcdiv( $market_summary['quote_volume'], $market_summary['mid'], 32 );
				$market_summary['btc_volume'] = null;
				$market_summary['last_price'] = $market_summary['last'];
				$market_summary['display_name'] = $market_summary['market'];
				$market_summary['result'] = true;
				$market_summary['created'] = null;
				$market_summary['open_buy_orders'] = null;
				$market_summary['open_sell_orders'] = null;
				$market_summary['percent_change'] = null;
				$market_summary['frozen'] = $market_summary['hidden'];
				$market_summary['verified_only'] = null;
				$market_summary['initial_margin'] = null;
				$market_summary['expiration'] = null;
				$market_summary['maximum_order_size'] = null;
				$market_summary['price_precision'] = $market_summary['decimal_places'];
				$market_summary['minimum_order_size_base'] = $market_summary['min_amount'];
				$market_summary['minimum_order_size_quote'] = null;
				$market_summary['minimum_margin'] = null;
				$market_summary['vwap'] = null;
				$market_summary['market_id'] = null;

				unset( $market_summary['vol'] );
				unset( $market_summary['avg'] );
				unset( $market_summary['buy'] );
				unset( $market_summary['sell'] );
				unset( $market_summary['last'] );
				unset( $market_summary['updated'] );
				unset( $market_summary['vol_cur'] );
				unset( $market_summary['decimal_places'] );
				unset( $market_summary['fee'] );
				unset( $market_summary['hidden'] );
				unset( $market_summary['max_price'] );
				unset( $market_summary['min_amount'] );
				unset( $market_summary['min_price'] );

				ksort( $market_summary );

				array_push( $this->market_summaries, $market_summary );
			}
			return $this->market_summaries;
		}

	}
?>