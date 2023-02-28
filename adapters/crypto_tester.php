<?PHP

	class Tester {

		public function test( $method, $data ) {

			if( isset( $data['ERROR'] ) ) {
				switch( $data['ERROR'] ) {
					case 'METHOD_NOT_AVAILABLE':
						return;
					case 'METHOD_NOT_IMPLEMENTED':
						return;
					case 'CURRENCY_NOT_FOUND':
						return;
					default:
						die( "UNKNOWN ERROR" );
				}
			}

			if( ! is_array( $data ) ) {
				print_r( $data );
				die( "ERROR: Expecting array." );
			}

			$method = 'test_' . $method;
			return $this->$method( $data );

		}

		private function test_currencies( $currencies ) {
			foreach( $currencies as $currency ) {
				$keys = array( 'currency', 'aclass', 'altname', 'decimals', 'display_decimals', 'collateral_value', 'status' );
				$numbers = array( 'decimals', 'display_decimals' );
				$strings = array( 'currency', 'aclass', 'altname', 'status' );
				$this->test_currency( $currency['currency'] );
				$this->equal_keys( $keys, $currency );
				$this->numbers( $numbers, $currency );
				$this->strings( $strings, $currency );
			}
		}
		
		private function test_currency( $currency ) {
			if( strtoupper( $currency ) !== $currency )
				die( "Currency must be uppercase: $currency" );
			if( strlen( $currency ) < 1 || strlen( $currency ) > 20 )
				die( "Currency must be 1-20 characters: $currency" );
		}

		private function test_markets( $markets ) {
			foreach( $markets as $market )
				if( ! is_string( $market ) )
					die( "Market must be a string: $market" );
		}

		private function test_market_summaries( $market_summaries ) {
			foreach( $market_summaries as $market_summary )
				$this->test_market_summary( $market_summary );
		}

		private function test_market_summary( $market_summary ) {
			$keys = array(	'ask', 'base_volume', 'bid', 'btc_volume', 'created', 'display_name', 'exchange', 
							'expiration', 'frozen', 'high', 'initial_margin', 'last_price', 
							'low', 'market', 'market_id', 'maximum_order_size', 'mid', 'minimum_margin', 
							'minimum_order_size_base', 'minimum_order_size_quote', 'open_buy_orders',
							'open_sell_orders', 'percent_change', 'price_precision', 
							'quote_volume', 'result', 'timestamp', 'verified_only', 'vwap',
							'altname', 'wsname', 'aclass_base', 'base', 'aclass_quote', 'quote', 'lot', 
							'cost_decimals', 'pair_decimals', 'lot_decimals', 'lot_multiplier',
							'leverage_buy', 'leverage_sell', 'fees', 'fees_maker', 'fee_volume_currency', 'margin_call', 'margin_stop', 
							'ordermin', 'costmin', 'tick_size', 'status', 'OHLC', 'Depth', 'Trades', 'Spread' );

			$numbers = array( 'ask', 'base_volume', 'bid', 'high', 'last_price', 'low', 'quote_volume' );
			$strings = array( 'display_name', 'exchange' );
			$above_zero = array( );
			$not_null = array_merge( $numbers, $strings  );

			//Tests:
			$this->equal_keys( $keys, $market_summary );
			$this->numbers( $numbers, $market_summary );
			$this->not_null( $not_null, $market_summary );
			$this->above_zero( $above_zero, $market_summary );
			$this->strings( $strings, $market_summary );

			if(  is_null( $market_summary['minimum_order_size_base'] ) && is_null( $market_summary['minimum_order_size_quote'] ) ) {
				print_r( $market_summary );
				die( "\n\nEither base or quote minimum order size is required!\n\n" );
			}
		}

		private function test_balances( $balances ) {
			foreach( $balances as $balance ) {
				$this->test_balance( $balance );
			}
		}

		private function test_balance( $balance ) {
			$keys = array( 'type', 'currency', 'available', 'total', 'reserved', 'pending', 'btc_value' );
			$numbers = array( 'available', 'total', 'reserved', 'pending' );
			$this->equal_keys( $keys, $balance );
			$this->numbers( $numbers, $balance );
		}

		private function test_cancel_all( $cancel_all ) {
			if( is_array( $cancel_all ) )
				$this->equal_keys( array( 'success', 'error', 'message' ), $cancel_all );
			else
				die( "parameter must be array cancel_all" );
		}

		private function test_volumes( $volumes ) {
			$keys = array( 'market', 'base_volume', 'quote_vol' );
			foreach( $volumes as $volume ) {
				$this->equal_keys( $keys, $volume );
			}
		}

		private function test_worth( $volumes ) {
			$keys = array( 'btc_worth' );
			foreach( $volumes as $volume ) {
				$this->equal_keys( $keys, $volume );
			}
		}

		private function test_deposit_addresses( $addresses ) {
			foreach( $addresses as $address ) {
				$this->test_deposit_address( $address );
			}
		}

		private function test_deposit_address( $address ) {
			$keys = array( 'currency', 'address', 'wallet_type', 'status', 'cryptoAddressTag' );
			$this->equal_keys( $keys, $address );
		}

		private function test_open_orders( $active_orders ) {
			$keys = array( 'id', 'market', 'price', 'timestamp_created', 'exchange', 'avg_execution_price', 'side','type', 'is_live', 'is_cancelled', 'is_hidden', 'was_forced', 'original_amount', 'remaining_amount', 'executed_amount', 'amount' );
			foreach( $active_orders as $active_order ) {
				$this->equal_keys( $keys, $active_order );
			}
		}

		private function test_completed_orders( $completed_orders ) {
			$keys = array( 'market', 'price', 'amount', 'timestamp', 'exchange', 'type', 'fee_currency', 'fee_amount', 'tid', 'order_id', 'id', 'fee', 'total' );
			foreach( $completed_orders as $completed_order )
				$this->equal_keys( $keys, $completed_order );
		}

		private function test_buy_order( $buy_order ) {
			$keys = array( 'order_id', 'success' );
			$this->equal_keys( $keys, $buy_order );
		}

		private function test_sell_order( $sell_order ) {
			$keys = array( 'order_id', 'success' );
			$this->equal_keys( $keys, $sell_order );
		}

		private function test_trades( $trades ) {
			$keys = array( 'market', 'price', 'amount', 'timestamp', 'exchange', 'tid', 'type' );
			foreach( $trades as $trade )
				$this->equal_keys( $keys, $trade );
		}

		private function test_orderbook( $orderbook ) {
			$keys = array( 'market', 'price', 'amount', 'timestamp', 'exchange', 'type' );
			foreach( $orderbook as $order )
				$this->equal_keys( $keys, $order );
		}

		private function test_positions( $positions ) {
			$keys = array( 'market', 'price', 'amount', 'timestamp', 'exchange', 'type' );
			foreach( $positions as $position )
				$this->equal_keys( $keys, $position );
		}

		private function test_deposits_withdrawals( $deposits_withdrawals ) {
			$keys = array( 'id', 'address', 'confirmations', 'currency', 'method', 'type', 'amount', 'description', 'status', 'fee', 'timestamp', 'exchange' );
			foreach( $deposits_withdrawals as $transaction ) {
				$this->equal_keys( $keys, $transaction );
			}
		}

		private function test_deposits( $deposits ) {
			$this->test_deposits_withdrawals( $deposits );
		}

		private function test_withdrawals( $withdrawals ) {
			$this->test_deposits_withdrawals( $withdrawals );
		}

		/***********************

			Test Utility Methods

		 ***********************/

		private function not_null( $not_null, $arr ) {
			foreach( $not_null as $field ) {
				if( is_null( $field ) ) {
					print_r( $arr );
					die( "\n\nRequired Not Null ($field)\n\n" );
				}
			}
			return true;
		}

		private function strings( $strings, $arr ) {
			foreach( $strings as $field ) {
				if( ! is_string( $arr[$field] ) ) {
					print_r( $arr );
					print_r( $strings );
					die( "\n\nRequired String ($field)\n\n" );
				}
			}
			return true;
		}

		private function above_zero( $above_zero, $arr ) {
			foreach( $above_zero as $field ) {
				if( is_nan( $arr[$field] ) || is_null( $arr[$field] ) || ! is_numeric( $arr[$field] ) || $arr[$field] <= 0 ) {
					print_r( $arr );
					print_r( $above_zero );
					die( "\n\nRequired Above Zero ($field)\n\n" );
				}
			}
			return true;
		}

		private function equal_keys($keys, $arr) {
			if( sizeof( $keys ) != sizeof( $arr ) ) {
				$broken_keys = array_keys( $arr );
				echo "\n required but not there \n";
				print_r( array_diff( $keys, $broken_keys ) );
				echo "\n shouldn't be there \n";
				print_r( array_diff( $broken_keys, $keys ) );
				echo "\n what is actually there \n";
				print_r( array_keys( $arr ) );
				print_r( $keys );
				die( "\n\nMismatched Array Keys" );
			}
			return true;
		}

		private function numbers( $numbers, $arr ) {
			foreach( $numbers as $number ) {
				if( ! isset( $arr[$number] ) || is_null( $arr[$number] ) || ! is_numeric( $arr[$number] ) ) {
					print_r( $arr );
					print_r( $numbers );
					die( "\n\nRequired Number ($number)\n\n" );
				}
			}
			return true;
		}

		private function capitals( $capitals, $arr ) {
			foreach( $capitals as $capital ) {
				if( ! isset( $arr[$number] ) || is_null( $arr[$number] ) || ( ! strtoupper( $capital ) == $capital ) ) {
					print_r( $arr );
					print_r( $capitals );
					die( "\n\nRequired Capital ($capital)\n\n" );
				}
			}
			return true;
		}

	}

?>
