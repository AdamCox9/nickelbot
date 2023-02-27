<?PHP

	class KrakenAdapter extends CryptoBase implements CryptoExchange {

		public function __construct( $Exch ) {
			$this->exch = $Exch;
		}

		private function get_market_symbol( $market ) {
			if( strlen( $market ) == 6 )
				return substr( $market, 0, 3 ) . '-' . substr( $market, 3, 3 );
			if( strlen( $market ) == 7 )
				return substr( $market, 0, 4 ) . '-' . substr( $market, 4, 3 );
			if( strlen( $market ) == 8 )
				return substr( $market, 0, 4 ) . '-' . substr( $market, 4, 4 );
			if( strlen( $market ) == 10 )
				return substr( $market, 0, 4 ) . '-' . substr( $market, 4, 6 );
		}

		private function unget_market_symbol( $market ) {
			return str_replace( '-', '', $market );
		}

		public function get_markets() {
			if( isset( $this->markets ) )
				return $this->markets;
				
			$market_summaries = $this->get_market_summaries();			
			
			$markets = [];
			foreach( $market_summaries as $market_summary ) {
				array_push( $markets, $market_summary['market'] );
			}
			
			$this->markets = $markets;
			return 	$this->markets;
		}

		//Need to change currencies from array to array of arrays everywhere else also.
		public function get_currencies() {
			if( isset( $this->currencies ) )
				return $this->currencies;
				
			$currencies = $this->exch->Assets();		
			$results = [];
			foreach( $currencies['result'] as $key => $currency ) {
				$currency['currency'] = $key;
				$currency['collateral_value'] = isset( $currency['collateral_value'] ) ? $currency['collateral_value'] : null;
				array_push( $results, $currency );
			}
			return $results;
		}

/*****

<pair_name> = pair name
    a = ask array(<price>, <whole lot volume>, <lot volume>),
    b = bid array(<price>, <whole lot volume>, <lot volume>),
    c = last trade closed array(<price>, <lot volume>),
    v = volume array(<today>, <last 24 hours>),
    p = volume weighted average price array(<today>, <last 24 hours>),
    t = number of trades array(<today>, <last 24 hours>),
    l = low array(<today>, <last 24 hours>),
    h = high array(<today>, <last 24 hours>),
    o = today's opening price
Note: Today's prices start at 00:00:00 UTC

 *****/

		public function get_market_summary( $market = "ETH-BTC" ) {
			$market_summaries = $this->get_market_summaries();
			foreach( $market_summaries as $market_summary ) {
				if( $market_summary['market'] == $market )
					return $market_summary;
			}
			/* 	It might be better to get Market Data for a specific Market rather than filling the
				entire Market Summary at once since it hits the server for each pair.
				Market Summary is cached in class variable to mitigate this. Fill the market summary data and reuse it.
			*/

		}

		public function get_market_summaries( ) {
			$results = [];

			$AssetPairs = $this->exch->AssetPairs( );
			if( $AssetPairs['error'] ) {
				return array( 'ERROR' => $AssetPairs['error'] );
			}
			
			$Ticker = $this->exch->Ticker( );
			if( $Ticker['error'] ) {
				return array( 'ERROR' => $Ticker['error'] );
			}

			$market_summaries = array_merge_recursive( $AssetPairs['result'], $Ticker['result'] );
			//print_r( array_keys( $market_summaries['ZRXXBT'] ) );
			//die( "TEST" );

			$results = [];
			
			foreach( $market_summaries as $key => $market_summary ) {
				$market_summary['market'] = $key;

				$OHLC = $this->exch->OHLC( $key );
				if( $OHLC['error'] ) {
					return array( 'ERROR' => $OHLC['error'] );
				}
				$market_summary['OHLC'] = $OHLC;
	
				$Depth = $this->exch->Depth( $key );
				if( $Depth['error'] ) {
					return array( 'ERROR' => $Depth['error'] );
				}
				$market_summary['Depth'] = $Depth;
	
				$Trades = $this->exch->Trades( $key );
				if( $Trades['error'] ) {
					return array( 'ERROR' => $Trades['error'] );
				}
				$market_summary['Trades'] = $Trades;
	
				$Spread = $this->exch->Spread( $key );
				if( $Spread['error'] ) {
					return array( 'ERROR' => $Spread['error'] );
				}
				$market_summary['Spread'] = $Spread;

				array_push( $results, $this->standardize_market_summary( $market_summary ) );
			}

			return $results;
		}

		private function standardize_market_summary( $market_summary ) {
		
			print_r( array_keys( $market_summary ) );
			die( "TEST" );
		
			$market_summary['exchange'] = "Kraken";
			$market_summary['ask'] = $market_summary['a'][0];
			$market_summary['bid'] = $market_summary['b'][0];
			$market_summary['high'] = $market_summary['h'][0];
			$market_summary['low'] = $market_summary['l'][0];
			$market_summary['base_volume'] = $market_summary['v'][1];
			$market_summary['btc_volume'] = null;
			$market_summary['created'] = null;
			$market_summary['display_name'] = $market_summary['market'];
			$market_summary['expiration'] = null;
			$market_summary['frozen'] = null;
			$market_summary['initial_margin'] = null;
			$market_summary['last_price'] = $market_summary['c'][0];
			$market_summary['market_id'] = null;
			$market_summary['maximum_order_size'] = null;
			$market_summary['mid'] = ( $market_summary['ask'] + $market_summary['bid'] ) / 2;
			$market_summary['minimum_margin'] = null;
			$curs_bq = explode( "-", $market_summary['market'] );
			$base_cur = $curs_bq[0];
			$market_summary['minimum_order_size_base'] = $market_summary[ 'ordermin' ];
			$market_summary['minimum_order_size_quote'] = null;
			$market_summary['open_buy_orders'] = null;
			$market_summary['open_sell_orders'] = null;
			$market_summary['percent_change'] = null;
			$market_summary['price_precision'] = $this->AssetPairs[ $key ][ 'cost_decimals' ];
			$market_summary['quote_volume'] = bcmul( $market_summary['base_volume'], number_format( $market_summary['mid'], 8, ".", "" ), 32 );;
			$market_summary['result'] = null;
			$market_summary['timestamp'] = null;
			$market_summary['verified_only'] = null;
			$market_summary['vwap'] = null;

			unset( $market_summary['a'] );
			unset( $market_summary['b'] );
			unset( $market_summary['c'] );
			unset( $market_summary['v'] );
			unset( $market_summary['p'] );
			unset( $market_summary['t'] );
			unset( $market_summary['l'] );
			unset( $market_summary['h'] );
			unset( $market_summary['o'] );
			
			return $market_summary;
		}

		public function get_balance( $currency="BTC", $opts = array() ) {
			$balances = $this->get_balances();
			foreach( $balances as $balance )
				if( $balance['currency'] == $currency )
					return $balance;
			return array( 'ERROR' => 'CURRENCY_NOT_FOUND' );
		}

		//TODO allow option CACHE=TRUE to be passed in:
		public function get_balances() {
			if( isset( $this->balances ) )
				return $this->balances;
		
			$balances = $this->exch->Balance();
			
			$results = [];
			foreach( $balances['result'] as $key => $available ) {
				$balance['currency'] = $key;
				$balance['available'] = $available;
				$balance['type'] = 'exchange';
				$balance['total'] = $available;
				$balance['reserved'] = 0;
				$balance['pending'] = 0;
				$balance['btc_value'] = null;

				$results[$key] = $balance;
			}

			$this->balances = $results;
			return $this->balances;
		}

		public function buy( $pair="ETH-BTC", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
		
			$pair = $this->unget_market_symbol( $pair );
			
			/*echo "pair: $pair\n";
			echo "type: $type\n";
			echo "price: $price\n";
			echo "amount: $amount\n";*/
			
			$result = $this->exch->AddOrder( $pair , "buy", strtolower( $type ), $price, $amount );

			if( $result['error'] != false )
				$result['message'] = $result['error'];
			return $result;
		}
		
		public function sell( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$result = $this->exch->AddOrder( $this->unget_market_symbol( $pair ), "sell", strtolower( $type ), $price, $amount );
			if( $result['error'] != false )
				$result['message'] = $result['error'];
			return $result;
		}

		public function update_order( $pair = "", $order_id=0, $amount=0, $price=0, $opts=array() ) {
			$result = $this->exch->EditOrder(  $this->unget_market_symbol( $pair ), $order_id, $amount, $price );
			if( $result['error'] != false )
				$result['message'] = $result['error'];
			return $result ;
		}

		public function cancel( $orderid="1", $opts = array() ) {
			$this->exch->CancelOrder( $orderid );
		}

		public function cancel_all() {
			$open_orders = $this->get_open_orders();
			foreach( $open_orders as $open_order )
				$this->cancel( $open_order['id'] );
		}

		public function get_open_orders( $market="BTC-USD", $limit=100 ) {
			$open_orders = $this->exch->OpenOrders();
			$results = [];
			foreach( $open_orders['result']['open'] as $key => $open_order ) {
			
				$open_order['id'] = $key;

				$open_order['market'] = $this->get_market_symbol( $open_order['descr']['pair'] );
				$open_order['timestamp_created'] = $open_order['opentm'];
				$open_order['exchange'] = "Kraken";
				$open_order['avg_execution_price'] = null;
				$open_order['side'] = strtoupper( $open_order['descr']['type'] );
				$open_order['type'] = $open_order['descr']['type'];
				$open_order['is_live'] = true;
				$open_order['is_cancelled'] = false;
				$open_order['is_hidden'] = false;
				$open_order['was_forced'] = false;
				$open_order['original_amount'] = $open_order['vol'];
				$open_order['remaining_amount'] = $open_order['vol'] - $open_order['vol_exec'];
				$open_order['executed_amount'] = $open_order['vol_exec'];
				$open_order['amount'] = $open_order['vol'];

				unset( $open_order['refid'] );
				unset( $open_order['userref'] );
				unset( $open_order['status'] );
				unset( $open_order['opentm'] );
				unset( $open_order['starttm'] );
				unset( $open_order['expiretm'] );
				unset( $open_order['descr'] );
				unset( $open_order['vol'] );
				unset( $open_order['vol_exec'] );
				unset( $open_order['cost'] );
				unset( $open_order['fee'] );
				unset( $open_order['misc'] );
				unset( $open_order['oflags'] );
				unset( $open_order['stopprice'] );
				unset( $open_order['limitprice'] );

				array_push( $results, $open_order );
			}
			return $results;
		}

		public function get_completed_orders( $market="BTC-USD", $limit=100 ) {
			$closed_orders = $this->exch->ClosedOrders();
			$results = [];
			foreach( $closed_orders['result']['closed'] as $key => $closed_order ) {

				$closed_order['market'] = $closed_order['descr']['pair'];
				$closed_order['amount'] = $closed_order['vol'];
				$closed_order['timestamp'] = $closed_order['closetm'];
				$closed_order['exchange'] = "Kraken";
				$closed_order['type'] = $closed_order['descr']['type'];
				$closed_order['fee_currency'] = null;
				$closed_order['fee_amount'] = $closed_order['fee'];
				$closed_order['tid'] = $key;
				$closed_order['order_id'] = $key;
				$closed_order['id'] = $key;
				$closed_order['total'] = $closed_order['vol'];

				unset( $closed_order['refid'] );
				unset( $closed_order['userref'] );
				unset( $closed_order['status'] );
				unset( $closed_order['reason'] );
				unset( $closed_order['opentm'] );
				unset( $closed_order['closetm'] );
				unset( $closed_order['starttm'] );
				unset( $closed_order['expiretm'] );
				unset( $closed_order['descr'] );
				unset( $closed_order['vol'] );
				unset( $closed_order['vol_exec'] );
				unset( $closed_order['cost'] );
				unset( $closed_order['misc'] );
				unset( $closed_order['oflags'] );
				unset( $closed_order['stopprice'] );
				unset( $closed_order['limitprice'] );

				array_push( $results, $closed_order );
			}
			return $results;
		}

		public function deposit_address( $currency = "BTC", $wallet_type = "exchange" ){
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}
		
		public function deposit_addresses(){
			return array( 'ERROR' => 'METHOD_NOT_IMPLEMENTED' );
		}

		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 ) {
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

		public function get_order( $orderid = "1" ) {
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
