<?php
declare(strict_types=1);

/*
Plugin Name: Dynamic Weight and Class Shipping Methods
Plugin URI: https://lukehands.net/
Description: Dynamically adjusts available WooCommerce shipping methods based on cart weight and shipping classes.
Version: 1.1.0
Author: Luke Hands
Author URI: https://lukehands.net/
Text Domain: dwcsm
Requires PHP: 7.4
Requires at least: 5.6
WC requires at least: 5.0
WC tested up to: 8.0
*/

namespace DWCSM;

use DWCSM\Handlers\ShippingMethodHandler;
use DWCSM\Handlers\AdminSettingsHandler;
use DWCSM\Exceptions\InitializationException;

// Ensure the script is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class for Dynamic Weight and Class Shipping Methods
 */
final class DynamicWeightAndClassShippingMethods {
    private const MINIMUM_PHP_VERSION = '7.4';
    private const MINIMUM_WP_VERSION = '5.6';
    private const MINIMUM_WC_VERSION = '5.0';

    private static ?self $instance = null;
    private ShippingMethodHandler $shippingHandler;
    private AdminSettingsHandler $adminHandler;

    /**
     * Get the singleton instance of the plugin
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     * 
     * @throws InitializationException When requirements are not met
     */
    private function __construct()
    {
        $this->checkRequirements();
        $this->initializeHandlers();
        $this->registerHooks();
    }

    /**
     * Check if all requirements are met
     * 
     * @throws InitializationException
     */
    private function checkRequirements(): void
    {
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            throw new InitializationException(
                sprintf('PHP version %s or higher is required. Current version is %s', 
                    self::MINIMUM_PHP_VERSION, 
                    PHP_VERSION
                )
            );
        }

        if (version_compare($GLOBALS['wp_version'], self::MINIMUM_WP_VERSION, '<')) {
            throw new InitializationException(
                sprintf('WordPress version %s or higher is required', self::MINIMUM_WP_VERSION)
            );
        }

        if (!class_exists('WooCommerce')) {
            throw new InitializationException('WooCommerce must be installed and activated');
        }

        if (defined('WC_VERSION') && version_compare(WC_VERSION, self::MINIMUM_WC_VERSION, '<')) {
            throw new InitializationException(
                sprintf('WooCommerce version %s or higher is required', self::MINIMUM_WC_VERSION)
            );
        }
    }

    /**
     * Initialize the handler classes
     */
    private function initializeHandlers(): void
    {
        $this->shippingHandler = new ShippingMethodHandler();
        $this->adminHandler = new AdminSettingsHandler();
    }

    /**
     * Register WordPress hooks
     */
    private function registerHooks(): void
    {
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
        add_action('admin_notices', [$this, 'displayAdminNotices']);
    }

    /**
     * Load plugin text domain for translations
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'dwcsm',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Display admin notices if any
     */
    public function displayAdminNotices(): void
    {
        // Implementation for admin notices
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
        // Prevent cloning
    }

    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}

// Initialize the plugin
try {
    DynamicWeightAndClassShippingMethods::getInstance();
} catch (InitializationException $e) {
    add_action('admin_notices', function() use ($e) {
        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html(sprintf('Dynamic Weight and Class Shipping Methods: %s', $e->getMessage()))
        );
    });
    return;
}