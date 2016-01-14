<?PHP

	class BittrexAdapter implements CryptoExchange {

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

		public function cancel($orderid="1", $opts = array() ) {
			return $this->exch->market_cancel( array("uuid" => $orderid ) );
		}
		
		public function cancel_all() {
			$result = $this->get_open_orders();
			$response = array();
			if( isset( $result['success'] ) )
				foreach( $result['result'] as $order )
					array_push($response,$this->cancel($order['OrderUuid']));

			if( $result['success'] )
				return array( 'success' => true, 'error' => false, 'message' => $response );
			else
				return array( 'success' => false, 'error' => true, 'message' => $result );
		}

		public function buy( $pair="LTC-BTC", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = explode( "-", $pair );
			$pair = $pair[1] . "-" . $pair[0];
			return $this->exch->market_buylimit( array( 'market' => strtoupper($pair), 'quantity' => $amount, 'rate' => $price ) );
		}
		
		public function sell( $pair="LTC-BTC", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$pair = explode( "-", $pair );
			$pair = $pair[1] . "-" . $pair[0];
			return $this->exch->market_selllimit( array( 'market' => strtoupper($pair), 'quantity' => $amount, 'rate' => $price ) );
		}

		public function get_open_orders() {
			if( isset( $this->open_orders ) )
				return $this->open_orders;
			$open_orders = $this->exch->market_getopenorders();
			$this->open_orders = [];
			foreach( $open_orders['result'] as $open_order ) {
				$open_order['id'] = $open_order['OrderUuid'];
				$open_order['market'] = $open_order['Exchange'];
				$open_order['exchange'] = "bittrex";
				$open_order['price'] = $open_order['Limit'];
				$open_order['timestamp'] = $open_order['Opened'];
				$open_order['avg_execution_price'] = $open_order['Price'];
				$open_order['side'] = $open_order['OrderType'];
				$open_order['type'] = $open_order['OrderType'];
				$open_order['is_live'] = true;
				$open_order['is_cancelled'] = false;
				$open_order['is_hidden'] = false;
				$open_order['was_forced'] = false;
				$open_order['original_amount'] = null;
				$open_order['remaining_amount'] = null;
				$open_order['executed_amount'] = null;
				$open_order['amount'] = null;

				unset( $open_order['Uuid'] );
				unset( $open_order['OrderUuid'] );
				unset( $open_order['Exchange'] );
				unset( $open_order['OrderType'] );
				unset( $open_order['Quantity'] );
				unset( $open_order['QuantityRemaining'] );
				unset( $open_order['Limit'] );
				unset( $open_order['CommissionPaid'] );
				unset( $open_order['Price'] );
				unset( $open_order['PricePerUnit'] );
				unset( $open_order['Opened'] );
				unset( $open_order['Closed'] );
				unset( $open_order['CancelInitiated'] );
				unset( $open_order['ImmediateOrCancel'] );
				unset( $open_order['IsConditional'] );
				unset( $open_order['Condition'] );
				unset( $open_order['ConditionTarget'] );

				array_push( $this->open_orders, $open_order );
			}
			return $this->open_orders;
		}

/*Array
(
    [Uuid] =>
    [OrderUuid] => 15507d39-5d77-43fd-bf46-8b30901ef448
    [Exchange] => BTC-JBS
    [OrderType] => LIMIT_BUY
    [Quantity] => 21963.79333375
    [QuantityRemaining] => 21963.79333375
    [Limit] => 8.003E-5
    [CommissionPaid] => 0
    [Price] => 0
    [PricePerUnit] =>
    [Opened] => 2015-09-14T08:19:13.263
    [Closed] =>
    [CancelInitiated] =>
    [ImmediateOrCancel] =>
    [IsConditional] =>
    [Condition] => NONE
    [ConditionTarget] =>
)*/

		public function get_completed_orders() {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$this->completed_orders = [];
			foreach( $this->get_markets() as $market ) {
				$completed_orders = $this->exch->account_getorderhistory( array( 'market' => $market, 'count' => 100 ) );
				foreach( $completed_orders['result'] as $completed_order ) {
					$completed_order['exchange'] = "bittrex";
					$completed_order['market'] = $market;
					array_push( $this->completed_orders, $completed_order );
				}
			}
			return $this->completed_orders;
		}

		public function get_markets() {
			$markets = $this->exch->getmarketsummaries();
			$response = [];
			foreach( $markets['result'] as $market ) {
				array_push( $response, $market['MarketName'] );
			}
			return array_map( 'strtoupper', $response );
		}

		public function get_currencies() {
			$currencies = $this->exch->getcurrencies();
			$response = [];
			foreach( $currencies['result'] as $currency ) {	
				array_push( $response, $currency['Currency'] );
			}
			return array_map( 'strtoupper', $response );
		}

		public function deposit_address( $currency = "BTC" ){
			if( ! isset( $this->cnt ) )
				$this->cnt = 0;
			if( $this->cnt > 5 )
				return false;
			$address = $this->exch->account_getdepositaddress( array( 'currency' => $currency ) );
			if( $address['message'] == 'CURRENCY_OFFLINE' )
				return FALSE;
			if( $address['success'] == 1 ) {
				if( $address['result']['Address'] == "" ) {
					sleep( 5 );
					$this->cnt++;
					return $this->deposit_address( $currency );
				}
				return $address['result'];
			}
			if( $address['message'] == 'ADDRESS_GENERATING' ) {
				sleep( 5 );
				$this->cnt++;
				return $this->deposit_address( $currency );
			}
			return false;
		}
		
		public function deposit_addresses(){
			$currencies = $this->get_currencies();
			$addresses = [];
			foreach( $currencies as $currency ) {
				$address = $this->deposit_address( $currency );
				if( $address ) {
					$address['wallet_type'] = "exchange";
					$address['currency'] = $address['Currency'];
					$address['address'] = $address['Address'];
					$address['method'] = null;

					unset( $address['Currency'] );
					unset( $address['Address'] );

					array_push( $addresses, $address );
				}
			}
			return $addresses;
		}

		public function get_balances() {
			$balances = $this->exch->account_getbalances();
			if( $balances['success'] == 1 )
				$balances = $balances['result'];
			else
				return [];

			$results = [];
			foreach( $balances as $balance ) {
				$balance['type'] = "exchange";
				$balance['currency'] = strtoupper( $balance['Currency'] );
				$balance['total'] = $balance['Balance'];
				$balance['available'] = $balance['Available'];
				$balance['pending'] = $balance['Pending'];
				$balance['reserved'] = $balance['total'] - $balance['available'];
				$balance['btc_value'] = 0;

				unset( $balance['Currency'] );
				unset( $balance['Balance'] );
				unset( $balance['Available'] );
				unset( $balance['Pending'] );
				unset( $balance['CryptoAddress'] );

				array_push( $results, $balance );
			}
			return $results;
		}

		public function get_balance( $currency="BTC" ) {
			return [];
		}

		public function get_market_summary( $market="LTC-BTC" ) {
			return $this->exch->getmarketsummary( array('market' => $market ) );
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;
			
			$market_summaries = $this->exch->getmarketsummaries();
			$market_summaries = $market_summaries['result'];
			$this->market_summaries = [];
			foreach( $market_summaries as $market_summary ) {
				$market_summary['exchange'] = "bittrex";
				$msmn = explode( "-", $market_summary['MarketName'] );
				$market_summary['market'] = $msmn[1] . "-" . $msmn[0];
				$market_summary['high'] = $market_summary['High'];
				$market_summary['low'] = $market_summary['Low'];
				$market_summary['base_volume'] = $market_summary['Volume'];
				$market_summary['quote_volume'] = $market_summary['BaseVolume'];
				$market_summary['btc_volume'] = null;
				$market_summary['last_price'] = $market_summary['Last'];
				$market_summary['timestamp'] = $market_summary['TimeStamp'];
				$market_summary['bid'] = is_null( $market_summary['Bid'] ) ? 0 : $market_summary['Bid'];
				$market_summary['ask'] = is_null( $market_summary['Ask'] ) ? 0 : $market_summary['Ask'];
				$market_summary['display_name'] = $market_summary['market'];
				$market_summary['result'] = true;
				$market_summary['created'] = $market_summary['Created'];
				$market_summary['open_buy_orders'] = $market_summary['OpenBuyOrders'];
				$market_summary['open_sell_orders'] = $market_summary['OpenSellOrders'];
				$market_summary['percent_change'] = null;
				$market_summary['frozen'] = null;
				$market_summary['verified_only'] = null;
				$market_summary['expiration'] = null;
				$market_summary['initial_margin'] = null;
				$market_summary['maximum_order_size'] = null;
				$market_summary['mid'] = ( $market_summary['bid'] + $market_summary['ask'] ) / 2;
				$market_summary['minimum_margin'] = null;
				$market_summary['minimum_order_size_quote'] = 0.00050000;
				$market_summary['minimum_order_size_base'] = null;
				$market_summary['price_precision'] = 8;
				$market_summary['vwap'] = null;
				$market_summary['market_id'] = null;

				unset( $market_summary['OpenBuyOrders'] );
				unset( $market_summary['OpenSellOrders'] );
				unset( $market_summary['MarketName'] );
				unset( $market_summary['High'] );
				unset( $market_summary['Low'] );
				unset( $market_summary['Volume'] );
				unset( $market_summary['Last'] );
				unset( $market_summary['BaseVolume'] );
				unset( $market_summary['TimeStamp'] );
				unset( $market_summary['Bid'] );
				unset( $market_summary['Ask'] );
				unset( $market_summary['Created'] );
				unset( $market_summary['PrevDay'] );

				ksort( $market_summary );

				array_push( $this->market_summaries, $market_summary );
			}

			return $this->market_summaries;
		}

		//TODO convert the $time to $count
		public function get_all_trades( $time = 0 ) {
			$results = [];
			foreach( $this->get_markets() as $market ) {
				array_push( $results, $this->get_trades( $market, $time ) );
			}
			return $results;
		}

		public function get_trades( $market = 'BTC-USD', $time = 0 ) {
			return $this->exch->getmarkethistory( array( 'market' => $market, 'count' => 20 ) );
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 0 ) {
			$result = $this->exch->getorderbook( array( 'market' => $market, 'type' => "both", 'depth' => $depth ) );
			return $result;
		}

		public function get_orderbooks( $depth = 0 ) {
			$result = $this->exch->getorderbook( array( 'market' => $market, 'type' => "both", 'depth' => $depth ) );
			return $result;
		}

	}

?>