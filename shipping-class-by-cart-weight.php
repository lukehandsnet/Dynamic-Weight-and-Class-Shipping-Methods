<?php
/*
Plugin Name: Shipping Class By Cart Weight
Plugin URI: https://lukehands.net/
Description: Adjusts available shipping methods based on cart weight.
Version: 1.0
Author: Luke Hands
Author URI: https://lukehands.net/
Text Domain: adjust-shipping-methods
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'admin-menu-settings.php';
require_once plugin_dir_path(__FILE__) . 'shipping-methods.php';
