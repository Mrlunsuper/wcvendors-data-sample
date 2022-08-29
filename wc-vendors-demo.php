<?php
/**
 * Plugin Name:     WC Vendors Demo
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     wc-vendors-demo
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wc_Vendors_Demo
 */

// Your code starts here.
define( 'WP_DM_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WP_DM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_DM_PLUGIN_INC', trailingslashit( WP_DM_PLUGIN_PATH ) . '/inc' );

require_once WP_DM_PLUGIN_INC . '/class-wc-vendors-demo-users.php';

/**
 * Add product after insert user.
 *
 * @param array $user_ids Array user id.
 */
function add_user_products( $user_ids ) {
	$user_ids = array_filter( $user_ids, 'is_int' );
	foreach ( $user_ids as $id ) {
		$user           = new WP_User( $id );
		$user_last_name = $user->last_name;

		$product_name = $user_last_name . ' Simple Product';
		$price        = wp_rand( 100, 500 );

		$product_post = array(
			'post_title'  => $product_name,
			'post_author' => $id,
			'post_type'   => 'product',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_regular_price' => wc_format_decimal( $price ),
				'_price'         => wc_format_decimal( $price ),
			),
		);
		$post_id      = wp_insert_post( $product_post );
	}
}

add_action( 'wc_dm_after_create_user', 'add_user_products', 10, 1 );

