<?PHP

	class BittrexAdapter extends CryptoBase implements CryptoExchange {

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
			return $this->exch->market_cancel( array("id" => $orderid ) );
		}

		public function get_deposits_withdrawals() {
			$results = [];

			//_____Withdrawals:
			$transactions = $this->exch->account_getwithdrawalhistory( array() );

			foreach( $transactions['result'] as $transaction ) {
				$transaction['exchange'] = "Bittrex";
				$transaction['type'] = 'WITHDRAWAL';
				array_push( $results, $transaction );
			}

			//_____Deposits:
			$transactions = $this->exch->account_getdeposithistory( array() );
			foreach( $transactions['result'] as $transaction ) {
				$transaction['exchange'] = "Bittrex";
				$transaction['type'] = 'DEPOSIT';
				array_push( $results, $transaction );
			}

			$return = [];
			foreach( $results as $result ) {

				if( isset( $result['PaymentUuid'] ) ) {
					$result['id'] = $result['PaymentUuid'];
				} else if ( isset( $result['Id'] ) ) {
					$result['id'] = $result['Id'];
				} else {
					$result['id'] = null;
				}

				$result['currency'] = $result['Currency'];
				$result['method'] = $result['Currency'];
				$result['amount'] = $result['Amount'];
				$result['description'] = $result['Currency'];
				$result['status'] = isset( $result['PendingPayment'] ) ? $result['PendingPayment'] : null;
				$result['fee'] = isset( $result['TxCost'] ) ? $result['TxCost'] : null;
				$result['address'] = isset( $result['CryptoAddress'] ) ? $result['CryptoAddress'] : null;
				$result['fee'] = isset( $result['TxCost'] ) ? $result['TxCost'] : null;

				if( isset( $result['LastUpdated'] ) ) {
					$result['timestamp'] = $result['LastUpdated'];
				} else if ( isset( $result['Opened'] ) ) {
					$result['timestamp'] = $result['Opened'];
				} else {
					$result['timestamp'] = null;
				}

				$result['confirmations'] = isset( $result['Confirmations'] ) ? $result['Confirmations'] : null;

				unset( $result['PaymentUuid'] );
				unset( $result['Currency'] );
				unset( $result['Amount'] );
				unset( $result['Address'] );
				unset( $result['Opened'] );
				unset( $result['Authorized'] );
				unset( $result['PendingPayment'] );
				unset( $result['TxCost'] );
				unset( $result['TxId'] );
				unset( $result['Canceled'] );
				unset( $result['InvalidAddress'] );
				unset( $result['Id'] );
				unset( $result['Confirmations'] );
				unset( $result['LastUpdated'] );
				unset( $result['CryptoAddress'] );

				array_push( $return, $result );
			}

			return $return;
		}

		public function get_deposits() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_deposit( $deposit_id="1", $opts = array() ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_withdrawals() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function cancel_all() {
			$result = $this->get_open_orders();
			$response = array();

			foreach( $result as $order ) {
				array_push($response,$this->cancel($order['id']));
			}

			if( isset( $result['success'] ) )
				return array( 'success' => true, 'error' => false, 'message' => $response );
			else
				return array( 'success' => false, 'error' => true, 'message' => $result );
		}

		public function buy( $pair="LTC-BTC", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$buy = $this->exch->post_buy( array( 'market' => $pair, 'quantity' => $amount, 'rate' => $price ) );
			return $buy;
		}
		
		public function sell( $pair="LTC-BTC", $amount=0, $price=10000, $type="LIMIT", $opts=array() ) {
			$sell = $this->exch->post_sell( array( 'market' => $pair, 'quantity' => $amount, 'rate' => $price ) );
			return $sell;
		}

/*
/orders/open looks like:
    [1] => Array
        (
            [id] => 3443f225-67b6-4646-b393-4e10bf8ee612
            [marketSymbol] => PIVX-BTC
            [direction] => SELL
            [type] => LIMIT
            [quantity] => 100.00000000
            [limit] => 0.000020000000
            [timeInForce] => GOOD_TIL_CANCELLED
            [fillQuantity] => 0.00000000
            [commission] => 0.00000000
            [proceeds] => 0.00000000
            [status] => OPEN
            [createdAt] => 2023-02-18T18:54:00.71Z
            [updatedAt] => 2023-02-18T18:54:00.71Z
        )
*/
		public function get_open_orders() {
			if( isset( $this->open_orders ) )
				return $this->open_orders;
			$open_orders = $this->exch->get_openorders();
			$this->open_orders = [];
			
			foreach( $open_orders as $open_order ) {
				$open_order['market'] = $open_order['marketSymbol'];
				$open_order['exchange'] = "bittrex";
				$open_order['price'] = $open_order['limit'];
				$open_order['timestamp_created'] = $open_order['createdAt'];
				$open_order['side'] = $open_order['direction'];
				$open_order['is_live'] = true;
				$open_order['is_cancelled'] = false;
				$open_order['is_hidden'] = false;
				$open_order['was_forced'] = false;
				$open_order['original_amount'] = null;
				$open_order['remaining_amount'] = null;
				$open_order['executed_amount'] = null;
				$open_order['amount'] = $open_order['quantity'];

				array_push( $this->open_orders, $open_order );
			}
			return $this->open_orders;
		}

		public function get_completed_orders( $market="BTC-USD", $limit = 100 ) {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$this->completed_orders = [];
			foreach( $this->get_markets() as $market ) {
				$completed_orders = $this->exch->account_getorderhistory( array( 'market' => $market, 'count' => $limit ) );
				foreach( $completed_orders['result'] as $completed_order ) {
					$completed_order['exchange'] = "bittrex";
					$completed_order['market'] = $market;
					$completed_order['price'] = null;
					$completed_order['amount'] = null;
					$completed_order['timestamp'] = null;
					$completed_order['type'] = null;
					$completed_order['fee_currency'] = null;
					$completed_order['fee_amount'] = null;
					$completed_order['tid'] = null;
					$completed_order['order_id'] = null;
					$completed_order['id'] = null;
					$completed_order['fee'] = null;
					$completed_order['total'] = null;

					unset( $completed_order['OrderUuid'] );
					unset( $completed_order['Exchange'] );
					unset( $completed_order['TimeStamp'] );
					unset( $completed_order['OrderType'] );
					unset( $completed_order['Limit'] );
					unset( $completed_order['Quantity'] );
					unset( $completed_order['QuantityRemaining'] );
					unset( $completed_order['Commission'] );
					unset( $completed_order['Price'] );
					unset( $completed_order['PricePerUnit'] );
					unset( $completed_order['IsConditional'] );
					unset( $completed_order['Condition'] );
					unset( $completed_order['ConditionTarget'] );
					unset( $completed_order['ImmediateOrCancel'] );
					unset( $completed_order['Closed'] );

					array_push( $this->completed_orders, $completed_order );
				}
			}
			return $this->completed_orders;
		}

		/*	
			//markets returns:
			Array
			(
			    [symbol] => ZUSD-USDT
			    [baseCurrencySymbol] => ZUSD
			    [quoteCurrencySymbol] => USDT
			    [minTradeSize] => 3.07913370
			    [precision] => 4
			    [status] => ONLINE
			    [createdAt] => 2021-04-27T14:03:18.55Z
			    [prohibitedIn] => Array
				(
				    [0] => US
				)

			    [associatedTermsOfService] => Array
				(
				)

			    [tags] => Array
				(
				)
			)        
		*/
		public function get_markets() {
			$markets = $this->exch->get_markets();
			
			$response = [];
			foreach( $markets as $market ) {
				array_push( $response, $market['symbol'] );
			}
			return $response;
		}

		public function get_currencies() {
			$currencies = $this->exch->get_currencies();
			$response = [];
			foreach( $currencies as $currency ) {	
				array_push( $response, $currency['symbol'] );
			}
			return $response;
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
		
		
		/*
Array
(
    [status] => PROVISIONED
    [currencySymbol] => BITS
    [cryptoAddress] => ARDOR-S42Z-ERET-QLMX-4JR77
    [cryptoAddressTag] => fc0eec6736094c10876f87c1c5ce0713b6d3be46519d454d9003e006e69235d1
)
*/		
		
		public function deposit_addresses(){
			$addresses = $this->exch->get_addresses();
			$response = [];
			foreach ( $addresses as $address ) {
				$address['currency'] = $address['currencySymbol'];
				$address['address'] = $address['cryptoAddress'];
				$address['wallet_type'] = "exchange";
				$address['cryptoAddressTag'] = isset( $address['cryptoAddressTag'] ) ? $address['cryptoAddressTag'] : null;
				unset( $address['currencySymbol'] );
				unset( $address['cryptoAddress'] );
				array_push( $response, $address );
			}
			return $response;
		}

		public function get_balances() {
			$balances = $this->exch->get_balances();
			if( $balances['success'] == 1 )
				$balances = $balances['result'];
			else
				return [];

			$response = [];
			foreach( $balances as $balance ) {
				$balance['type'] = "exchange";
				$balance['currency'] = $balance['Currency'];
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

				$response[$balance['currency']] = $balance;
			}

			$this->balances = $response;
			return $this->balances;
		}

		public function get_balance( $currency="BTC" ) {
			$balance = $this->exch->get_balance( array('currency' => $currency ) );
			return $balance;

		}

		public function get_market_summary( $market="LTC-BTC" ) {
			$market_summary = $this->exch->get_markets_summary( array('market' => $market ) );
			$ticker = $this->exch->get_ticker( array('market' => $market ) );
			
			return $this->standardize_market_summary( array_merge( $market_summary, $ticker ) );
		}

		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;
			
			//$market_summaries = $this->exch->get_markets_summaries();
			$market_summaries = $this->exch->get_markets();
			
			$this->market_summaries = [];
			foreach( $market_summaries as $market_summary ) {
				array_push( $this->market_summaries, $this->standardize_market_summary( $market_summary ) );
			}
			return $this->market_summaries;
		}


		private function standardize_market_summary( $market_summary ) {
		
/*


Would need to query Specific Market, Ticker, etc... for each market for this data:

/markets is an array of all markets like:
Array
(
    [symbol] => 1ECO-BTC
    [high] => 0.000026220000
    [low] => 0.000022220000
    [volume] => 256.66200000
    [quoteVolume] => 0.00648639
    [percentChange] => -0.08
    [updatedAt] => 2023-02-16T23:20:47.96Z

/markets/summary looks like:
Array
(
    [symbol] => PIVX-BTC
    [baseCurrencySymbol] => PIVX
    [quoteCurrencySymbol] => BTC
    [minTradeSize] => 9.12762204
    [precision] => 8
    [status] => ONLINE
    [createdAt] => 2016-03-02T20:45:37.987Z
    [prohibitedIn] => Array
        (
        )

    [associatedTermsOfService] => Array
        (
        )

    [tags] => Array
        (
        )
)

/markets/{market}/ticker
Array
(
    [symbol] => PIVX-BTC
    [lastTradeRate] => 0.000016350000
    [bidRate] => 0.000016820000
    [askRate] => 0.000016990000
    [updatedAt] => 2023-02-18T00:55:17.888038Z
)


)*/		

//print_r( $market_summary );

			$market_summary['exchange'] = "bittrex";
			$market_summary['market'] = $market_summary['symbol'];
			$market_summary['high'] = isset( $market_summary['high'] ) ? $market_summary['high'] : null;
			$market_summary['low'] = isset( $market_summary['low'] ) ? $market_summary['low'] : null;
			$market_summary['base_volume'] = isset( $market_summary['volume'] ) ? $market_summary['volume'] : null;
			$market_summary['quote_volume'] = isset( $market_summary['quoteVolume'] ) ? $market_summary['quoteVolume'] : null;
			$market_summary['btc_volume'] = null;
			$market_summary['last_price'] = null;
			$market_summary['timestamp'] = null;
			$market_summary['bid'] = isset( $market_summary['bidRate'] ) ? $market_summary['bidRate'] : null;
			$market_summary['ask'] = isset( $market_summary['askRate'] ) ? $market_summary['askRate'] : null;
			$market_summary['display_name'] = $market_summary['market'];
			$market_summary['result'] = true;
			$market_summary['created'] = null;
			$market_summary['open_buy_orders'] = null;
			$market_summary['open_sell_orders'] = null;
			$market_summary['percent_change'] = null;
			$market_summary['frozen'] = null;
			$market_summary['verified_only'] = null;
			$market_summary['expiration'] = null;
			$market_summary['initial_margin'] = null;
			$market_summary['maximum_order_size'] = null;
			$market_summary['mid'] = null;
			$market_summary['minimum_margin'] = null;
			$market_summary['minimum_order_size_quote'] = null;
			$market_summary['minimum_order_size_base'] = isset( $market_summary['minTradeSize'] ) ? $market_summary['minTradeSize'] : null;
			$market_summary['price_precision'] = 8;
			$market_summary['vwap'] = null;
			$market_summary['market_id'] = null;

			unset( $market_summary['bidRate'] );
			unset( $market_summary['askRate'] );

			return $market_summary;
		}

		public function get_trades( $market = 'BTC-USD', $opts = array( 'limit' => 10 ) ) {
			$trades = $this->exch->getmarkethistory( array( 'market' => $market, 'count' => $opts['limit'] ) );

			$results = [];
			foreach( $trades['result'] as $trade ) {
				array_push( $results, $trade );
			}

			return $results;
		}

		public function get_orderbook( $market = "BTC-USD", $depth = 10 ) {
			$orderbooks = $this->exch->getorderbook( array( 'market' => $market, 'type' => "both", 'depth' => $depth ) );
			$orderbooks = $orderbooks['result'];
			$n_orderbooks = [];
			$o_orderbooks = [];

			if( isset( $orderbooks['buy'] ) )
				foreach( $orderbooks['buy'] as $orderbook ) {
					array_push( $n_orderbooks, $orderbook );
				}

			if( isset( $orderbooks['sell'] ) )
				foreach( $orderbooks['sell'] as $orderbook ) {
					array_push( $n_orderbooks, $orderbook );
				}

			foreach( $n_orderbooks as $orderbook ) {
				$orderbook['market'] = $market;
				$orderbook['price'] = $orderbook['Rate'];
				$orderbook['amount'] = $orderbook['Quantity'];
				$orderbook['timestamp'] = null;
				$orderbook['exchange'] = null;
				$orderbook['type'] = null;

				unset( $orderbook['Quantity'] );
				unset( $orderbook['Rate'] );
				array_push( $o_orderbooks, $orderbook );
			}

			return $o_orderbooks;
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
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
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
