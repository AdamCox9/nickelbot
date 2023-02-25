<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will cancel the oldest orders.

		$_CONFIG['direction'] = "BOTH";		// [BOTH|BUY|SELL] types of orders to cancel
		$_CONFIG['count'] = 3;			// Number of orders to cancel

	*/

	function cancel_oldest_orders( $Adapters, $_CONFIG = array() ) {
		//_____get open orders, sort them by creation date:
		foreach( $Adapters as $Adapter ) {
			echo "*** cancel_oldest_orders: " . get_class( $Adapter ) . " ***\n";

			$open_orders = $Adapter->get_open_orders( );

			if( isset( $open_orders['MESSAGE'] ) )
				if( isset( $open_orders['MESSAGE']['ERROR'] ) )
					if( $open_orders['MESSAGE']['ERROR'] == "GOT_EMPTY_RESPONSE" )
						continue; //no open orders

			//print_r( $open_orders );
			echo " -> got " . count( $open_orders ) . " open orders \n";
			//die( "TEST" );

			//Only close BUY orders:
			if( $_CONFIG['direction'] == 'BUY' )
				foreach( $open_orders as $key => $open_order ) {
					if( $open_order['side'] == 'BUY' ) continue;				
					unset( $open_orders[$key] ); //Remove SELL order
				}

			//Only close SELL orders:
			elseif( $_CONFIG['direction'] == 'SELL' )
				foreach( $open_orders as $key => $open_order ) {
					if( $open_order['side'] == 'SELL' ) continue;				
					unset( $open_orders[$key] ); //Remove BUY order
				}

			//print_r( $open_orders );
			echo " -> narrowed down to " . count( $open_orders ) . " open orders \n";
			//die( "TEST" );

			//Ensure they are sorted by time created:
			//TODO test time format recieved from adapter/library:
			usort( $open_orders, function( $a, $b ) {
				return strtotime( $b['timestamp_created'] ) - strtotime( $a['timestamp_created'] );
			});

			//Only oldest orders set by config:
			if( count( $open_orders ) >= $_CONFIG['count'] )
				array_splice( $open_orders, 0, count( $open_orders ) - $_CONFIG['count'] );

			//print_r( $open_orders );
			echo " -> narrowed down to oldest " . count( $open_orders ) . " open orders \n";
			//die( "TEST" );

			//_____remove open orders
			foreach( $open_orders as $open_order ) {
				//print_r( $open_order );
				print_r( $Adapter->cancel( $open_order['id'] ) );
				sleep(1);
			}
		}
	}

?>
