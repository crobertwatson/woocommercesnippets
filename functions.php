/** 
 * Inject item in shop loop
 */ 

function wbdx_wc_inject_item_in_shop_loop() {

	global $wp_query;

	// Get the current post count
	$current_post = $wp_query->current_post;

	if ( $current_post !== 0 && $current_post %3 == 0 ) : // <- change this 3 to position desired
	?>
	<li class="product">
		<div><?php _e("Add your content here"); ?></div>
	</li>
	<?php
	endif;
}
add_action( 'woocommerce_shop_loop', 'wbdx_wc_inject_item_in_shop_loop', 100 );

/** 
 * Processing orders go directly to Completed
 */ 

function wbdx_wc_move_processing_to_completed( $order_id, $from, $to ){
	
	$order = wc_get_order( $order_id );

	if( $to == 'processing' ) {
		$order->update_status( 'completed' );
	}

};
add_action( 'woocommerce_order_status_changed', 'wbdx_wc_move_processing_to_completed', 10, 3 );

/** 
 * Previously purchased note (shortcode)
 * Add a note shortcode for products that have previously been purchased
 */ 

function wbdx_wc_product_purchased() {
	
	if ( ! is_user_logged_in() ) return;

	global $product;
	$current_user = wp_get_current_user();
	if (wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product->get_id() ) ){
		return '<p class="woocommerce-message woocommerce-info">You\'ve previously purchased this item.</p>';'
	}

}
add_shortcode( 'wbdx_wc_product_purchased', 'wbdx_wc_product_purchased' );

/** 
 * Add "sample" button to product page
 */ 

function wbdx_wc_free_sample_button_add_Cart() {
	$sample_id = 953; // enter your sample product's id here
	echo '<p><a href="/?add-to-cart='.$sample_id.'" class="button">Add Sample to Cart</a></p>';
}
add_action( 'woocommerce_single_product_summary', 'wbdx_wc_free_sample_button_add_cart', 35 );

/** 
 * Adds any number of custom columns and data to orders admin page
 */ 

// Add columns on orders page
function wbdx_add_custom_column_headers( $columns ) {
	
	$new_columns = (is_array($columns)) ? $columns : array();

	$new_columns['shipping_postcode'] = 'Shipping Postcode'; // <-- replace with desired data and/or add more

	return $new_columns;
}
add_filter('manage_edit-shop_order_columns', 'wbdx_add_custom_column_headers');

// Add data to the columns
function wbdx_add_custom_column_content( $column ) {
	
	global $post, $the_order;

	if(empty($the_order) || $the_order->get_id() != $post->ID) {
		$the_order = wc_get_order($post->ID);
	}

	// *NOTE* Replace with dsired data or add more
	$shipping_postcode = $the_order->get_shipping_postcode();
	if($column == 'shipping_postcode') {
		echo('<span class="order=shipping-postcode">' . $shipping_postcode . '</span>');
	}
}
add_action('manage_shop_order_posts_custom_column', 'wbdx_add_custom_column_content');

/** 
 * Change "Select Options" text in WooCommerce product archive
 */ 

function wbdx_custom_select_options_button_text( $text ) {
	global $product;
	if ( $product->is_type( 'variable' ) ) {
		$text = $product->is_purchaseable() ? __( 'Custom options text', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
	}
	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'wbdx_custom_select_options_button_text', 10 );

/** 
 * Redirect to Checkout on Add to Cart
 */ 

function wbdx_wc_redirect_cart_to_checkout() {
	return wc_get_checkout_url();
}

add_filter( 'woocommerce_add_to_cart_redirect', 'wbdx_wc_redirect_cart_to_checkout' );

/** 
 * Customize added to cart message
 */ 

function wbdx_wc_custom_add_to_cart_message() {
	$message = 'You did it! Feel free to browse some more or head to <a href="/checkout">the checkout</a>.' ;
	return $message;
}
add_filter( 'wc_add_to_cart_message_html', 'wbdx_wc_custom_add_to_cart_message' );

/** 
 * Add custom notice to single product page
 */ 

function wbdx_wc_custom_product_notice() {
	echo '<div class="woocommerce-message">Free delivery when you add this item to your cart!</div>';
}

add_action( 'woocommerce_single_product_summary', 'wbdx_wc_custom_product_notice', 10 );

/** 
 * Upsell products on the "Thank You" page
 */ 

function wbdx_wc_than_you_upsell() {
	echo '<h2>You might also be interested in:</h2>';
	echo do_shortcoed( '[products columns="3" limit="3" ]' );
}
add_action( 'woocommerce_thankyou', 'wbdx_wc_thank_you_upsell', 10 );
