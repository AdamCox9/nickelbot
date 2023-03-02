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

		//TESTED v3: works
		public function cancel($orderid="1", $opts = array() ) {
			return $this->exch->order_cancel( array("id" => $orderid ) );
		}
 
		//TESTED v3: works
		public function get_deposits_withdrawals() {
			$results = [];

			//_____Withdrawals:
			$transactions = $this->exch->get_withdrawals( );

			foreach( $transactions as $transaction ) {
				$transaction['exchange'] = "Bittrex";
				$transaction['type'] = 'WITHDRAWAL';
				array_push( $results, $transaction );
			}

			//_____Deposits:
			$transactions = $this->exch->get_deposits( );
			foreach( $transactions as $transaction ) {
				$transaction['exchange'] = "Bittrex";
				$transaction['type'] = 'DEPOSIT';
				array_push( $results, $transaction );
			}

			$return = [];
			foreach( $results as $result ) {
				$result['id'] = isset( $result['txId'] ) ? $result['txId'] : null;
				$result['currency'] = $result['currencySymbol'];
				$result['method'] = null;
				$result['amount'] = $result['quantity'];
				$result['description'] = null;
				$result['fee'] = isset( $result['txCost'] ) ? $result['txCost'] : null;
				$result['address'] = isset( $result['cryptoAddress'] ) ? $result['cryptoAddress'] : null;
				$result['timestamp'] = isset( $result['createdAt'] ) ? $result['createdAt'] : null;
				$result['confirmations'] = null;

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
				unset( $result['currencySymbol'] );
				unset( $result['quantity'] );
				unset( $result['cryptoAddress'] );
				unset( $result['txCost'] );
				unset( $result['txId'] );
				unset( $result['createdAt'] );
				unset( $result['completedAt'] );
				unset( $result['target'] );
				unset( $result['cryptoAddressTag'] );
				unset( $result['updatedAt'] );
				unset( $result['source'] );
				unset( $result['fundsTransferMethodId'] );

				array_push( $return, $result );
			}

			return $return;
		}

		public function get_deposits() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_deposit( $deposit_id = "1", $opts = [] ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_withdrawals() {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		public function get_withdrawal( $withdrawal_id = "1", $opts = [] ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}

		//NEEDS TO BE UPDATED to API v3
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

		//TESTED v3: works
		public function buy( $pair="LTC-BTC", $amount=0, $price=0, $type="LIMIT", $opts=array() ) {
			$buy = $this->exch->post_buy( array( 'market' => $pair, 'quantity' => $amount, 'rate' => $price ) );
			return $buy;
		}
		
		//TESTED v3: works
		public function sell( $pair="LTC-BTC", $amount=0, $price=10000, $type="LIMIT", $opts=array() ) {
			$sell = $this->exch->post_sell( array( 'market' => $pair, 'quantity' => $amount, 'rate' => $price ) );
			return $sell;
		}

		public function update_order( $order_id=0, $amount=0, $price=0, $opts=array() ) {
			return array( 'ERROR' => 'METHOD_NOT_AVAILABLE' );
		}
		
		//TESTED v3: works
		public function get_open_orders() {
			$m_open_orders = $this->exch->get_openorders();

			if( isset( $m_open_orders['ERROR'] ) )
				return array( 'MESSAGE' => $m_open_orders );

			$open_orders = [];
			
			foreach( $m_open_orders as $open_order ) {
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
				$open_order['avg_execution_price'] = null;
				$open_order['amount'] = $open_order['quantity'];

				unset( $open_order['marketSymbol'] );
				unset( $open_order['direction'] );
				unset( $open_order['quantity'] );
				unset( $open_order['limit'] );
				unset( $open_order['timeInForce'] );
				unset( $open_order['fillQuantity'] );
				unset( $open_order['commissio'] );
				unset( $open_order['proceeds'] );
				unset( $open_order['status'] );
				unset( $open_order['createdAt'] );
				unset( $open_order['updatedAt'] );
				unset( $open_order['commission'] );

				array_push( $open_orders, $open_order );
			}
			return $open_orders;
		}

		//TESTED v3: works
		public function get_completed_orders( $market="BTC-USD", $limit = 100 ) {
			if( isset( $this->completed_orders ) )
				return $this->completed_orders;
			$completed_orders = $this->exch->get_closedorders( $market, $limit );
			$this->completed_orders = [];

			foreach( $completed_orders as $completed_order ) {
				$completed_order['exchange'] = "bittrex";
				$completed_order['market'] = $market;
				$completed_order['price'] = $completed_order['limit'];
				$completed_order['amount'] = $completed_order['quantity'];
				$completed_order['timestamp'] = $completed_order['closedAt'];
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
				unset( $completed_order['marketSymbol'] );
				unset( $completed_order['direction'] );
				unset( $completed_order['quantity'] );
				unset( $completed_order['limit'] );
				unset( $completed_order['timeInForce'] );
				unset( $completed_order['fillQuantity'] );
				unset( $completed_order['commission'] );
				unset( $completed_order['proceeds'] );
				unset( $completed_order['status'] );
				unset( $completed_order['createdAt'] );
				unset( $completed_order['updatedAt'] );
				unset( $completed_order['closedAt'] );

				array_push( $this->completed_orders, $completed_order );
			}
			return $this->completed_orders;
		}

		//TESTED v3: works
		public function get_markets() {
			$markets = $this->exch->get_markets();
			
			$response = [];
			foreach( $markets as $market ) {
				array_push( $response, $market['symbol'] );
			}
			return $response;
		}

		//TESTED v3: works
		public function get_currencies() {
			$currencies = $this->exch->get_currencies();
			$response = [];
			foreach( $currencies as $currency ) {	
				array_push( $response, $currency['symbol'] );
			}
			return $response;
		}

		//NEED TO UPDATE to API v3
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
				
		//TESTED v3: works
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

		//TESTED v3: works
		public function get_balances() {
			$balances = $this->exch->get_balances();

			$response = [];
			foreach( $balances as $balance ) {
				$balance['type'] = "exchange";
				$balance['currency'] = $balance['currencySymbol'];
				$balance['pending'] = 0;
				$balance['reserved'] = $balance['total'] - $balance['available'];
				$balance['btc_value'] = null;

				unset( $balance['currencySymbol'] );
				unset( $balance['updatedAt'] );

				$response[$balance['currency']] = $balance;
			}

			$this->balances = $response;
			return $this->balances;
		}

		//TESTED v3: works
		public function get_balance( $currency="BTC" ) {
			$balance = $this->exch->get_balance( array('currency' => $currency ) );
			
			$balance['type'] = "exchange";
			$balance['currency'] = $balance['currencySymbol'];
			$balance['pending'] = $balance['total'] - $balance['available'];
			$balance['reserved'] = $balance['total'] - $balance['available'];
			$balance['btc_value'] = null;

			unset( $balance['currencySymbol'] );
			unset( $balance['updatedAt'] );

			$response[$balance['currency']] = $balance;
			
			return $balance;

		}

		//Works for now: can get more data from get_markets???
		public function get_market_summary( $market="LTC-BTC" ) {
			$market_summary = $this->exch->get_markets_summary( array('market' => $market ) );			
			$m_market = $this->exch->get_market( array('market' => $market )  );
			$ticker = $this->exch->get_ticker( array('market' => $market ) );

			$market_summary = array_merge( $market_summary, $m_market );
			$market_summary = array_merge( $market_summary, $ticker );
			
			return $this->standardize_market_summary( $market_summary );
		}

		//Works
		public function get_market_summaries() {
			if( isset( $this->market_summaries ) ) //cache
				return $this->market_summaries;
			else
				$this->market_summaries = array();

			$markets_summaries = $this->exch->get_markets_summaries();
			$markets = $this->exch->get_markets();
			$tickers = $this->exch->get_tickers();
			$market_summaries = [];
			$response = [];

			//Will merge the three arrays by 'symbol':

			//Merge markets and markets_summaries into market_summaries (notice the s).
			foreach( $markets_summaries as $markets_summary ) {
				foreach( $markets as $market ) {
					if( $markets_summary['symbol'] == $market['symbol'] ) {
						array_push( $market_summaries, array_merge( $markets_summary, $market ) );
					}
				}
			}

			//Merge market_summaries from above with $tickers (notice the s).
			foreach( $tickers as $ticker ) {
				foreach( $market_summaries as $market_summary ) {
					if( $market_summary['symbol'] == $ticker['symbol'] ) {
						array_push( $response, array_merge( $market_summary, $ticker ) ); //Standardize the format
					}
				}
			}

			//TODO take list of countries for prohibitedIn as parameter. It defaults to US for now.
			foreach( $response as $key => $market_summary ) {
				if( count( $market_summary['prohibitedIn'] ) > 0 ) {
					foreach( $market_summary['prohibitedIn'] as $prohibitedIn ) {
						if( $prohibitedIn == "US" ) {
							unset( $response[$key] );
						}
					}
				}
			}

			//Standardize the format
			foreach( $response as $key => $value ) {
				$response[$key] = $this->standardize_market_summary( $value );
			}

			$this->market_summaries = $response;
			return $this->market_summaries;
		}

		//Still missing some data but good enough for now:
		private function standardize_market_summary( $market_summary ) {		
			$market_summary['exchange'] = "bittrex";
			$market_summary['market'] = $market_summary['symbol'];
			$market_summary['high'] = isset( $market_summary['high'] ) ? $market_summary['high'] : null;
			$market_summary['low'] = isset( $market_summary['low'] ) ? $market_summary['low'] : null;
			$market_summary['base_volume'] = isset( $market_summary['volume'] ) ? $market_summary['volume'] : null;
			$market_summary['quote_volume'] = isset( $market_summary['quoteVolume'] ) ? $market_summary['quoteVolume'] : null;
			$market_summary['btc_volume'] = null;
			$market_summary['last_price'] = isset( $market_summary['lastTradeRate'] ) ? $market_summary['lastTradeRate'] : null;
			$market_summary['timestamp'] = null;
			$market_summary['bid'] = isset( $market_summary['bidRate'] ) ? $market_summary['bidRate'] : null;
			$market_summary['ask'] = isset( $market_summary['askRate'] ) ? $market_summary['askRate'] : null;
			$market_summary['display_name'] = $market_summary['market'];
			$market_summary['result'] = true;
			$market_summary['created'] = null;
			$market_summary['open_buy_orders'] = null;
			$market_summary['open_sell_orders'] = null;
			$market_summary['percent_change'] = isset( $market_summary['percentChange'] ) ? $market_summary['percentChange'] : null;
			$market_summary['frozen'] = null;
			$market_summary['verified_only'] = null;
			$market_summary['expiration'] = null;
			$market_summary['initial_margin'] = null;
			$market_summary['maximum_order_size'] = null;
			$market_summary['mid'] = null;
			$market_summary['minimum_margin'] = null;
			$market_summary['minimum_order_size_quote'] = null;
			$market_summary['minimum_order_size_base'] = isset( $market_summary['minTradeSize'] ) ? $market_summary['minTradeSize'] * 1.2 : null;
			$market_summary['price_precision'] = 8;
			$market_summary['vwap'] = null;
			$market_summary['market_id'] = null;
			$market_summary['base'] = $market_summary['baseCurrencySymbol'];
			$market_summary['quote'] = $market_summary['quoteCurrencySymbol'];

			unset( $market_summary['symbol'] );
			unset( $market_summary['baseCurrencySymbol'] );
			unset( $market_summary['quoteCurrencySymbol'] );
			unset( $market_summary['minTradeSize'] );
			unset( $market_summary['precision'] );
			unset( $market_summary['status'] );
 			unset( $market_summary['createdAt'] );
			unset( $market_summary['prohibitedIn'] );
			unset( $market_summary['associatedTermsOfService'] );
			unset( $market_summary['tags'] );
			unset( $market_summary['lastTradeRate'] );
			unset( $market_summary['updatedAt'] );
			unset( $market_summary['bidRate'] );
			unset( $market_summary['askRate'] );
			unset( $market_summary['volume'] );
			unset( $market_summary['quoteVolume'] );
			unset( $market_summary['percentChange'] );

			return $market_summary;
		}

		//Needs to be updated for API V3:
		public function get_trades( $market = 'BTC-USD', $opts = array( 'limit' => 10 ) ) {
			$trades = $this->exch->get_market_trades( array( 'market' => $market, 'count' => $opts['limit'] ) );

			$results = [];
			foreach( $trades as $trade ) {
				$trade['market'] = $market;
				$trade['price'] = $trade['rate'];
				$trade['amount'] = $trade['quantity'];
				$trade['timestamp'] = $trade['executedAt'];
				$trade['exchange'] = 'bittrex';
				$trade['tid'] = $trade['id'];
				$trade['type'] = ['takerSide'];

				unset( $trade['id'] );
				unset( $trade['executedAt'] );
				unset( $trade['quantity'] );
				unset( $trade['rate'] );
				unset( $trade['takerSide'] );

				array_push( $results, $trade );
			}

			return $results;
		}

		//Works
		public function get_orderbook( $market = "BTC-USD", $depth = 25 ) {
			$orderbooks = $this->exch->get_orderbook( array( 'market' => $market, 'depth' => $depth ) );
			$n_orderbooks = [];
			$o_orderbooks = [];

			if( isset( $orderbooks['bid'] ) )
				foreach( $orderbooks['bid'] as $orderbook ) {
					$orderbook['type']  = "BID";
					array_push( $n_orderbooks, $orderbook );
				}

			if( isset( $orderbooks['ask'] ) )
				foreach( $orderbooks['ask'] as $orderbook ) {
					$orderbook['type']  = "ASK";
					array_push( $n_orderbooks, $orderbook );
				}

			foreach( $n_orderbooks as $orderbook ) {
				$orderbook['market'] = $market;
				$orderbook['price'] = $orderbook['rate'];
				$orderbook['amount'] = $orderbook['quantity'];
				$orderbook['timestamp'] = null;
				$orderbook['exchange'] = "bittrex";

				unset( $orderbook['quantity'] );
				unset( $orderbook['rate'] );
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
