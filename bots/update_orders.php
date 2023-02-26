<?PHP

	/*

		@Author Adam Cox

		This is a simple example of a bot that will make minimum buy orders on the spread.
		It will set buy orders for every trading pair available for each adapter where balances permit meeting criteria below.

		TODO
		 - Allow to specify certain trading pairs instead of hitting all of them

	*/

	function update_orders( $Adapters = array(), $_CONFIG = array() ) {

		foreach( $Adapters as $Adapter ) {
			echo "*** make_min_orders: " . get_class( $Adapter ) . " ***\n";


			echo " -> getting balances \n";
			$balances = $Adapter->get_balances( );

			//Print balances for debugging:
			//print_r( $balances );

			$open_orders = $Adapter->get_open_orders();
			
			foreach( $open_orders as $open_order ) {
				print_r( $open_order );
				$market = $open_order['market'];
				$market_summary = $Adapter->get_market_summary( $market );
				
				print_r( $market_summary );
								
				$order_id = $open_order['id'];
				$amount = $market_summary['minimum_order_size_base'];
				$price = $market_summary['ask'];

				$results = $Adapter->update_order( $order_id, $amount, $price, array() );
				
				print_r( $results );
				
				//Get the market the order is in
				//Update the price so it is 2% above ask if sell or 2% below bid if buy
				//Next...
				
				die( "TEST" );
			}

			echo " -> finished executing " . get_class( $Adapter ) . "\n\n";
		}
	}

?>
