<?php

	//implements https://poloniex.com/support/api

	class poloniex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://poloniex.com/tradingApi";
		protected $public_url = "https://poloniex.com/public";
		
		public function __construct($api_key, $api_secret) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
			
		private function query(array $req = array()) 
		{
			usleep(1000000);//rate limit

			/*echo "\n\n";
			print_r( $req );
			echo "\n\n";*/

			// API settings
			$key = $this->api_key;
			$secret = $this->api_secret;

			$mt = explode(' ', microtime());
			$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
		 
			// generate the POST data string
			$post_data = http_build_query($req, '', '&');
			$sign = hash_hmac('sha512', $post_data, $secret);
		 
			// generate the extra headers
			$headers = array(
				'Key: '.$key,
				'Sign: '.$sign,
			);

			// curl handle (initialize if required)
			static $ch = null;
			if (is_null($ch)) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 
					'Mozilla/4.0 (compatible; Poloniex PHP bot; '.php_uname('a').'; PHP/'.phpversion().')'
				);
			}
			curl_setopt($ch, CURLOPT_URL, $this->trading_url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			// run the query
			$res = curl_exec($ch);

			if ($res === false) 
				throw new Exception('Curl error: '.curl_error($ch));
			$dec = json_decode($res, true);
			//if (!$dec)
				//throw new Exception('Invalid data: '.$res);
			return $dec;
		}
		
		protected function retrieveJSON($URL) {
			$opts = array('http' =>
				array(
					'method'  => 'GET',
					'timeout' => 10 
				)
			);
			$context = stream_context_create($opts);
			$feed = file_get_contents($URL, false, $context);
			$json = json_decode($feed, true);
			return $json;
		}

		//Public Methods

		public function returnTicker() {
			return $this->retrieveJSON( $this->public_url . '?command=returnTicker' );
		}
		
		public function return24hVolume() {
			return $this->retrieveJSON( $this->public_url . '?command=return24hVolume' );
		}
		
		public function returnOrderBook( $pair ) {
			return $this->retrieveJSON( $this->public_url . "?command=returnOrderBook&currencyPair=$pair" );
		}
		
		public function returnPublicTradeHistory( $pair ) {
			return $this->retrieveJSON( $this->public_url . "?command=returnTradeHistory&currencyPair=$pair" );
		}
		
		public function returnChartData($pair) {
			return $this->retrieveJSON( $this->public_url . "?command=returnChartData&currencyPair=$pair" );
		}
		
		public function returnCurrencies() {
			return $this->retrieveJSON( $this->public_url . '?command=returnCurrencies' );
		}

		public function returnLoanOrders() {
			return $this->retrieveJSON( $this->public_url . '?command=returnLoanOrders' );
		}

		//Authenticated Methods

		public function returnBalances() {
			if( ! isset( $this->balances ) )
				$this->balances = $this->query( 
					array(
						'command' => 'returnBalances'
					)
				);
			return $this->balances;
		}

		public function returnCompleteBalances() {
			if( ! isset( $this->completeBalances ) )
				$this->completeBalances = $this->query( 
					array(
						'command' => 'returnCompleteBalances'
					)
				);
			return $this->completeBalances;
		}

		public function returnDepositAddresses() {
			if( ! isset( $this->depositAddresses ) )
				$this->depositAddresses = $this->query( 
					array(
						'command' => 'returnDepositAddresses'
					)
				);
			return $this->depositAddresses;
		}

		public function generateNewAddress($currency) {		
			return $this->query( 
				array(
					'command' => 'generateNewAddress',
					'currency' => strtoupper($currency)
				)
			);
		}

		public function returnDepositsWithdrawals() {		
			return $this->query( 
				array(
					'command' => 'returnDepositsWithdrawals',
					'start' => 0,
					'end' => time()
				)
			);
		}
		
		public function returnOpenOrders($currencyPair) {		
			return $this->query( 
				array(
					'command' => 'returnOpenOrders',
					'currencyPair' => $currencyPair
				)
			);
		}
		
		public function returnTradeHistory($currencyPair) {
			return $this->query(
				array(
					'command' => 'returnTradeHistory',
					'currencyPair' => strtoupper($currencyPair)
				)
			);
		}
		
		public function buy($currencyPair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'buy',	
					'currencyPair' => strtoupper($currencyPair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}
		
		public function sell($currencyPair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'sell',	
					'currencyPair' => strtoupper($currencyPair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}
		
		public function cancelOrder($currencyPair, $order_number) {
			return $this->query( 
				array(
					'command' => 'cancelOrder',	
					'currencyPair' => $currencyPair,
					'orderNumber' => $order_number
				)
			);
		}
		
		public function withdraw($currency, $amount, $address) {
			return $this->query( 
				array(
					'command' => 'withdraw',	
					'currency' => strtoupper($currency),				
					'amount' => $amount,
					'address' => $address
				)
			);
		}

		public function returnAvailableAccountBalances() {
			return $this->query( 
				array(
					'command' => 'returnAvailableAccountBalances'
				)
			);
		}

		public function returnTradableBalances() {
			return $this->query( 
				array(
					'command' => 'returnTradableBalances'
				)
			);
		}

		public function transferBalance($currency, $amount, $fromAccount, $toAccount) {
			return $this->query( 
				array(
					'command' => 'withdraw',	
					'currency' => strtoupper($currency),				
					'amount' => $amount,
					'address' => $address
				)
			);
		}

		public function marginBuy($currencyPair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'marginBuy',	
					'currencyPair' => strtoupper($currencyPair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}
		
		public function marginSell($currencyPair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'marginSell',	
					'currencyPair' => strtoupper($currencyPair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}

		public function getMarginPosition($currencyPair) {
			return $this->query( 
				array(
					'command' => 'getMarginPosition',
					'currencyPair' => strtoupper($currencyPair)
				)
			);
		}

		public function closeMarginPosition($currencyPair) {
			return $this->query( 
				array(
					'command' => 'closeMarginPosition',
					'currencyPair' => strtoupper($currencyPair)
				)
			);
		}

		public function createLoanOffer($currency, $amount, $duration, $autoRenew, $lendingRate) {
			return $this->query( 
				array(
					'command' => 'createLoanOffer',
					'currency' => strtoupper($currency),
					'amount' => strtoupper($amount),
					'duration' => strtoupper($duration),
					'autoRenew' => strtoupper($autoRenew),
					'lendingRate' => strtoupper($lendingRate)
				)
			);
		}

		public function cancelLoanOffer($orderNumber) {
			return $this->query( 
				array(
					'command' => 'cancelLoanOffer',
					'orderNumber' => strtoupper($orderNumber)
				)
			);
		}

		public function returnOpenLoanOffers() {
			return $this->query( 
				array(
					'command' => 'returnOpenLoanOffers'
				)
			);
		}

		public function returnActiveLoans() {
			return $this->query( 
				array(
					'command' => 'returnActiveLoans'
				)
			);
		}

		public function toggleAutoRenew($orderNumber) {
			return $this->query( 
				array(
					'command' => 'toggleAutoRenew',
					'orderNumber' => strtoupper($orderNumber)
				)
			);
		}


				
	}

?>