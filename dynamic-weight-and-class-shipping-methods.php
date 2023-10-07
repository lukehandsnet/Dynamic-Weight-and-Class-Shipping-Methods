<?php
/*
Plugin Name: Dynamic Weight and Class Shipping Methods
Plugin URI: https://lukehands.net/
Description: Dynamically adjusts available WooCommerce shipping methods based on cart weight and shipping classes.
Version: 1.0
Author: Luke Hands
Author URI: https://lukehands.net/
Text Domain: dwcsm
*/

namespace DWCSM;

// Ensure the script is not accessed directly for enhanced security
if (!defined('ABSPATH')) exit;

// Include required handler classes to manage various aspects of the plugin functionality
require_once('shipping-method-handler.php');  // Handle adjustments to available shipping methods
require_once('admin-settings-handler.php');   // Manage admin settings page and settings initialization


/**
 * Class DWCSM_Plugin
 *
 * Main class for the Dynamic Weight and Class Shipping Methods plugin.
 * Initiates the handler classes upon instantiation.
 */
class DWCSM_Plugin {
    /**
     * DWCSM_Plugin constructor.
     *
     * Initializes instances of the handler classes to set up the plugin functionality.
     */
    public function __construct() {
        // Instantiate the handler classes to manage different aspects of shipping method adjustments
        new Shipping_Method_Handler();  // Manages adjustments and conditions for available shipping methods
        new Admin_Settings_Handler();   // Manages the admin settings page and related functionality
    }
}

// Create an instance of the main plugin class, initializing the handler classes and setting up the plugin functionality
new DWCSM_Plugin();
