<?php

	//implements https://www.bitstamp.net/api/

	class bitstamp
	{
		private $key;
		private $secret;
		private $client_id;
		private $trading_url;

		public function __construct($api_key, $api_secret, $client_id)
		{
			$this->key = $api_key;
			$this->secret = $api_secret;
			$this->client_id = $client_id;
		}

		private function query($path, array $req = array(), $type = 'post')
		{

			/*echo "\n\n";
			echo "$path";
			echo "\n";
			print_r( $req );
			echo "\n";
			echo "$type";
			echo "\n\n";*/

			usleep( 100000 ); //sleep for 1/10th of second so don't overload server...

			// API settings
			$key = $this->key;
			
			// generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
			$mt = explode(' ', microtime());
			$req['nonce'] = $mt[1] . substr($mt[0], 2, 6);
			$req['key'] = $key;
			$message = $req['nonce'].$this->client_id.$this->key;
			$req['signature'] = strtoupper(hash_hmac('sha256', $message, $this->secret));
			
			// generate the POST data string
			$post_data = http_build_query($req, '', '&');
			// any extra headers
			$headers = array();
			
			// our curl handle (initialize if required)
			static $ch = null;
			if (is_null($ch))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT,
					'Mozilla/4.0 (compatible; Bitstamp PHP Client; ' . php_uname('s') . '; PHP/' .
					phpversion() . ')');
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
			curl_setopt($ch, CURLOPT_URL, 'https://www.bitstamp.net/api/' . $path .'/');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			if ($type == 'post') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$res = curl_exec($ch);
			if ($res === false)
				throw new \Exception('Could not get reply: ' . curl_error($ch));
			$dec = json_decode($res, true);
			if (is_null($dec))
				throw new \Exception('Invalid data received, please make sure connection is working and requested API exists');
			return $dec;
		}
		
		//Public Functions

		public function ticker() {
			return $this->query('ticker', array(), 'get');
		}
		
		public function ticker_hour() {
			return $this->query('ticker_hour', array(), 'get');
		}
		
		function order_book($group=1){
			return $this->query('order_book', array('group' => $group), 'get');
		}

		public function transactions($time='hour'){
			return $this->query('transactions', array('time' => $time), 'get');
		}

		public function eur_usd() {
			return $this->query('eur_usd', array(), 'get');
		}

		//Private Function

		public function balance(){
			return $this->query('balance');
		}
		public function user_transactions($arr){
			return $this->query('user_transactions', $arr, 'post');
		}

		public function open_orders(){
			return $this->query('open_orders');
		}

		public function order_status(){
			return $this->query('open_orders', array(), 'post');
		}

		public function cancel_order($id='0'){
			return $this->query('cancel_order', array('id' => $id));
		}

		public function cancel_all_orders(){
			return $this->query('cancel_all_orders', array(), 'post');
		}

		public function buy($amount="0.01", $price="0.01"){
			return $this->query('buy', array('amount' => $amount, 'price' => $price));
		}

		public function sell($amount="0.01", $price="1000"){
			return $this->query('sell', array('amount' => $amount, 'price' => $price));
		}

		public function withdrawal_requests(){
			return $this->query('withdrawal_requests', array(), 'post');
		}
		
		public function bitcoin_withdrawal(){
			return $this->query('bitcoin_withdrawal', array(), 'post');
		}
		
		public function bitcoin_deposit_address(){
			return $this->query('bitcoin_deposit_address');
		}

		public function unconfirmed_btc(){
			return $this->query('unconfirmed_btc');
		}
		
		public function ripple_withdrawal(){
			return $this->query('ripple_withdrawal', array(), 'post');
		}

		public function ripple_address(){
			return $this->query('ripple_address');
		}

	}