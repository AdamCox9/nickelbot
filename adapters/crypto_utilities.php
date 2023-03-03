<?PHP

	// Utility functions:
	// These should be compatible with all of the data returned from Adapter objects.
	// They should not use the Adapter objects themselves, but they should accept arrays returned from Adapter APIs
	// This can be used to perform custom calculations on data.

	class Utilities {

		public static function get_min_order_size( $min_order_size_base, $min_order_size_quote, $price, $price_precision ) {
			if( $price == 0 )
				return 0;
			if( is_null( $min_order_size_base ) )
				return bcdiv( $min_order_size_quote, $price, $price_precision );
			else
				return $min_order_size_base;
		}

		//Calculate avg_buy_price, avg_sell_price, loss, profit, breakeven sale price, etc...
		public static function analysis( $trades = array(), $time = 0 ){}

		//This function will parse through trades since $time finding the highest & lowest sell price
		public static function get_high_low( $trades = array(), $time = 0 ){}

		//This function will parse through trades and calculate the ema for $time
		//Maybe this is already implemented in PHP???
		public static function get_ema( $trades = array(), $time = 0 ){}

	}
?>
