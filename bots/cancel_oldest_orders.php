<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will cancel the oldest orders.

	*/

	function cancel_oldest_orders( $Adapters ) {


			/*****

			//_____get open orders, sort them by creation date:
			foreach( $market_summaries as $market_summary ) {
				$open_orders = $Adapter->get_open_orders( $market_summary['market'] );
				usort($open_orders, function($a, $b) {
					return $b['timestamp_created'] - $a['timestamp_created'];
				});

				print_r( $open_orders );

				//_____remove open orders
				foreach( $open_orders as $open_order ) {
					print_r( $open_order );
					if( get_class( $Adapter ) == "PoloniexAdapter" )
						print_r( $Adapter->cancel( $open_order['id'], array( "market" => $open_order['market'] ) ) );
					else
						print_r( $Adapter->cancel( $open_order['id'] ) );
					sleep(1);
				}
				sleep(1);
			}

			******/

	}

?>