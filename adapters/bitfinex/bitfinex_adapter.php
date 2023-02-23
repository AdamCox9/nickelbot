<?PHP

	class BitfinexAdapter extends CryptoBase implements CryptoExchange {

		public function __construct( $Exch ) {
			$this->exch = $Exch;
		}

		//Should be in format of "ETH-BTC" "BASE-QUOTE"
		private function get_market_symbol( $market ) {

			if( strlen( $market ) == 6 )
				return substr_replace($market, '-', 3, 0);
			else
				return str_replace(":", "-", $market);
		
		}

		private function unget_market_symbol( $market ) {
			if( strlen( $market ) == 7 ) {
				$market = str_replace("-", "", $market);
				print_r( $market );
				return $market;
			} else {
				$market = str_replace("-", ":", $market);
				print_r( $market );
				return $market;
			}
		}

		public function get_info() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function withdraw( $account = "exchange", $currency = "BTC", $address = "1fsdaa...dsadf", $amount = 1 ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_currency_summary( $currency = "BTC" ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		public function get_currency_summaries( $currency = "BTC" ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		public function get_order( $orderid = "1" ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		public function cancel( $orderid="1", $opts = array() ) {
			return $this->exch->order_cancel( (int)$orderid );
		}

		public function get_deposits_withdrawals() {
			$currencies = $this->get_currencies();
			$results = [];
			foreach( $currencies as $currency ) {
				$transactions = $this->exch->history_movements( $currency );
				foreach( $transactions as $transaction ) {
					$transaction['exchange'] = "Bitfinex";
					$transaction['fee'] = 0;
					$transaction['address'] = null;
					$transaction['confirmations'] = null;
					array_push( $results, $transaction );
				}
			}
			return $results;
		}

		public function cancel_all() {
			$result = $this->exch->order_cancel_all();
			if( $result['result'] == "All orders cancelled" ) {
				return array( 'success' => true, 'error' => false, 'message' => $result );
			}
			return array( 'success' => false, 'error' => true, 'message' => $result );
		}

		public function buy( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$buy = $this->exch->order_new( $this->unget_market_symbol( $pair ), $amount.'', $price, "bitfinex", "buy", "exchange limit", true );
			if( isset( $sell['message'] ) )
				print_r( $buy );
			return $buy;
		}
		
		public function sell( $pair="BTC-USD", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$sell = $this->exch->order_new( $this->unget_market_symbol( $pair ), $amount.'', $price, "bitfinex", "sell", "exchange limit", true );
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
			$markets = $this->exch->configs( "list", "pair", "exchange" );
			$results = [];
			
			foreach( $markets[0] as $market ) {
				array_push( $results, $this->get_market_symbol( $market ) );
			}
			return $results;
		}

		public function get_currencies() {
			$currencies = $this->exch->configs( "map", "currency", "label" );
			$results = [];

			foreach( $currencies[0] as $currency ) {
				array_push( $results, $currency[0] );
			}
			return( $results );
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
			$balances = $this->exch->balances();
			$response = [];
			foreach( $balances as $balance ) {
				$balance['total'] = $balance['amount'];
				$balance['reserved'] = $balance['total'] - $balance['available'];
				$balance['btc_value'] = 0;
				$balance['pending'] = 0;
				$balance['currency'] = strtoupper( $balance['currency'] );
				unset( $balance['amount'] );
				$response[$balance['currency']] = $balance;
			}

			return $response;
		}

		public function get_balance( $currency = "BTC", $opts = array() ) {
			$balances = $this->get_balances();

			foreach( $balances as $key => $balance )
				if( $key == $currency )
					if( isset( $balances['type'] )&& $balances['type'] == "exchange" )
						return $balance;

			//If balance not set, it must be 0.00000000, build a response array:
			return array( 'type' => 'exchange', 'currency' => $currency, 'available' => 0.00000000, 'total' => 0.00000000, 'reserved' => 0.00000000, 'btc_value' => 0.00000000, 'pending' => 0.00000000 );
		}

		public function get_market_summary( $market = "ETH-BTC" ) {
			$market_summary = $this->exch->ticker( $this->unget_market_symbol( $market ) );
			$market_summary['market'] = $market;
			$market_summary = $this->standardize_market_summary( $market_summary );

			return $market_summary;
		}

		/*
			TODO add BID_SIZE and ASK_SIZE to standardized market summary for all exchanges:
		
			[0]	BID			float	Price of last highest bid
			[1]	BID_SIZE		float	Sum of the 25 highest bid sizes
			[2]	ASK			float	Price of last lowest ask
			[3]	ASK_SIZE		float	Sum of the 25 lowest ask sizes
			[4]	DAILY_CHANGE		float	Amount that the last price has changed since yesterday
			[5]	DAILY_CHANGE_RELATIVE	float	Relative price change since yesterday (*100 for percentage change)
			[6]	LAST_PRICE		float	Price of the last trade
			[7]	VOLUME			float	Daily volume
			[8]	HIGH			float	Daily high
			[9]	LOW			float	Daily low

			........translated to.........
			
			'ask', 'base_volume', 'bid', 'btc_volume', 'created', 'display_name', 'exchange', 
			'expiration', 'frozen', 'high', 'initial_margin', 'last_price', 
			'low', 'market', 'market_id', 'maximum_order_size', 'mid', 'minimum_margin', 
			'minimum_order_size_base', 'minimum_order_size_quote', 'open_buy_orders',
			'open_sell_orders', 'percent_change', 'price_precision', 
			'quote_volume', 'result', 'timestamp', 'verified_only', 'vwap' 
		*/
		private function standardize_market_summary( $market_summary ) {		
			$market_summary['exchange'] = "bittrex";
			$market_summary['high'] = isset( $market_summary[8] ) ? $market_summary[8] : null;
			$market_summary['low'] = isset( $market_summary[9] ) ? $market_summary[9] : null;
			$market_summary['base_volume'] = null;
			$market_summary['quote_volume'] = isset( $market_summary[7] ) ? $market_summary[7] : null;
			$market_summary['btc_volume'] = null;
			$market_summary['last_price'] = isset( $market_summary[6] ) ? $market_summary[6] : null;
			$market_summary['timestamp'] = null;
			$market_summary['bid'] = isset( $market_summary[0] ) ? $market_summary[0] : null;
			$market_summary['ask'] = isset( $market_summary[2] ) ? $market_summary[2] : null;
			$market_summary['display_name'] = $market_summary['market'];
			$market_summary['result'] = true;
			$market_summary['created'] = null;
			$market_summary['open_buy_orders'] = null;
			$market_summary['open_sell_orders'] = null;
			$market_summary['percent_change'] = isset( $market_summary[4] ) ? $market_summary[4] : null;
			$market_summary['frozen'] = null;
			$market_summary['verified_only'] = null;
			$market_summary['expiration'] = null;
			$market_summary['initial_margin'] = null;
			$market_summary['maximum_order_size'] = null;
			$market_summary['mid'] = null;
			$market_summary['minimum_margin'] = null;
			$market_summary['minimum_order_size_quote'] = null;
			$market_summary['minimum_order_size_base'] = null;
			$market_summary['price_precision'] = 8;
			$market_summary['vwap'] = null;
			$market_summary['market_id'] = null;

			unset( $market_summary['0'] );
			unset( $market_summary['1'] );
			unset( $market_summary['2'] );
			unset( $market_summary['3'] );
			unset( $market_summary['4'] );
			unset( $market_summary['5'] );
			unset( $market_summary['6'] );
			unset( $market_summary['7'] );
			unset( $market_summary['8'] );
			unset( $market_summary['9'] );

			return $market_summary;
		}


		public function get_market_summaries() {
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

		public function get_trades( $market = "BTC-USD", $opts = array( 'limit' => 10 ) ) {
			$trades = $this->exch->trades( $this->unget_market_symbol( $market ), $opts['limit'] );
			$results = [];
			foreach( $trades as $trade ) {
				$trade['market'] = $market;
				array_push( $results, $trade );
			}
			return $results;
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 20 ) {
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

		//Return trollbox data from the exchange, otherwise get forum posts or twitter feed if must...
		public function get_trollbox() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		//Margin trading
		public function margin_history() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		public function margin_info() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		//lending:
		public function loan_offer() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		public function cancel_loan_offer() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		public function loan_offer_status() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function active_loan_offers() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		//borrowing:

		public function get_positions() {
			$positions = $this->exch->positions();
			$results = [];
			foreach( $positions as $position ) {
				array_push( $results, $position );
			}
			return $results;
		}

		public function claim_position() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function close_position() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function active_loan() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function inactive_loan() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

	}
?>
