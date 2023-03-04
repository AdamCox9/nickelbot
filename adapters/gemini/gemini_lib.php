<?php
	
	//implements yet another exchange

	class gemini {
		protected $key;
		protected $secret;
		protected $trading_url = "https://api.sandbox.gemini.com";
		protected $curl;
		
		public function __construct( $api_key, $api_secret ) 
		{
			$this->key = $api_key;
			$this->secret = $api_secret;
			$this->curl = curl_init();

			curl_setopt_array($this->curl, array(
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_USERAGENT => 'Gemini PHP API Agent',
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true)
			);

		}

		/*
			Header	Value
			Content-Length	0
			Content-Type	text/plain
			X-GEMINI-APIKEY	Your Gemini API key
			X-GEMINI-PAYLOAD	The base64-encoded JSON payload
			X-GEMINI-SIGNATURE	hex(HMAC_SHA384(base64(payload), key=api_secret))
			Cache-Control	no-cache
		*/

		private function query( $path, array $request = array() ) 
		{
			usleep( 1000000 ); //1000000 = 1 Second

			$b64 = base64_encode(utf8_encode(json_encode([ "request" => "/v1/account/list", "nonce" => time() ])));
			$header = [
				'Content-Type: text/plain',
				'Content-Length: 0',
				'X-GEMINI-APIKEY: ' . $this->key,
				'X-GEMINI-PAYLOAD: ' . $b64,
				'X-GEMINI-SIGNATURE: ' . hash_hmac( 'sha384', $b64, utf8_encode( $this->secret ) ),
				'Cache-Control: no-cache'
			    ];
			$ch = curl_init('https://api.sandbox.gemini.com/v1/account/list');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

			$result = curl_exec($ch);
			if($result===false)
				throw new Exception('CURL error: ' . curl_error($this->curl));

			$result = json_decode($result, true);
			if(!is_array($result))
				throw new Exception('JSON decode error');

			return $result;

		}
		
		public function get_info( )
		{
			return $this->query( "/v1/account/list" );
		}
		
	}
?>
