<?php
namespace DWCSM;

/**
 * Class Shipping_Method_Handler
 * 
 * Handles the adjustment of available shipping methods based on various conditions.
 */
class ShippingMethodHandler {
    private $timeBasedWeightHandler;

    /**
     * Shipping_Method_Handler constructor.
     *
     * Hooks into WooCommerce to adjust shipping methods based on conditions.
     */
    public function __construct() {
        // Adding a filter to adjust available shipping methods based on certain conditions
        add_filter('woocommerce_package_rates', [$this, 'adjust_shipping_methods'], 10, 2);
        $this->timeBasedWeightHandler = new Handlers\TimeBasedWeightHandler();
    }

    /**
     * Adjust available shipping methods based on cart weight and shipping classes.
     *
     * @param array $available_shipping_methods List of available shipping methods.
     * @param array $package The package array containing cart items, destination, etc.
     * @return array Modified list of available shipping methods.
     */
    function adjust_shipping_methods($available_shipping_methods, $package) {
        global $woocommerce;
        // Get the total weight of items in the cart
        $cart_weight = $woocommerce->cart->cart_contents_weight;
        error_log('Cart weight: ' . $cart_weight);
        
        // Retrieve saved settings from the WordPress options table
        //$saved_settings = get_option('asm_plugin_settings', []);
        
        // Loop through each available shipping method
        foreach ($available_shipping_methods as $method_id => $method) {
            // Retrieve minimum and maximum weight and allowed shipping classes from saved settings
            // Also, ensure that if there's no setting, a default value is used
            $min_weight_key = 'wc_dwcsm_min_weight_' . $method->instance_id;
            $max_weight_key = 'wc_dwcsm_max_weight_' . $method->instance_id;
            $shipping_classes_key = 'wc_dwcsm_shipping_classes_' . $method->instance_id;
            
            $min_weight = get_option($min_weight_key, null);
            $max_weight = get_option($max_weight_key, null);
            $allowed_classes = get_option($shipping_classes_key, []);

            // Get time-based weight limits
            $time_limits = $this->timeBasedWeightHandler->getWeightLimits($method_id);
            
            // If time-based rules exist, they override the default weight limits
            if ($time_limits['min_weight'] > 0) {
                $min_weight = $time_limits['min_weight'];
            }
            if ($time_limits['max_weight'] < PHP_FLOAT_MAX) {
                $max_weight = $time_limits['max_weight'];
            }

            // Check if the cart weight is valid for the current shipping method
            $is_weight_valid = 
                (is_null($min_weight) || $cart_weight >= $min_weight) && 
                (is_null($max_weight) || $cart_weight <= $max_weight);
            // Check if the cart's shipping classes are allowed for the current shipping method
            $is_class_valid = false;
            $cart_product_shipping_classes = array();
            foreach ($package['contents'] as $cart_item) {
                // add each shipping id to the array
                $cart_product_shipping_classes[] = $cart_item['data']->get_shipping_class_id();
            }
            $is_class_valid = empty($allowed_classes) || 
                              empty(array_diff($cart_product_shipping_classes, $allowed_classes));
            // If either the weight or class condition is not met, remove the shipping method from available methods
            if (!$is_weight_valid || !$is_class_valid) {
                unset($available_shipping_methods[$method_id]);
            }
        }
        
        // Return the potentially modified array of shipping methods
        return $available_shipping_methods;
    }
}
