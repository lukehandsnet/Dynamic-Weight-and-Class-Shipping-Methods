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

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Autoload or require the class files
require_once('shipping-method-handler.php');
require_once('admin-settings-handler.php');
require_once('weight-class-handler.php');

class SCBCW_Plugin {
    public function __construct() {
        new Shipping_Method_Handler();
        new Admin_Settings_Handler();
        new Weight_Class_Handler();
    }
}

new SCBCW_Plugin();