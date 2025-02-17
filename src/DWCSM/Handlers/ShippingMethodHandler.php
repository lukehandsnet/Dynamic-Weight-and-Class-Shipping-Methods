<?php
declare(strict_types=1);

namespace DWCSM\Handlers;

use WC_Cart;
use WC_Shipping_Method;
use WC_Shipping_Rate;

/**
 * Handles the shipping method logic and calculations
 */
class ShippingMethodHandler {
    private const OPTION_PREFIX = 'dwcsm_';
    
    /**
     * Initialize the handler
     */
    public function __construct() {
        add_filter('woocommerce_package_rates', [$this, 'filterShippingMethods'], 10, 2);
    }

    /**
     * Filter shipping methods based on cart weight and shipping classes
     *
     * @param array $rates Array of shipping rates
     * @param array $package The shipping package
     * @return array Filtered shipping rates
     */
    public function filterShippingMethods(array $rates, array $package): array
    {
        if (empty($rates)) {
            return $rates;
        }

        $cart = WC()->cart;
        if (!$cart instanceof WC_Cart) {
            return $rates;
        }

        $cartWeight = $this->getCartWeight($cart);
        $cartShippingClasses = $this->getCartShippingClasses($cart);

        return array_filter($rates, function($rate) use ($cartWeight, $cartShippingClasses) {
            return $this->isMethodAvailable($rate, $cartWeight, $cartShippingClasses);
        });
    }

    /**
     * Get the total cart weight
     *
     * @param WC_Cart $cart
     * @return float
     */
    private function getCartWeight(WC_Cart $cart): float
    {
        $weight = 0.0;
        foreach ($cart->get_cart() as $item) {
            $product = $item['data'];
            if ($product && $product->needs_shipping()) {
                $weight += (float)$product->get_weight() * $item['quantity'];
            }
        }
        return $weight;
    }

    /**
     * Get unique shipping classes in cart
     *
     * @param WC_Cart $cart
     * @return array
     */
    private function getCartShippingClasses(WC_Cart $cart): array
    {
        $classes = [];
        foreach ($cart->get_cart() as $item) {
            $product = $item['data'];
            if ($product && $product->needs_shipping()) {
                $classes[] = $product->get_shipping_class_id();
            }
        }
        return array_unique(array_filter($classes));
    }

    /**
     * Check if a shipping method is available based on weight and classes
     *
     * @param WC_Shipping_Rate $rate
     * @param float $cartWeight
     * @param array $cartClasses
     * @return bool
     */
    private function isMethodAvailable(WC_Shipping_Rate $rate, float $cartWeight, array $cartClasses): bool
    {
        $methodId = $rate->get_method_id();
        
        // Check weight constraints
        $minWeight = (float)get_option(self::OPTION_PREFIX . $methodId . '_min_weight', 0);
        $maxWeight = (float)get_option(self::OPTION_PREFIX . $methodId . '_max_weight', PHP_FLOAT_MAX);
        
        if ($cartWeight < $minWeight || $cartWeight > $maxWeight) {
            return false;
        }

        // Check shipping class constraints
        $allowedClasses = $this->getAllowedShippingClasses($methodId);
        if (!empty($allowedClasses) && !empty($cartClasses)) {
            $hasAllowedClass = false;
            foreach ($cartClasses as $classId) {
                if (in_array($classId, $allowedClasses, true)) {
                    $hasAllowedClass = true;
                    break;
                }
            }
            if (!$hasAllowedClass) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get allowed shipping classes for a method
     *
     * @param string $methodId
     * @return array
     */
    private function getAllowedShippingClasses(string $methodId): array
    {
        $classes = get_option(self::OPTION_PREFIX . $methodId . '_allowed_classes', []);
        return is_array($classes) ? $classes : [];
    }
}