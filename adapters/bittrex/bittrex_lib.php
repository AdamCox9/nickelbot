<?PHP

	//implements https://bittrex.com/Home/Api

	class bittrex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://bittrex.com/api/v1.1";

		public function __construct( $api_key, $api_secret ) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
	
		private function query( $path, array $req = array() ) 
		{

			echo "\n\n";
			echo "$path";
			echo "\n";
			print_r( $req );
			echo "\n\n";

			$key = $this->api_key;
			$secret = $this->api_secret;

			$req['apikey'] = $key;
			$mt = explode( ' ', microtime() );
			$req['nonce'] = $mt[1];

			$queryString = http_build_query( $req, '', '&' );
			$requestUrl = $this->trading_url . $path . '?' . $queryString;	
			$sign = hash_hmac( 'sha512', $requestUrl, $secret );

			static $ch = null;
			
			if( is_null( $ch ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Bittrex PHP bot; ' . php_uname('a') . '; PHP/' . phpversion() . ')' );
			}
			curl_setopt( $ch, CURLOPT_HTTPGET, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'apisign:' . $sign ) );
			curl_setopt( $ch, CURLOPT_URL, $requestUrl );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
			
			// run the query
			$res = curl_exec( $ch );
			
			if( $res === false )
				throw new Exception( 'Could not get reply: ' . curl_error( $ch ) );
			
			$dec = json_decode( $res, true );
			if ( ! $dec )
				throw new Exception( 'Invalid data: ' . $res );
			
			return $dec;
		}

		//Public Functions

		public function getmarkets() {
			return $this->query( "/public/getmarkets" );
		}

		public function getcurrencies() {
			return $this->query( "/public/getcurrencies" );
		}

		public function getticker( $arr = array( "market" => "BTC-LTC" ) ) {
			return $this->query( "/public/getticker", $arr );
		}

		public function getmarketsummaries() {
			return $this->query( "/public/getmarketsummaries" );
		}

		public function getmarketsummary( $arr = array( "market" => "BTC-LTC" ) ) {
			return $this->query( "/public/getmarketsummary", $arr );
		}

		public function getorderbook( $arr = array() ) {
			return $this->query( "/public/getorderbook", $arr );
		}

		public function getmarkethistory( $arr = array() ) {
			return $this->query( "/public/getmarkethistory", $arr );
		}

		//Private Functions

		public function market_buylimit( $arr = array() ) {
			return $this->query( "/market/buylimit", $arr );
		}

		public function market_buymarket( $arr = array() ) {
			return $this->query( "/market/buymarket", $arr );
		}

		public function market_selllimit( $arr = array() ) {
			return $this->query( "/market/selllimit", $arr );
		}

		public function market_sellmarket( $arr = array() ) {
			return $this->query( "/market/sellmarket", $arr );
		}

		public function market_cancel( $arr = array("uuid" => '123' ) ) {
			return $this->query("/market/cancel", $arr);
		}

		public function market_getopenorders( $arr = array() ) {
			return $this->query( "/market/getopenorders", $arr );
		}

		public function account_getbalances() {
			return $this->query( "/account/getbalances" );
		}

		public function account_getbalance( $arr = array( 'currency' => 'BTC' ) ) {
			return $this->query( "/account/getbalance", $arr );
		}

		public function account_getdepositaddress( $arr = array() ) {
			return $this->query( "/account/getdepositaddress", $arr );
		}

		public function account_withdraw( $arr = array() ) {
			return $this->query( "/account/withdraw", $arr );
		}

		public function account_getorder( $arr = array( 'uuid' => '123' ) ) {
			return $this->query( "/account/getorder", $arr );
		}

		public function account_getorderhistory( $arr = array() ) {
			return $this->query( "/account/getorderhistory", $arr );
		}

		public function account_getwithdrawalhistory( $arr = array( 'market' => 'BTC-LTC', 'count' => 10 ) ) {
			return $this->query( "/account/getwithdrawalhistory", $arr );
		}

		public function account_getdeposithistory( $arr = array( 'market' => 'BTC-LTC', 'count' => 10 ) ) {
			return $this->query( "/account/getdeposithistory", $arr );
		}

	}

?>