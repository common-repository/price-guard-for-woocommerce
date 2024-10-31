<?php
/**
 * PriceGuard Public
 *
 * @package PriceGuard
 */

defined( 'ABSPATH' ) || exit;

/**
 * PriceGuard_Public class.
 */
class PriceGuard_Public {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'woocommerce_get_price_html', array( $this, 'hide_price' ), 10, 2 );
        add_filter( 'woocommerce_is_purchasable', array( $this, 'make_unpurchasable' ), 10, 2 );
        add_action( 'woocommerce_single_product_summary', array( $this, 'add_custom_button' ), 30 );
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'modify_add_to_cart_button' ), 10, 2 );
    }


    /**
     * Check if the product should be affected.
     *
     * @param object $product The product object.
     * @return bool
     */
    private function should_affect_product( $product ) {
        $apply_globally = get_option( 'price_guard_apply_globally', 'yes' );
        $categories = get_option( 'price_guard_categories', array() );

        if ( 'yes' === $apply_globally ) {
            return true;
        }

        if ( ! empty( $categories ) ) {
            $product_categories = $product->get_category_ids();
            return ! empty( array_intersect( $product_categories, $categories ) );
        }

        return false;
    }

    /**
     * Hide price.
     *
     * @param string $price   The price HTML.
     * @param object $product The product object.
     * @return string
     */
    public function hide_price( $price, $product ) {
        if ( 'yes' === get_option( 'price_guard_hide_price', 'no' ) && $this->should_affect_product( $product ) ) {
            return ''; // Return empty string instead of custom text (pro feature)
        }
        return $price;
    }

    /**
     * Make product unpurchasable.
     *
     * @param bool   $is_purchasable Whether the product is purchasable.
     * @param object $product        The product object.
     * @return bool
     */
    public function make_unpurchasable( $is_purchasable, $product ) {
        if ( 'yes' === get_option( 'price_guard_hide_add_to_cart', 'no' ) && $this->should_affect_product( $product ) ) {
            return false;
        }
        return $is_purchasable;
    }

    /**
     * Add custom button on single product page.
     */
    public function add_custom_button() {
        global $product;
        if ( 'yes' === get_option( 'price_guard_hide_add_to_cart', 'no' ) && $this->should_affect_product( $product ) ) {
            $button_text = get_option( 'price_guard_custom_button_text', __( 'Request a Quote', 'price-guard' ) );
            $button_function = get_option( 'price_guard_button_function', 'normal_link' );
            $button_link = get_option( 'price_guard_custom_button_link', '#' );
            
            if ( 'normal_link' === $button_function ) {
                echo '<a href="' . esc_url( $button_link ) . '" class="button wp-element-button alt price-guard-button">' . esc_html( $button_text ) . '</a>';
            } else {
                // Popup functionality would be added here in the pro version
                echo '<a href="#" class="button alt price-guard-button">' . esc_html( $button_text ) . '</a>';
            }
        }
    }

    /**
     * Modify the Add to Cart button on archive pages and product lists.
     *
     * @param string $button The button HTML.
     * @param object $product The product object.
     * @return string
     */
    public function modify_add_to_cart_button( $button, $product ) {
        if ( 'yes' === get_option( 'price_guard_hide_add_to_cart', 'no' ) && $this->should_affect_product( $product ) ) {
            $button_text = get_option( 'price_guard_custom_button_text', __( 'Request a Quote', 'price-guard' ) );
            $button_function = get_option( 'price_guard_button_function', 'normal_link' );
            $button_link = get_option( 'price_guard_custom_button_link', $product->get_permalink() );
            
            if ( 'normal_link' === $button_function ) {
                return sprintf( '<a href="%s" data-quantity="%s" class="button product_type_%s price-guard-button" %s>%s</a>',
                    esc_url( $button_link ),
                    esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                    esc_attr( $product->get_type() ),
                    isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                    esc_html( $button_text )
                );
            } else {
                // Popup functionality would be added here in the pro version
                return sprintf( '<a href="#" data-quantity="%s" class="button product_type_%s price-guard-button" %s>%s</a>',
                    esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                    esc_attr( $product->get_type() ),
                    isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                    esc_html( $button_text )
                );
            }
        }
        
        return $button;
    }
}

new PriceGuard_Public();