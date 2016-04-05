<?PHP

	class PoloniexAdapter implements CryptoExchange {

		public function __construct($Exch) {
			$this->exch = $Exch;
		}

		private function get_market_symbol( $market ) {
			$market = explode( "-", $market );
			$a = $market[1] . "_" . $market[0];

			return $a;
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
			$market = $this->get_market_symbol( $market );

			$trades = $this->exch->returnPublicTradeHistory( $market );

			foreach( $trades as $trade ) {
				array_push( $results, $trade );
			}
			
			return $results;
		}

		public function get_all_trades( $time = 0 ) {
			if( isset( $this->trades ) )
				return $this->trades;
			$this->trades = [];
			foreach( $this->get_markets() as $market ) {
				$trades = $this->get_trades( $market, $time );
				$this->trades = array_merge( $this->trades, $trades );
			}
			return $this->trades;
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 0 ) {
			$market = $this->get_market_symbol( $market );
			$orderbook = $this->exch->returnOrderBook( $market );
			$results = [];

			foreach( $orderbook['asks'] as $order ) {
				$order['market'] = $market;
				$order['type'] = 'ask';
			}
			foreach( $orderbook['bids'] as $order ) {
				$order['market'] = $market;
				$order['type'] = 'bid';
			}

			return $results;
		}

		public function get_orderbooks( $depth = 20 ) {
			$results = [];
			foreach( $this->get_markets() as $market )
				$results = array_merge( $results, $this->get_orderbook( $market, $depth ) );

			return $results;
		}

		public function buy( $market = 'LTC-BTC', $amount = 0, $price = 0, $type = "LIMIT", $opts = array() ) {
			$market = $this->get_market_symbol( $market );
			$buy = $this->exch->buy( $market, $price, $amount );
			if( isset( $buy['error'] ) )
				print_r( $buy );
			return $buy;
		}
		
		public function sell( $market = 'LTC-BTC', $amount = 0, $price = 0, $type = "LIMIT", $opts = array() ) {
			$market = $this->get_market_symbol( $market );
			$sell = $this->exch->sell( $market, $price, $amount );
			if( isset( $sell['error'] ) )
				print_r( $sell );
			return $sell;
		}

		public function get_open_orders( $market = "BTC-USD" ) {
			if( isset( $this->open_orders ) )
				return $this->open_orders;

			$market = $this->get_market_symbol( $market );
			$open_orders = $this->exch->returnOpenOrders( 'All' /*$market*/ );

			$results = [];
			foreach( $open_orders as $market => $orders ) {
				foreach( $orders as $order ) {
					$order['market'] = $market;
					$order['id'] = $order['orderNumber'];
					$order['price'] = $order['rate'];
					$order['timestamp_created'] = strtotime( $order['date'] . " UTC" );
					$order['exchange'] = null;
					$order['avg_execution_price'] = null;
					$order['side'] = null;
					$order['is_live'] = null;
					$order['is_cancelled'] = null;
					$order['is_hidden'] = null;
					$order['was_forced'] = null;
					$order['original_amount'] = null;
					$order['remaining_amount'] = null;
					$order['executed_amount'] = null;

					unset( $order['orderNumber'] );
					unset( $order['rate'] );
					unset( $order['total'] );
					unset( $order['date'] );
					unset( $order['margin'] );

					array_push( $results, $order );
				}
			}

			$this->open_orders = $results;
			return $this->open_orders;
		}

		public function get_completed_orders( $market = "BTC-USD" ) {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;

			$market = $this->get_market_symbol( $market );
			$completed_orders = $this->exch->returnTradeHistory( 'All' /*$market*/ );

			$results = [];
			foreach( $completed_orders as $market => $orders ) {
				foreach( $orders as $order ) {
					$order['market'] = $market;
					$order['price'] = $order['rate'];
					$order['timestamp'] = $order['date'];
					$order['exchange'] = null;
					$order['fee_currency'] = null;
					$order['fee_amount'] = null;
					$order['tid'] = null;
					$order['order_id'] = null;
					$order['id'] = null;

					unset( $order['globalTradeID' ] );
					unset( $order['tradeID' ] );
					unset( $order['date' ] );
					unset( $order['rate' ] );
					unset( $order['orderNumber' ] );
					unset( $order['category' ] );
					
					array_push( $results, $order );
				}
			}

			$this->completed_orders = $results;
			return $this->completed_orders;
		}

		//BTC_USD, BTC_LTC, LTC_USD, etc...
		public function get_markets() {
			$markets = array_map( 'strtoupper', array_keys( $this->exch->returnTicker() ) );
			return str_replace( '_', '-', $markets );
		}

		//BTC, LTC, USD, etc...
		public function get_currencies() {
			return array_map( 'strtoupper', array_keys( $this->exch->returnCurrencies() ) );
		}
		
		public function deposit_address( $currency = "BTC" ){
			return [];
		}
		
		public function deposit_addresses(){
			$addresses = $this->exch->returnDepositAddresses();
			$currencies = array_diff( $this->get_currencies(), array_keys( $addresses ) );
			foreach( $currencies as $currency ) {
				$this->exch->generateNewAddress( $currency );
			}
			$results = [];
			$addresses = $this->exch->returnDepositAddresses();
			foreach( $addresses as $currency => $address ) {
				$n_address = [];
				$n_address['address'] = $address;
				$n_address['currency'] = $currency;
				$n_address['wallet_type'] = "exchange";
				array_push( $results, $n_address );
			}
			
			return $results;
		}

		public function get_balances() {
			if( isset( $this->balances ) )//internal cache
				return $this->balances;

			$balances = $this->exch->returnCompleteBalances();
			$response = [];
			foreach( $balances as $key => $balance ) {
				$balance['type'] = 'exchange'; //Or, is it all accounts?
				$balance['currency'] = $key;
				$balance['reserved'] = isset( $balance['onOrders'] ) ? $balance['onOrders'] : 0;
				$balance['total'] = $balance['available'] + $balance['reserved'];
				$balance['btc_value'] = $balance['btcValue'];
				$balance['pending'] = 0;

				unset( $balance['onOrders'] );
				unset( $balance['btcValue'] );

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
			$market = strtoupper($market);
			$prices = $this->exch->returnTicker();
			if(isset($prices[$market]))
				return $prices[$market];
			else
				return array();
		}

		public function get_market_summaries() {
			$market_summaries = $this->exch->returnTicker();
			$response = [];
			foreach( $market_summaries as $key => $market_summary ) {
				$market_summary['market'] = strtoupper( str_replace( "_", "-", $key ) );
				$msmn = explode( "-", $market_summary['market'] );
				$market_summary['market'] = $msmn[1] . "-" . $msmn[0];
				$market_summary['exchange'] = "poloniex";
				$market_summary['last_price'] = $market_summary['last'];
				$market_summary['ask'] = is_null( $market_summary['lowestAsk'] ) ? 0 : $market_summary['lowestAsk'];
				$market_summary['bid'] = is_null( $market_summary['highestBid'] ) ? 0 : $market_summary['highestBid'];
				$market_summary['quote_volume'] = $market_summary['baseVolume'];
				$market_summary['base_volume'] = $market_summary['quoteVolume'];
				$market_summary['btc_volume'] = null;
				$market_summary['low'] = $market_summary['low24hr'];
				$market_summary['high'] = $market_summary['high24hr'];
				$market_summary['display_name'] = $market_summary['market'];
				$market_summary['percent_change'] = $market_summary['percentChange'];
				$market_summary['frozen'] = $market_summary['isFrozen'];
				$market_summary['result'] = true;
				$market_summary['created'] = null;
				$market_summary['verified_only'] = null;
				$market_summary['expiration'] = null;
				$market_summary['initial_margin'] = null;
				$market_summary['maximum_order_size'] = null;
				$market_summary['mid'] = null;
				$market_summary['minimum_margin'] = null;

				if( strpos( $market_summary['market'], "-XMR" ) !== FALSE )
					$market_summary['minimum_order_size_quote'] = '0.01000000';
				if( strpos( $market_summary['market'], "-USDT" ) !== FALSE )
					$market_summary['minimum_order_size_quote'] = '0.01000000';
				if( strpos( $market_summary['market'], "-BTC" ) !== FALSE )
					$market_summary['minimum_order_size_quote'] = '0.00050000';

				$market_summary['minimum_order_size_base'] = null;
				$market_summary['price_precision'] = 8;
				$market_summary['timestamp'] = null;
				$market_summary['vwap'] = null;
				$market_summary['open_buy_orders'] = null;
				$market_summary['open_sell_orders'] = null;
				$market_summary['market_id'] = null;

				unset( $market_summary['last'] );
				unset( $market_summary['lowestAsk'] );
				unset( $market_summary['highestBid'] );
				unset( $market_summary['baseVolume'] );
				unset( $market_summary['quoteVolume'] );
				unset( $market_summary['low24hr'] );
				unset( $market_summary['high24hr'] );
				unset( $market_summary['percentChange'] );
				unset( $market_summary['isFrozen'] );

				ksort( $market_summary );

				array_push( $response, $market_summary );
			}
			return $response;
		}

		//_____Cancel all orders:
		function cancel_all() {
			$markets = $this->get_markets();
			$results = [];
			foreach( $markets as $key ) {
				$key = str_replace( "-", "_", $key );
				$open_orders = $this->get_open_orders($key); //this will change to be standard across all adapters...
				if( is_array( $open_orders ) )
					foreach( $open_orders as $open_order )
						foreach( $open_order as $order )
							if( isset( $order['orderNumber'] ) )
								array_push($results, $this->cancel($order['orderNumber'], array( 'market' => $key ) ) );
			}
			return array( 'success' => true, 'error' => false, 'message' => $results );
		}

		public function cancel( $orderid="1", $opts = array() ) {//requires market to be passed in
			return $this->exch->cancelOrder( $opts['market'], $orderid );
		}

	}

?>