<?php
/**
 * Uninstall PriceGuard
 *
 * @package PriceGuard
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; // Exit if accessed directly.
}

// Delete plugin options.
delete_option( 'price_guard_hide_prices' );
delete_option( 'price_guard_button_text' );