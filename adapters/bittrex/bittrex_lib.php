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

		private function queryGET( $path="/markets", array $req = array() ) 
		{
			sleep(1);//Don't want to hammer the API...
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

		private function queryDELETE( $path="/orders", array $req = array( 'id' => 'ERE-3FE-3ff' ) ) 
		{
			sleep(1);//Don't want to hammer the API...
			$timestamp = time()*1000;
			$url = $this->trading_url.$path."/".$req['id'];
			
			$method = "DELETE";
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
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    
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
			sleep(1);//Don't want to hammer the API...
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


		//_____ GET Functions:
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

		public function get_tickers() {
			return $this->queryGET( "/markets/tickers" );
		}

		public function get_balance( $arr = array( 'currency' => 'BTC' ) ) {
			return $this->queryGET( "/balances/" . $arr['currency'] );
		}

		public function get_balances() {
			return $this->queryGET( "/balances" );
		}

		public function get_openorders( $arr = array() ) {
			return $this->queryGET( "/orders/open", $arr );
		}

		public function get_addresses() {
			return $this->queryGET( "/addresses" );
		}

		public function get_withdrawals() {
			return $this->queryGET( "/withdrawals/closed" );
		}

		public function get_deposits() {
			return $this->queryGET( "/deposits/closed" );
		}

		//_____ POST Functions:
		public function post_buy( $arr = array() ) {
			$arr['direction'] = "buy";
			return $this->queryPOST( "/orders", $arr );
		}

		public function post_sell( $arr = array() ) {
			$arr['direction'] = "sell";
			return $this->queryPOST( "/orders", $arr );
		}

		//_____ DELETE Functions:
		public function order_cancel( $arr = array("uuid" => '123' ) ) {
			return $this->queryDELETE("/orders", $arr);
		}



	}

?>
