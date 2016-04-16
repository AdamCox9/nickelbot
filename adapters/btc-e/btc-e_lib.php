<?PHP

	//implements https://btc-e.com/api/documentation
	//implements https://btc-e.com/api/3/docs
	//implements https://btc-e.com/tapi/docs

	class btce {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://btc-e.com/tapi/";
		
		public function __construct($api_key, $api_secret) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
			$this->x = 0;
		}

		private function query($method, array $req = array())
		{

			/*echo "\n\n";
			echo "$method";
			echo "\n";
			print_r( $req );
			echo "\n\n";*/

			usleep( 1000000 ); //sleep for 1/10th of second so don't overload server...

			// API settings
			$key = $this->api_key;
			$secret = $this->api_secret;

			$req['method'] = $method;
			$mt = explode(' ', microtime());
			$req['nonce'] = $mt[1] + $this->x++;
		   
			// generate the POST data string
			$post_data = http_build_query($req, '', '&');

			$sign = hash_hmac('sha512', $post_data, $secret);

			// generate the extra headers
			$headers = array(
				'Sign: '.$sign,
				'Key: '.$key,
			);

			// our curl handle (initialize if required)
			static $ch = null;
			if (is_null($ch)) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, 
						'Mozilla/4.0 (compatible; BTCE PHP client; '.php_uname('s').'; PHP/'.phpversion().')'
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

			//fix bug with nonce too small...
			if( isset( $dec['error'] ) && $dec['success'] === 0 ) {
				$nonce = explode( "you should send:", $dec['error'] );
				if( isset( $nonce[1] ) )
					$this->x = $nonce[1] - $req['nonce'];
			}


			if (!$dec) 
				throw new Exception('Invalid data: '.$res);
			return $dec;
		}

		//Public Functions:

		public function info($ticker) {
			return json_decode( file_get_contents( 'https://btc-e.com/api/3/info/' . $ticker ), true );
		}

		public function ticker($ticker) {
			return json_decode( file_get_contents( 'https://btc-e.com/api/3/ticker/' . $ticker ), true );
		}

		public function depth($ticker) {
			return json_decode( file_get_contents( 'https://btc-e.com/api/3/depth/' . $ticker ), true );
		}

		public function trades($ticker) {
			return json_decode( file_get_contents( 'https://btc-e.com/api/3/trades/' . $ticker ), true );
		}

		//Authenticated Functions:

		public function getInfo() {
			return $this->query('getInfo');
		}

		public function TransHistory( $arr = array( 'from' => 0, 'count' => 1000, 'from_id' => '0', 'end_id' => '9999999999999', 'order' => 'DESC', 'since' => 0, 'end' => '9999999999999' ) ) {
			return $this->query('TransHistory');
		}

		public function TradeHistory( $arr = array() ) {
			return $this->query( 'TradeHistory', $arr );
		}

		public function ActiveOrders( $arr = array() ) {
			return $this->query( 'ActiveOrders', $arr );
		}

		public function Trade( $arr = array() ) {
			return $this->query( 'Trade', $arr );
		}
		
		public function CancelOrder( $arr = array() ) {
			return $this->query( 'CancelOrder', $arr );
		}
		
	}

?>