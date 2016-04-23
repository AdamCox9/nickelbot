<?php
	
	//implements yet another exchange

	class bitfinex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "";
		
		public function __construct( $api_key, $api_secret ) 
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}

		/*****
			exchange specific query
			can we make a generic query object that can be configured for each exchange?
		 *****/
		private function query( $path, array $req = array() ) 
		{

		}
	}
?>