<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will cancel the oldest orders.

	*/

	function cancel_oldest_orders( $Adapters ) {

		//_____get open orders, sort them by creation date:
		foreach( $Adapters as $Adapter ) {
			$open_orders = $Adapter->get_open_orders( );

			//print_r( $open_orders );
			echo " -> got " . count( $open_orders ) . " open orders \n";
			//die( "TEST" );

			//Only close BUY orders:
			foreach( $open_orders as $key => $open_order ) {
				if( $open_order['direction'] == 'BUY' ) continue;				
				unset( $open_orders[$key] ); //Remove SELL order
			}

			//print_r( $open_orders );
			echo " -> narrowed down to " . count( $open_orders ) . " open BUY orders \n";
			//die( "TEST" );

			//Ensure they are sorted by time created:
			//TODO test time format recieved from adapter/library:
			usort($open_orders, function($a, $b) {
				return strtotime( $b['timestamp_created'] ) - strtotime( $a['timestamp_created'] );
			});

			//Only oldest 5 orders:
			array_splice($open_orders, 0, count($open_orders)-5);

			//print_r( $open_orders );
			echo " -> narrowed down to oldest " . count( $open_orders ) . " open BUY orders \n";
			//die( "TEST" );

			//_____remove open orders
			foreach( $open_orders as $open_order ) {
				//print_r( $open_order );
				if( get_class( $Adapter ) == "PoloniexAdapter" )
					print_r( $Adapter->cancel( $open_order['id'], array( "market" => $open_order['market'] ) ) );
				else
					print_r( $Adapter->cancel( $open_order['id'] ) );
				sleep(1);
			}
		}
	}

?>
