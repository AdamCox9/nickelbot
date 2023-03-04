<?php

	//implements https://docs.exchange.coinbase.com/

	class coinbase {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://api.coinbase.com/api/v3/brokerage";
		
		public function __construct( $api_key, $api_secret )
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
			
		public function query( $method, $params = array(), $type = "GET" ) 
		{

			$key = $this->api_key;
			$secret = $this->api_secret;

			$time = time();
			$url = $this->trading_url . $method;

			$body = (!empty($params) ? json_encode($params) : '');

			$data = $time.$type.$method.$body;

			$sign = base64_encode( hash_hmac("sha256", $data, base64_decode( $secret ), true ) );                

/*

--header "CB-ACCESS-KEY: Sd55555555555tP3"
--header "CB-ACCESS-SIGN: 35f238402bb2400aeb0a048dab64d6cd82d99590da255bd9afe1c1f37fd1686d"
--header "CB-ACCESS-TIMESTAMP: 1667500462"
*/


			$headers = array(
				'User-Agent: Coinbase Compatible PHP',
				'Content-Type: application/json',
				'CB-ACCESS-KEY: '.$key,
				'CB-ACCESS-SIGN: '.$sign,
				'CB-ACCESS-TIMESTAMP: '.$time
			);

			static $ch = null;

			if (is_null($ch)) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

			if( $type == "POST" ) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			}
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

			// run the query
			$res = curl_exec($ch);

			if ($res === false) 
				throw new Exception('Curl error: '.curl_error($ch));

			$dec = json_decode($res, true);
			
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

		public function accounts() {
			return $this->query('/accounts');
		}

		public function account($order_id) {
			return $this->query('/accounts/'.$order_id);
		}

		public function account_ledger($account_id) {
			return $this->query('/accounts/'.$account_id.'/ledger');
		}

		public function account_holds($account_id) {
			return $this->query('/accounts/'.$account_id.'/holds');
		}

		public function create_order( $arr = array() ) {
			return $this->query('/orders', $arr, "POST");
		}

		public function cancel_order( $order_id ) {
			return $this->query('/orders/'.$order_id, array(), "DELETE" );
		}

		public function get_orders() {
			return $this->query('/orders');
		}

		public function get_fills( $arr = array() ) {
			return $this->query('/fills');
		}

		public function transfers( $arr = array() ) {
			return $this->query('/transfers');
		}

		public function reports( $arr = array() ) {
			return $this->query('/reports');
		}

		public function products() {
			$products = $this->query('/products');
			print_r( $products );
			die( "TEST" );
			return $products;
		}

		public function products_book( $product_id ) {
			return $this->query('/products/'.$product_id.'/book?level=2' );
		}

		public function products_ticker( $product_id ) {
			return $this->query('/products/'.$product_id.'/ticker');
		}

		//need to get pagination working...
		public function products_trades( $product_id, $limit = 10 ) {
			return $this->query('/products/'.$product_id.'/trades?limit=' . $limit);
		}

		public function products_candles( $product_id ) {
			return $this->query('/products/'.$product_id.'/candles');
		}

		public function products_stats( $product_id ) {
			return $this->query('/products/'.$product_id.'/stats');
		}

		public function currencies() {
			return $this->products();
		}

		public function get_time() {
			return $this->query('/time');
		}

	}
?>
