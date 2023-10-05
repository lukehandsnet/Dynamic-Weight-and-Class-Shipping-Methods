<?php
namespace SCBCW;

class Shipping_Method_Handler {
    public function __construct() {
        add_filter('woocommerce_package_rates', [$this, 'adjust_shipping_methods'], 10, 2);
    }

    function adjust_shipping_methods($available_shipping_methods, $package) {
        global $woocommerce;
        $cart_weight = $woocommerce->cart->cart_contents_weight;
        
        // Retrieve the saved settings
        $saved_settings = get_option('your_option_name', []);
        
        foreach ($available_shipping_methods as $method_id => $method) {
            // Use $method->title if you used titles as keys, or adapt accordingly
            $min_weight = $saved_settings['min_weight'][$method->instance_id] ?? null;
            $max_weight = $saved_settings['max_weight'][$method->instance_id] ?? null;
            $allowed_classes = $saved_settings['shipping_classes'][$method->title] ?? [];
            
            // Check weight conditions
            $is_weight_valid = 
                (is_null($min_weight) || $cart_weight >= $min_weight) && 
                (is_null($max_weight) || $cart_weight <= $max_weight);
            
            // Check if the cart’s shipping class is within the allowed classes for this method
            $is_class_valid = empty($allowed_classes) || 
                              in_array($package['contents'][0]['data']->get_shipping_class_id(), $allowed_classes);
            
            // If either condition is not met, unset the shipping method
            if (!$is_weight_valid || !$is_class_valid) {
                unset($available_shipping_methods[$method_id]);
            }
        }
        
        return $available_shipping_methods;
    }
}
