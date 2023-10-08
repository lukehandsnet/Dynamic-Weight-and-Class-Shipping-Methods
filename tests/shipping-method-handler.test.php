<?php
use PHPUnit\Framework\TestCase;
use DWCSM\Shipping_Method_Handler;

class Shipping_Method_Handler_Test extends TestCase {
    /**
     * Test that adjust_shipping_methods() correctly filters out shipping methods based on cart weight and shipping classes.
     */
    public function test_adjust_shipping_methods() {
        // Create a new instance of the Shipping_Method_Handler class
        $handler = new Shipping_Method_Handler();
        
        // Create some mock data for the available shipping methods and the cart contents
        $available_shipping_methods = [
            'flat_rate:1' => (object) [
                'id' => 'flat_rate:1',
                'instance_id' => '1',
                'title' => 'Flat Rate',
            ],
            'free_shipping:1' => (object) [
                'id' => 'free_shipping:1',
                'instance_id' => '1',
                'title' => 'Free Shipping',
            ],
            'local_pickup:1' => (object) [
                'id' => 'local_pickup:1',
                'instance_id' => '1',
                'title' => 'Local Pickup',
            ],
        ];
        
        $cart_contents = [
            [
                'data' => (object) [
                    'get_shipping_class_id' => function() { return 'large'; },
                    'get_weight' => function() { return 10; },
                ],
            ],
            [
                'data' => (object) [
                    'get_shipping_class_id' => function() { return 'small'; },
                    'get_weight' => function() { return 2; },
                ],
            ],
        ];
        
        // Set up some mock settings for the shipping methods
        $settings = [
            'min_weight' => [
                '1' => 5,
            ],
            'max_weight' => [
                '1' => 10,
            ],
            'shipping_classes' => [
                'Flat Rate' => ['large'],
                'Free Shipping' => [],
                'Local Pickup' => ['small'],
            ],
        ];
        
        // Set the mock settings using the WordPress options API
        update_option('asm_plugin_settings', $settings);
        
        // Call the adjust_shipping_methods() method with the mock data
        $filtered_methods = $handler->adjust_shipping_methods($available_shipping_methods, [
            'contents' => $cart_contents,
        ]);
        
        // Assert that the filtered methods only include the expected methods
        $this->assertEquals([
            'flat_rate:1' => $available_shipping_methods['flat_rate:1'],
            'local_pickup:1' => $available_shipping_methods['local_pickup:1'],
        ], $filtered_methods);
    }
}
