<?PHP

	//implements https://bter.com/api

	class bter {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://bter.com/api/";
		protected $nonce;

		public function __construct($api_key, $api_secret)
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}

		private function query($path, array $req = array()) 
		{

			echo "\n\n";
			echo "$path";
			echo "\n";
			print_r( $req );
			echo "\n\n";

			usleep( 100000 ); //sleep for 1/10th of second so don't overload server...

			$key = $this->api_key;
			$secret = $this->api_secret;
		 
			$mt = explode(' ', microtime());
			$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
		 
			// generate the POST data string
			$post_data = http_build_query($req, '', '&');
			$sign = hash_hmac('sha512', $post_data, $secret);
		 
			// generate the extra headers
			$headers = array(
				'KEY: '.$key,
				'SIGN: '.$sign,
			);

			// curl handle (initialize if required)
			static $ch = null;
			if (is_null($ch)) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Bter PHP bot; '.php_uname('a').'; PHP/'.phpversion().')');
			}
			curl_setopt($ch, CURLOPT_URL, $this->trading_url.$path);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			// run the query
			$res = curl_exec($ch);

			if ($res === false) 
				throw new Exception('Curl error: '.curl_error($ch));

			$dec = json_decode($res, true);
			if (!$dec) 
				throw new Exception('Invalid data: '.$res);
			
			return $dec;
		}

		//Public Functions:
	
		public function pairs() {
			return json_decode( file_get_contents( $this->trading_url . '1/pairs' ), true );
		}

		public function marketinfo() {
			return json_decode( file_get_contents( $this->trading_url . '1/marketinfo' ), true );
		}

		public function marketlist() {
			return json_decode( file_get_contents( $this->trading_url . '1/marketlist' ), true );
		}

		public function tickers() {
			return json_decode( file_get_contents( $this->trading_url . '1/tickers' ), true );
		}

		public function ticker( $curr_a = "BTC", $curr_b = "USD" ) {
			return json_decode( file_get_contents( $this->trading_url . '1/ticker/' . $curr_a . '_' . $curr_b ), true );
		}

		public function depth( $curr_a = "BTC", $curr_b = "USD" ) {
			return json_decode( file_get_contents( $this->trading_url . '1/depth/' . $curr_a . '_' . $curr_b ), true );
		}

		public function trade_history( $curr_a = "BTC", $curr_b = "USD" ) {
			return json_decode( file_get_contents( $this->trading_url . '1/trade/' . $curr_a . '_' . $curr_b ), true );
		}

		//Private Functions:

		public function getfunds() {
			return $this->query( '1/private/getfunds' );
		}

		public function placeorder( $arr = array() ) {
			return $this->query( '1/private/placeorder', $arr );
		}

		//array( 'order_id' => '123' )
		public function cancelorder( $arr = array() ) {
			return $this->query( '1/private/cancelorder', $arr );
		}

		public function getorder( $arr = array( 'order_id' => '123' ) ) {
			return $this->query( '1/private/getorder', $arr );
		}

		public function orderlist() {
			return $this->query( '1/private/orderlist' );
		}

		public function mytrades( $arr = array() ) {
			return $this->query( '1/private/mytrades', $arr );
		}

	}
?>