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
			usleep( 1000000 ); //sleep for 1 second so don't overload server...

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

			// decode results
			$result = json_decode($result, true);
			if(!is_array($result))
				throw new Exception('JSON decode error');

			return $result;
		}

		function QueryPublic($method, array $request = array())
		{
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

		function Assets() {
			return $this->QueryPublic( 'Assets' );
		}

		function AssetPairs() {
			return $this->QueryPublic( 'AssetPairs' );
		}

		function Ticker( $pair ) {
			return $this->QueryPublic( 'Ticker', array( 'pair' => $pair ) );
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

		function AddOrder( $pair = "BTCLTC", $type = "buy", $ordertype = "limit", $price = "100.00", $volume = "0.01" ) {
			return $this->query( 'AddOrder', array( 'pair' => $pair, 'type' => $type, 'ordertype' => $ordertype, 'price' => $price, 'volume' => $volume ) );
		}

	}

?>