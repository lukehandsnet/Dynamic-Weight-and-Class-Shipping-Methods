<?php
/*
Plugin Name: Shipping Class-By-Cart-Weight
Plugin URI: https://lukehands.net/
Description: Adjusts available shipping methods based on cart weight.
Version: 1.0
Author: Luke Hands
Author URI: https://lukehands.net/
Text Domain: adjust-shipping-methods
*/

namespace SCBCW;

// Ensure script is not accessed directly for security
if (!defined('ABSPATH')) exit;

// Include required handler classes to manage various aspects of the plugin functionality
require_once('shipping-method-handler.php');  // Handle adjustments to available shipping methods
require_once('admin-settings-handler.php');   // Manage admin settings page and settings initialization
require_once('weight-class-handler.php');     // Handle the definitions and retrieval of weight classes

/**
 * Class SCBCW_Plugin
 *
 * Main class for the Shipping Class-By-Cart-Weight plugin. Initiates the handler classes upon creation.
 */
class SCBCW_Plugin {
    /**
     * SCBCW_Plugin constructor.
     *
     * Initializes instances of the handler classes to set up the plugin functionality.
     */
    public function __construct() {
        // Instantiate the handler classes to manage different aspects of shipping method adjustments
        new Shipping_Method_Handler();  // Manages adjustments and conditions for available shipping methods
        new Admin_Settings_Handler();   // Manages the admin settings page and related functionality
        new Weight_Class_Handler();     // Handles retrieving and defining weight classes
    }
}

// Create an instance of the main plugin class, initializing the handler classes and setting up the plugin functionality
new SCBCW_Plugin();
