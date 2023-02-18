<?PHP

	//implements https://bittrex.com/Home/Api

	class bittrex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://api.bittrex.com/v3";

		public function __construct( $api_key, $api_secret ) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}

		
		//TODO: make a queryGET, queryPOST, queryDELETE, queryUPDATE function to make it more compatible with Bittrex v3 API:		
		private function query( $path, array $req = array() ) 
		{
			die( "Error: no longer in use. Use queryGET, queryPOST, queryDELETE, queryUPDATE instead" );
		}

		private function queryGET( $path="/markets", array $req = array() ) 
		{
			$timestamp = time()*1000;
			$url = $this->trading_url.$path;
			$method = "GET";
			$content = "";
			$subaccountId = "";
			$contentHash = hash('sha512', $content);
			$preSign = $timestamp . $url . $method . $contentHash . $subaccountId;
			$signature = hash_hmac('sha512', $preSign, $this->api_secret);

			$headers = array(
			"Accept: application/json",
			"Content-Type: application/json",
			"Api-Key: ".$this->api_key."",
			"Api-Signature: ".$signature."",
			"Api-Timestamp: ".$timestamp."",
			"Api-Content-Hash: ".$contentHash.""
			);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);

			// run the query
			$res = curl_exec( $ch );
			
			if( $res === false )
				throw new Exception( 'Could not get reply: ' . curl_error( $ch ) );
			
			$dec = json_decode( $res, true );
			if ( ! $dec )
				throw new Exception( 'Invalid data: ' . $res );

			curl_close($ch);
			
			return $dec;

		}



		private function queryPOST( $path="/orders", array $req = array() )
		{
			$timestamp = time()*1000;
			$url = $this->trading_url.$path;
			$method = "POST";
			$direction = $req['direction'];
			$market = $req['market'];
			$quantity = $req['quantity'];
			$limit = $req['rate'];

			$content = '{
				"marketSymbol": "'.$market.'",
				"direction": "'.$direction.'",
				"type": "LIMIT",
				"quantity": "'.$quantity.'",
				"limit": "'.$limit.'",
				"timeInForce": "GOOD_TIL_CANCELLED"
			}';

			$subaccountId = "";
			$contentHash = hash('sha512', $content);
			$preSign = $timestamp . $url . $method . $contentHash . $subaccountId;
			$signature = hash_hmac('sha512', $preSign, $this->api_secret);

			$headers = array(
				"Accept: application/json",
				"Content-Type: application/json",
				"Api-Key: ".$this->api_key."",
				"Api-Signature: ".$signature."",
				"Api-Timestamp: ".$timestamp."",
				"Api-Content-Hash: ".$contentHash.""
			);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

			// run the query
			$res = curl_exec( $ch );
			
			if( $res === false )
				throw new Exception( 'Could not get reply: ' . curl_error( $ch ) );
			
			$dec = json_decode( $res, true );
			if ( ! $dec )
				throw new Exception( 'Invalid data: ' . $res );

			curl_close($ch);
			
			return $dec;
		}


		//New Functions
		public function get_markets() {
			return $this->queryGET( "/markets" );
		}

		public function get_markets_summaries() {
			return $this->queryGET( "/markets/summaries" );
		}

		public function get_markets_summary( $arr = array( "market" => "BTC-LTC" ) ) {
			return $this->queryGET( "/markets/".$arr['market'] );
		}

		public function get_currencies() {
			return $this->queryGET( "/currencies" );
		}

		public function get_ticker( $arr = array( "market" => "BTC-LTC" ) ) {
			return $this->queryGET( "/markets/".$arr['market']."/ticker" );
		}

		public function get_balance( $arr = array( 'currency' => 'BTC' ) ) {
			return $this->queryGET( "/balances/" . $arr['currency'] );
		}

		public function post_buy( $arr = array() ) {
			$arr['direction'] = "buy";
			return $this->queryPOST( "/orders", $arr );
		}

		public function post_sell( $arr = array() ) {
			$arr['direction'] = "sell";
			return $this->queryPOST( "/orders", $arr );
		}
		public function get_addresses() {
			return $this->queryGET( "/addresses" );
		}



		//Old Functions (broken)


		public function getorderbook( $arr = array() ) {
			return $this->query( "/public/getorderbook", $arr );
		}

		public function getmarkethistory( $arr = array() ) {
			return $this->query( "/public/getmarkethistory", $arr );
		}


		public function market_buymarket( $arr = array() ) {
			return $this->query( "/market/buymarket", $arr );
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
			return $this->queryGET( "/balances" );
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
