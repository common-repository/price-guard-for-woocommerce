<?php
/**
 * PriceGuard Settings
 *
 * @package PriceGuard
 */

defined( 'ABSPATH' ) || exit;

/**
 * PriceGuard_Settings class.
 */
class PriceGuard_Settings {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
        add_action( 'woocommerce_settings_tabs_price_guard', array( $this, 'settings_tab' ) );
        add_action( 'woocommerce_update_options_price_guard', array( $this, 'update_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'woocommerce_admin_field_price_guard_multiselect', array( $this, 'render_price_guard_multiselect' ) );
    }

    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels.
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs['price_guard'] = __( 'Price Guard', 'price-guard' );
        return $settings_tabs;
    }

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     */
    public function settings_tab() {
        echo '<div class="price-guard-settings-wrap">';
        woocommerce_admin_fields( $this->get_settings() );
        
        echo '</div>';
    }

    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     */
    public function update_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(
            array(
                'title' => __( 'Price Guard Settings', 'price-guard' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'price_guard_section_title'
            ),
            array(
                'title'   => __( 'Hide Price', 'price-guard' ),
                'desc'    => __( 'Enable to hide product prices', 'price-guard' ),
                'id'      => 'price_guard_hide_price',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'   => __( 'Hide Price Text', 'price-guard' ),
                'desc'    => __( 'Text to display instead of the price (Pro feature)', 'price-guard' ),
                'id'      => 'price_guard_hide_price_text',
                'default' => __( 'Price hidden', 'price-guard' ),
                'type'    => 'text',
                'class'   => 'price-guard-hide-price-text price-guard-pro-feature',
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                ),
            ),
            array(
                'title'   => __( 'Hide Add to Cart Button', 'price-guard' ),
                'desc'    => __( 'Enable to hide the Add to Cart button', 'price-guard' ),
                'id'      => 'price_guard_hide_add_to_cart',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'   => __( 'Button Function', 'price-guard' ),
                'desc'    => __( 'Choose the function of the custom button', 'price-guard' ),
                'id'      => 'price_guard_button_function',
                'default' => 'normal_link',
                'type'    => 'select',
                'options' => array(
                    'normal_link' => __( 'Normal Link', 'price-guard' ),
                    'popup'       => __( 'Popup (Pro feature)', 'price-guard' ),
                ),
                'class'   => 'price-guard-button-function'
            ),
            array(
                'title'   => __( 'Custom Button Text', 'price-guard' ),
                'desc'    => __( 'Text for the custom button', 'price-guard' ),
                'id'      => 'price_guard_custom_button_text',
                'default' => __( 'Request a Quote', 'price-guard' ),
                'type'    => 'text',
                'class'   => 'price-guard-custom-button-text'
            ),
            array(
                'title'   => __( 'Custom Button Link', 'price-guard' ),
                'desc'    => __( 'Link for the custom button', 'price-guard' ),
                'id'      => 'price_guard_custom_button_link',
                'default' => '',
                'type'    => 'text',
                'class'   => 'price-guard-custom-button-link'
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'price_guard_section_end'
            ),
            array(
                'title' => __( 'Apply Settings To', 'price-guard' ),
                'type'  => 'title',
                'desc'  => __( 'Choose where to apply the above settings:', 'price-guard' ),
                'id'    => 'price_guard_apply_section_title'
            ),
            array(
                'title'   => __( 'Apply Globally', 'price-guard' ),
                'desc'    => __( 'Apply settings to all products', 'price-guard' ),
                'id'      => 'price_guard_apply_globally',
                'default' => 'yes',
                'type'    => 'radio',
                'options' => array(
                    'yes' => __( 'Yes, apply to all products', 'price-guard' ),
                    'no'  => __( 'No, I want to select specific categories', 'price-guard' )
                ),
                'desc_tip' => true,
            ),
            array(
                'title'   => __( 'Select Categories', 'price-guard' ),
                'desc'    => __( 'Apply settings to specific categories (only if "Apply Globally" is set to "No")', 'price-guard' ),
                'id'      => 'price_guard_categories',
                'default' => array(),
                'type'    => 'multiselect',
                'options' => $this->get_product_categories(),
                'class'   => 'wc-enhanced-select'
            ),
            array(
                'title'   => __( 'Hide for Countries', 'price-guard' ),
                'desc'    => __( 'Select countries to apply settings (Pro feature)', 'price-guard' ),
                'id'      => 'price_guard_countries',
                'default' => array('US', 'CA', 'GB'),
                'type'    => 'price_guard_multiselect',
                'options' => WC()->countries->get_countries(),
                'class'   => 'wc-enhanced-select price-guard-pro-select',
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                    'data-placeholder' => __( 'Select countries (Pro feature)', 'price-guard' ),
                ),
            ),
            array(
                'title'   => __( 'Hide for User Roles', 'price-guard' ),
                'desc'    => __( 'Select user roles to apply settings (Pro feature)', 'price-guard' ),
                'id'      => 'price_guard_user_roles',
                'default' => array('customer', 'subscriber'),
                'type'    => 'price_guard_multiselect',
                'options' => $this->get_user_roles(),
                'class'   => 'wc-enhanced-select price-guard-pro-select',
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                    'data-placeholder' => __( 'Select user roles (Pro feature)', 'price-guard' ),
                ),
            ),
            array(
                'title'   => __( 'Hide on Single Product', 'price-guard' ),
                'desc'    => __( 'Apply settings to individual products (Pro feature)', 'price-guard' ),
                'id'      => 'price_guard_single_product',
                'default' => 'no',
                'type'    => 'checkbox',
                'class'   => 'price-guard-pro-feature',
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'price_guard_apply_section_end'
            )
        );
    
        return apply_filters( 'woocommerce_price_guard_settings', $settings );
    }

    /**
     * Render custom multiselect field for pro features.
     */
    public function render_price_guard_multiselect( $value ) {
        $option_value = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
        ?><tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
            </th>
            <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                <select
                    name="<?php echo esc_attr( $value['id'] ); ?>[]"
                    id="<?php echo esc_attr( $value['id'] ); ?>"
                    class="<?php echo esc_attr( $value['class'] ); ?>"
                    multiple="multiple"
                    <?php echo esc_attr(isset( $value['custom_attributes'] ) ? wc_implode_html_attributes( $value['custom_attributes'] ) : ''); ?>
                >
                    <?php
                    foreach ( $value['options'] as $key => $val ) {
                        echo '<option value="' . esc_attr( $key ) . '"'
                            . selected( in_array( $key, $option_value ), true, false ) . '>'
                            . esc_html( $val ) . '</option>';
                    }
                    ?>
                </select>
                <div class="forminp-price-desc">
                 <?php echo esc_html($value['desc']); ?>
                </div>
            </td>
        </tr><?php
    }

    /**
     * Get all product categories.
     *
     * @return array
     */
    private function get_product_categories() {
        $categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ) );

        $category_options = array();
        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $category_options[ $category->term_id ] = $category->name;
            }
        }

        return $category_options;
    }

    /**
     * Get all user roles.
     *
     * @return array
     */
    private function get_user_roles() {
        global $wp_roles;
        $roles = $wp_roles->get_names();
        return array_merge( array( 'guest' => __( 'Guest', 'price-guard' ) ), $roles );
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'woocommerce_page_wc-settings' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'price-guard-admin', PRICE_GUARD_PLUGIN_URL . 'assets/css/admin.css', array(), PRICE_GUARD_VERSION );
        wp_enqueue_script( 'price-guard-admin', PRICE_GUARD_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), PRICE_GUARD_VERSION, true );
    }
}

new PriceGuard_Settings();