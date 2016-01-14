<?PHP

	//implements https://www.cryptsy.com/pages/apiv2

	class cryptsy {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://api.cryptsy.com/api/v2/";

		public function __construct( $api_key, $api_secret ) {
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
	
		public function query( $method, array $req = array(), $type = "GET" ) {

			$url = $this->trading_url . $method;

			$mt = explode( ' ', microtime() );
			$req['nonce'] = $mt[1].substr( $mt[0], 2, 6 );

			$query = http_build_query( $req );
			$url .= '?'.$query;

			$sign = hash_hmac( 'sha512', $query, $this->api_secret );
			$headers = [
				'Key: ' . $this->api_key,
				'Sign: ' . $sign,
				'Expect: '
			];

			static $ch = null;

			if( is_null( $ch ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36' );
			}

			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER , $headers );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_VERBOSE, true );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $type );
			
			if ( $type == 'POST' ) {
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $req );
			}

			$output = curl_exec( $ch );

			return json_decode( $output, true );

		}
	 
		//User /:action

		public function info() {
			return $this->query( "info" );
		}
	 
		public function balances() {
			return $this->query( "balances" );
		}
	 
		public function balance( $currency_id = '123' ) {
			return $this->query( "balances/" . $currency_id );
		}
	 
		public function deposits() {
			return $this->query( "deposits" );
		}
	 
		public function deposit( $currency_id = '123' ) {
			return $this->query( "deposits/" . $currency_id );
		}
	 
		public function addresses() {
			return $this->query( "addresses" );
		}
	 
		public function address( $currency_id ) {
			return $this->query( "addresses/" . $currency_id );
		}
	 
		public function get_orders( $arr = array() ) {
			return $this->query( "orders", $arr, "GET" );
		}
	 
		public function order( $currency_id = '123' ) {
			return $this->query( "orders/" . $currency_id );
		}
	 
	 	public function triggers() {
			return $this->query( "triggers" );
		}
	 
		public function trigger( $currency_id = '123' ) {
			return $this->query( "triggers/" . $currency_id );
		}

	 	public function tradehistory( $arr = array() ) {
			return $this->query( "tradehistory", $arr );
		}
	 
	 	public function validatetradekey() {
			return $this->query( "validatetradekey" );
		}
	 
	 	public function transfers() {
			return $this->query( "transfers" );
		}
	 
		public function transfer_currency( $currency_id = '123' ) {
			return $this->query( "transfers/" . $currency_id );
		}
	 
	 	public function withdrawals() {
			return $this->query( "withdrawals" );
		}
	 
		public function withdrawal( $currency_id = '123' ) {
			return $this->query( "withdrawals/" . $currency_id );
		}

		public function transfer( $currency_id = '123' ) {
			return $this->query( "transfer/" . $currency_id );
		}
	 
		public function withdraw( $currency_id = '123' ) {
			return $this->query( "withdraw/" . $currency_id );
		}

		//Markets /markets/:id/:action
		
		public function markets() {
			return $this->query( "markets" );
		}

		public function market( $market_id = '123' ) {
			return $this->query( "markets" . $market_id );
		}
		
		public function markets_volume() {
			return $this->query( "markets/volume" );
		}

		public function market_volume( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/volume" );
		}

		public function markets_ticker() {
			return $this->query( "markets/ticker" );
		}

		public function market_ticker( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/ticker" );
		}

		public function market_fees( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/fees" );
		}

		public function market_triggers( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/triggers" );
		}

		public function market_orderbook( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/orderbook" );
		}

		public function market_tradehistory( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/tradehistory" );
		}

		public function market_ohlc( $market_id = '123' ) {
			return $this->query( "markets/" . $market_id . "/ohlc" );
		}

		//Currencies /currencies/:id/:action

		public function currencies() {
			return $this->query("currencies");
		}

		public function currency( $currency_id = '123' ) {
			return $this->query( "currencies/" . $currency_id );
		}

		public function currency_markets( $currency_id = '123' ) {
			return $this->query( "currencies/" . $currency_id . "/markets" );
		}

		public function currency_status( $currency_id = '123' ) {
			return $this->query( "currencies/" . $currency_id . "/status" );
		}

		//Order /order/:id
		
		public function create_order( $arr = array() ) {
			return $this->query( "order", $arr, "POST" );
		}

		public function order_info( $order_id ) {
			return $this->query( "order/" . $order_id, array(), "GET" );
		}

		public function cancel_order( $order_id ) {
			return $this->query( "order/" . $order_id, array(), "DELETE" );
		}

		//Trigger /trigger/:id

		public function create_trigger() {
			return $this->query("trigger");
		}

		public function trigger_info( $trigger_id ) {
			return $this->query( "trigger/" . $trigger_id );
		}

		public function delete_trigger( $trigger_id ) {
			return $this->query( "trigger/" . $trigger_id );
		}

		//Converter /converter/:id

		public function converter() {
			return $this->query( "converter" );
		}

		public function converter_id( $converter_id ) {
			return $this->query( "converter/" . $converter_id );
		}

		public function converter_depositaddress( $id ) {
			return $this->query( "converter" . $id );
		}

	}
?>