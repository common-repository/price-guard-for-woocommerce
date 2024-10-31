<?php
/**
 * Main PriceGuard class
 *
 * @package PriceGuard
 */

defined( 'ABSPATH' ) || exit;

/**
 * PriceGuard class.
 */
final class PriceGuard {

    /**
     * Single instance of the PriceGuard class
     *
     * @var PriceGuard
     */
    protected static $instance = null;

    /**
     * Main PriceGuard Instance.
     *
     * Ensures only one instance of PriceGuard is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return PriceGuard - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * PriceGuard Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once PRICE_GUARD_PLUGIN_DIR . 'includes/class-price-guard-settings.php';
            include_once PRICE_GUARD_PLUGIN_DIR . 'includes/class-price-guard-public.php';
        }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * Hook in methods on plugins loaded.
     */
    public function on_plugins_loaded() {
        do_action( 'price_guard_loaded' );
    }

    /**
     * Init PriceGuard when WordPress Initialises.
     */
    public function init() {
        // Before init action.
        do_action( 'before_price_guard_init' );

        // Set up localisation.
        $this->load_plugin_textdomain();

        // Init action.
        do_action( 'price_guard_init' );
    }

    /**
     * Load Localisation files.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'price-guard', false, plugin_basename( dirname( PRICE_GUARD_PLUGIN_DIR ) ) . '/languages' );
    }
}