<?PHP

	class Kraken
	{
		protected $key;     // API key
		protected $secret;  // API secret
		protected $url;     // API base URL
		protected $version; // API version
		protected $curl;    // curl handle

		function __construct($key, $secret, $url='https://api.kraken.com', $version='0', $sslverify=true)
		{
			$this->key = $key;
			$this->secret = $secret;
			$this->url = $url;
			$this->version = $version;
			$this->curl = curl_init();

			curl_setopt_array($this->curl, array(
				CURLOPT_SSL_VERIFYPEER => $sslverify,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_USERAGENT => 'Kraken PHP API Agent',
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true)
			);
		}

		function __destruct()
		{
			curl_close($this->curl);
		}

		function query($method, array $request = array())
		{
			usleep( 1000000 ); //1000000 = 1 Second

			$mt = explode( ' ', microtime() );
			$request['nonce'] = $mt[1] . substr( $mt[0], 2, 6 );

			// build the POST data string
			$postdata = http_build_query($request, '', '&');

			// set API key and sign the message
			$path = '/' . $this->version . '/private/' . $method;

			$sign = hash_hmac('sha512', $path . hash('sha256', $request['nonce'] . $postdata, true), base64_decode($this->secret), true);
			$headers = array(
				'API-Key: ' . $this->key,
				'API-Sign: ' . base64_encode($sign)
			);

			// make request
			curl_setopt($this->curl, CURLOPT_URL, $this->url . $path);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($this->curl);
			if($result===false)
				throw new Exception('CURL error: ' . curl_error($this->curl));

			$result = json_decode($result, true);
			if(!is_array($result))
				throw new Exception('JSON decode error');

			return $result;
		}

		function QueryPublic($method, array $request = array())
		{
			usleep( 1000000 ); //1000000 = 1 Second
		
			// build the POST data string
			$postdata = http_build_query($request, '', '&');

			// make request
			curl_setopt($this->curl, CURLOPT_URL, $this->url . '/' . $this->version . '/public/' . $method);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());
			$result = curl_exec($this->curl);
			if($result===false)
				throw new Exception('CURL error: ' . curl_error($this->curl));

			// decode results
			$result = json_decode($result, true);
			if(!is_array($result))
				throw new Exception('JSON decode error');

			return $result;
		}


		/*
			https://docs.kraken.com/rest/
			TODO implement entirety of API
		*/
		
		//_____Public Functions:

		function Assets() {
			return $this->QueryPublic( 'Assets' );
		}

		function AssetPairs( $pair = null ) {
			if( is_null( $pair ) )
				return $this->QueryPublic( 'AssetPairs' );
			else
				return $this->QueryPublic( 'AssetPairs', array( 'pair' => $pair ) );
		}

		function Ticker( $pair = null ) {
			if( is_null( $pair ) )
				return $this->QueryPublic( 'Ticker' );
			else
				return $this->QueryPublic( 'Ticker', array( 'pair' => $pair ) );
		}

		function OHLC( $pair ) {
			return $this->QueryPublic( 'OHLC', array( 'pair' => $pair ) );
		}

		function Depth( $pair ) {
			return $this->QueryPublic( 'Depth', array( 'pair' => $pair ) );
		}

		function Trades( $pair ) {
			$since = time()-60*60;
			return $this->QueryPublic( 'Trades', array( 'pair' => $pair, 'since' => $since ) );
		}

		function Spread( $pair ) {
			$since = time()-60*60;
			return $this->QueryPublic( 'Spread', array( 'pair' => $pair, 'since' => $since ) );
		}



		//_____Private Functions:
		
		function Balance() {
			return $this->query( 'Balance' );
		}

		function TradeBalance( $asset ) {
			return $this->query( 'TradeBalance', array( 'asset' => $asset ) );
		}

		function DepositAddresses( $method ) {
			return $this->query( 'DepositAddresses', array( 'method' => $method ) );
		}

		function AddOrder( $pair = "LTCXBT", $type = "buy", $ordertype = "limit", $price = "0.001", $volume = "10" ) {
			$out = array( 'pair' => $pair, 'type' => $type, 'ordertype' => $ordertype, 'price' => $price, 'volume' => $volume );
			return $this->query( 'AddOrder', $out );
		}

		function EditOrder( $pair = "LTCXBT", $txid = "0", $price = "0.001", $volume = "10" ) {
			$out = array( 'pair' => $pair, 'txid' => $txid, 'price' => $price, 'volume' => $volume );
			return $this->query( 'EditOrder', $out );
		}

		function OpenOrders() {
			return $this->query( 'OpenOrders' );
		}

		function ClosedOrders() {
			return $this->query( 'ClosedOrders' );
		}

		function CancelOrder( $txid = 0 ) {
			return $this->query( 'CancelOrder', array( 'txid' => $txid ) );
		}

	}

?>
