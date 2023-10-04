<?php
namespace SCBCW;

class Shipping_Method_Handler {
    public function __construct() {
        add_filter('woocommerce_package_rates', [$this, 'adjust_shipping_methods'], 10, 2);
    }

    function adjust_shipping_methods($available_shipping_methods) {
        global $woocommerce;
        $cart_weight = $woocommerce->cart->cart_contents_weight;
        
        $options = get_option('asm_settings', array('asm_light_weight' => 5, 'asm_medium_weight' => 10, 'asm_heavy_weight' => 15));
        
        $weight_classes = array(
            'light' => array('max_weight' => $options['asm_light_weight'], 'delivery_class' => 'standard'),
            'medium' => array('max_weight' => $options['asm_medium_weight'], 'delivery_class' => 'express'),
            'heavy' => array('max_weight' => $options['asm_heavy_weight'], 'delivery_class' => 'freight')
        );
        
        $selected_method = '';
        foreach ($weight_classes as $class) {
            if ($cart_weight <= $class['max_weight']) {
                $selected_method = $class['delivery_class'];
                break;
            }
        }
        
        foreach ($available_shipping_methods as $method_id => $method) {
            $matched = false;
            foreach ($weight_classes as $class_name => $class) {
                if (strpos($method->get_label(), $class_name) !== false && $class['delivery_class'] === $selected_method) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                unset($available_shipping_methods[$method_id]);
            }
        }
        
        return $available_shipping_methods;
    }
}
