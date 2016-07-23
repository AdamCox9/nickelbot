<?php

//https://www.okcoin.com/about/rest_api.do

	require_once (dirname(__FILE__) . '/OKCoin/OKCoin.php');

	//implements yet another exchange

	class okcoin_local {
		protected $client;
		
		public function __construct( $api_key, $api_secret ) {
			$this->client = new OKCoin(new OKCoin_ApiKeyAuthentication($api_key, $api_secret));
		}

		public function tickerApi(){
			$params = array('symbol' => 'ltc_usd');
			return $this->client->tickerApi($params);
		}

		public function depthApi( $symbol = 'btc-usd', $size = 5 ) {
			$params = array('symbol' => $symbol, 'size' => $size);
			return $this->client->depthApi($params);
		}


			//$params = array('symbol' => 'btc_usd');
			//$result = $client -> tradesApi($params);

			//$params = array('symbol' => 'btc_usd', 'type' => '1day', 'size' => 5);
			//$result = $client -> klineDataApi($params);

			//$params = array('api_key' => API_KEY);
			//$result = $client -> userinfoApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'type' => 'buy', 'price' => 1, 'amount' => 1);
			//$result = $client -> tradeApi($params);
			//var_dump($result);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'type' => 'buy', 'orders_data' => "[;price:3,amount:5,type:'sell'var_dump($result);,;price:3,amount:3,type:'buy'var_dump($result);,;price:3,amount:3var_dump($result);]");
			//$result = $client -> batchTradeApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'order_id' => '546,456,998,65656');
			//$result = $client -> cancelOrderApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'order_id' => -1);
			//$result = $client -> orderInfoApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'status' => 0, 'current_page' => '1', 'page_length' => '1');
			//$result = $client -> ordersInfoApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'type' => 0, 'order_id' => '123,123,555');
			//$result = $client -> orderHistoryApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'chargefee' => '0.0001', 'trade_pwd' => '123456', 'withdraw_address' => '405sdsdsdsdsdsds', 'withdraw_amount' => 1);
			//$result = $client -> withdrawApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'withdraw_id' => 301);
			//$result = $client -> cancelWithdrawApi($params);

			//$params = array('symbol' => 'btc_usd', 'contract_type' => 'this_week');
			//$result = $client -> tickerFutureApi($params);

			//$params = array('symbol' => 'btc_usd', 'contract_type' => 'this_week', 'size' => 5);
			//$result = $client -> depthFutureApi($params);

			//$params = array('symbol' => 'btc_usd', 'contract_type' => 'this_week');
			//$result = $client -> tradesFutureApi($params);

			//$result = $client -> getUSD2CNYRateFutureApi(null);

			//$params = array('symbol' => 'btc_usd');
			//$result = $client -> getEstimatedPriceFutureApi($params);

			//$params = array('symbol' => 'btc_usd', 'date' => '2014-10-31', 'since' => '0');
			//$result = $client -> futureTradesHistoryFutureApi($params);

			//$params = array('symbol' => 'btc_usd', 'type' => '1day', 'contract_type' => 'this_week', 'size' => 5);
			//$result = $client -> getFutureIndexFutureApi($params);

			//$params = array('api_key' => API_KEY);
			//$result = $client -> userinfoFutureApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'contract_type' => 'this_week');
			//$result = $client -> positionFutureApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'contract_type' => 'this_week', 'price' => '400', 'amount' => '1', 'type' => '1', 'lever_rate' => '10');
			//$result = $client -> tradeFutureApi($params);

			//$params = array('api_key' => API_KEY, 'orders_data' => '[;price:5,amount:2,type:1,match_price:1var_dump($result);,;price:2,amount:3,type:1,match_price:1var_dump($result);]', 'symbol' => 'btc_usd', 'contract_type' => 'this_week', 'lever_rate' => '10');
			//$result = $client -> batchTradeFutureApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'order_id' => '173126', 'contract_type' => 'this_week');
			//$result = $client -> getOrderFutureApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'order_id' => '173126', 'contract_type' => 'this_week');
			//$result = $client -> cancelFutureApi($params);

			//$params = array('api_key' => API_KEY);
			//$result = $client -> fixUserinfoFutureApi($params);

			//$params = array('api_key' => API_KEY, 'symbol' => 'btc_usd', 'contract_type' => 'this_week', 'type' => 1);
			//$result = $client -> singleBondPositionFutureApi($params);
	}

?>