<?php
	
	//implements https://www.bitfinex.com/pages/api

	class bitfinex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://api.bitfinex.com/v1";
		protected $pub_url_v2 = "https://api-pub.bitfinex.com/v2";
		protected $auth_url_v2 = "https://api.bitfinex.com/v2/auth";

		public function __construct( $api_key, $api_secret ) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
			
		private function query( $path, array $req = array() ) 
		{
			sleep( 1 );

			// API settings
			$key = $this->api_key;
			$secret = $this->api_secret;
			$mt = explode( ' ', microtime() );
			$req['nonce'] = $mt[1] . substr( $mt[0], 2, 6 );
			$req['request'] = "/v1" . $path;		 
			// generate the POST data string
			$post_data = base64_encode( json_encode( $req ) );
			$sign = hash_hmac( 'sha384', $post_data, $secret );
		 
			// generate the extra headers
			$headers = array(
				'X-BFX-APIKEY: ' . $key,
				'X-BFX-PAYLOAD: ' . $post_data,
				'X-BFX-SIGNATURE: ' . $sign,
			);

			// curl handle (initialize if required)
			static $ch = null;
			if( is_null( $ch ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_USERAGENT, 
					'Mozilla/4.0 (compatible; Bitfinex PHP bot; ' . php_uname( 'a' ) . '; PHP/' . phpversion() . ')'
				);
			}
			curl_setopt( $ch, CURLOPT_URL, $this->trading_url . $path );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );

			// run the query
			$res = curl_exec( $ch );

			if( $res === false ) 
				throw new Exception( 'Curl error: ' . curl_error( $ch ) );
			$dec = json_decode( $res, true );

			return $dec;
		}

		//Public Functions:

		//	https://api-pub.bitfinex.com/v2/platform/status
		public function platform_status( ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/platform/status" ), true );
		}

		//     --url https://api-pub.bitfinex.com/v2/ticker/tBTCUSD \
		//	https://api-pub.bitfinex.com/v2/ticker/{symbol}
		public function ticker( $symbol = "tBTCUSD" ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/ticker/" . $symbol ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/tickers?symbols=ALL' \
		//	https://api-pub.bitfinex.com/v2/tickers
		//	https://api-pub.bitfinex.com/v2/tickers?symbols=ALL
		public function tickers( ) {
			echo "\n" . $this->pub_url_v2 . "/tickers?symbols=All" . "\n";
			return json_decode( file_get_contents( $this->pub_url_v2 . "/tickers?symbols=ALL" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/tickers/hist?symbols=ALL&limit=100' \
		//	https://api-pub.bitfinex.com/v2/tickers/hist
		public function tickers_history( ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/tickers/hist?symbols=All&limite=100" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/trades/tBTCUSD/hist?limit=125&sort=-1' \
		//	https://api-pub.bitfinex.com/v2/trades/{symbol}/hist
		public function trades( $symbol = "tBTCUSD" ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/trades/" . $symbol . "/hist?limit=125&sort=-1" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/book/tBTCUSD/P0?len=25' \
		//	https://api-pub.bitfinex.com/v2/book/{symbol}/{precision}
		public function book( $symbol = "tBTCUSD" ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/book/" . $symbol . "/P0?len=25" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/stats1/pos.size:1m:tBTCUSD:long/hist?sort=-1' \
		//	https://api-pub.bitfinex.com/v2/stats1/{key}:{size}:{symbol}:{side}/{section}
		public function stats( ) {
			return array( "ERROR" => "METHOD_NOT_IMPLEMENTED" );
			//return json_decode( file_get_contents( $this->pub_url_v2 . "/stats1/" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/candles/trade:1m:tBTCUSD/hist?limit=120&sort=-1' \
		//	https://api-pub.bitfinex.com/v2/candles/trade:{TimeFrame}:{Symbol}/{Section}
		public function candles( $symbol = "tBTCUSD" ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/candles/trade:1m:" . $symbol . "/hist?limit=120&sort=-1" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/status/deriv?keys=tBTCF0%3AUSTF0%2CtETHF0%3AUSTF0' \
		//	https://api-pub.bitfinex.com/v2/status/deriv
		public function derivatives_status( ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/status/deriv" ), true );
		}


		//     --url 'https://api-pub.bitfinex.com/v2/status/deriv/tBTCF0%3AUSTF0/hist?start=1568123933000&end=1570578740000&sort=-1&limit=100' \
		//	https://api-pub.bitfinex.com/v2/status/{type}/{symbol}/hist
		public function derivatives_status_history( ) {
			return array( "ERROR" => "METHOD_NOT_IMPLEMENTED" );
			return json_decode( file_get_contents( $this->pub_url_v2 . "/status/deriv" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/liquidations/hist?limit=120&sort=-1' \
		//	https://api-pub.bitfinex.com/v2/liquidations/hist
		public function liquidations( ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/liquidations/hist?limit=120&sort=-1" ), true );
		}

		//     --url 'https://api-pub.bitfinex.com/v2/rankings/vol:3h:tBTCUSD/hist?sort=-1&start=start%3D&end=end%3D&limit=125' \
		//	https://api-pub.bitfinex.com/v2/rankings/{Key}:{Time_Frame}:{Symbol}/{Section}
		public function leaderboards( $symbol = "tBTCUSD" ) {
			return json_decode( file_get_contents( $this->pub_url_v2 . "/rankings/vol:3h:" . $symbol . "/hist?sort=-1&start=start&end=end&limit=125" ), true );
		}

		//Authenticated Functions:

		public function account_infos() {
			return $this->query( "/account_infos" );
		}

		public function deposit_new( $method = "bitcoin", $wallet_name = "exchange", $renew = 0 ) {
			return $this->query( "/deposit/new", array( "method" => $method, "wallet_name" => $wallet_name, "renew" => $renew ) );
		}

		public function order_new( $symbol = "ltcbtc", $amount = "0.1", $price = "0.01", $exchange = "bitfinex", $side = "buy", $type = "limit", $is_hidden = true ) {
			return $this->query( "/order/new", array( "symbol" => $symbol, "amount" => $amount, "price" => $price, "exchange" => $exchange, "side" => $side, "type" => $type, "is_hidden" => $is_hidden ) );
		}
		
		public function order_new_multi( $orders = array( array( 'symbol' => "ltcbtc", 'amount' => "0.1", 'price' => "0.01", 'exchange' => "bitfinex", 'side' => "buy", 'type' => "limit", 'is_hidden' => true ) ) ) {
			return $this->query( "/order/new/multi", array( 'orders' => $orders ) );
		}

		public function order_cancel( $order_id ) {
			return $this->query( "/order/cancel", array( 'order_id' => $order_id ) );
		}

		public function order_cancel_multi() {
			return array('error'=>'NOT_IMPLEMENTED');
		}

		public function order_cancel_all() {
			return $this->query( "/order/cancel/all" );
		}

		public function order_cancel_replace() {
			return array( 'error'=>'NOT_IMPLEMENTED' );
		}

		public function order_status( $order_id ) {
			return $this->query( "/order/status", array( 'order_id' => $order_id ) );
		}

		public function orders() {
			return $this->query( "/orders" );
		}

		public function positions() {
			return $this->query( "/positions" );
		}

		public function position_claim() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function history() {
			return $this->query( "/history" );
		}

		public function history_movements( $currency = "BTC" ) {
			return $this->query( "/history/movements", array( 'currency' => $currency )  );
		}

		public function mytrades( $arr = array() ) {
			return $this->query( "/mytrades", $arr );
		}

		public function offer_new() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function cancel_offer() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function offer_status() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function credits() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function taken_funds() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function unused_taken_funds() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function total_taken_funds() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function funding_close() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function balances() {
			return $this->query( "/balances" );
		}

		public function margin_infos() {
			return $this->query( "/margin_infos" );
		}

		public function transfer() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function withdraw() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

		public function key_info() {
			return array( 'error' => 'NOT_IMPLEMENTED' );
		}

	}
?>
