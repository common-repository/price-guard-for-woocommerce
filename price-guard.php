<?php
/**
 * Plugin Name: Price Guard - Hide Price & Add to Cart for WooCommerce
 * Plugin URI: https://wpmario.com/price-guard
 * Description: Use Price Guard to hide prices and "Add to Cart" buttons for products and categories. It also lets you add a request a quote option to your WooCommerce store.
 * Version: 1.0.0
 * Author: WP Mario
 * Author URI: https://wpmario.com
 * Text Domain: price-guard
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 6.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package PriceGuard
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'PRICE_GUARD_VERSION', '1.0.0' );
define( 'PRICE_GUARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRICE_GUARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Declare HPOS compatibility.
add_action(
    'before_woocommerce_init',
    function() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }
);

// Include the main PriceGuard class.
if ( ! class_exists( 'PriceGuard' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-price-guard.php';
}

/**
 * Main instance of PriceGuard.
 *
 * Returns the main instance of PriceGuard to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return PriceGuard
 */
function price_guard() {
    return PriceGuard::instance();
}

// Global for backwards compatibility.
$GLOBALS['price_guard'] = price_guard();