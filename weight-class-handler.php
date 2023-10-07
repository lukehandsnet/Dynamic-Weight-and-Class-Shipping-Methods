<?php
namespace SCBCW;

/**
 * Class Weight_Class_Handler
 *
 * Handles the retrieval and definition of weight classes used for determining shipping options.
 */
class Weight_Class_Handler {
    
    /**
     * Retrieve defined weight classes and their respective properties from plugin settings.
     *
     * @return array An associative array with defined weight classes and their properties.
     */
    public function get_weight_classes() {
        // Retrieve weight class settings from the WordPress options table. 
        // If options don't exist, provide default values to ensure functionality.
        $options = get_option('asm_settings', array('asm_light_weight' => 5, 'asm_medium_weight' => 10, 'asm_heavy_weight' => 15));
        
        // Define and return an array of weight classes, associating each class with its max weight and delivery class.
        // 'light', 'medium', and 'heavy' represent categories of weight classes.
        // 'max_weight' defines the upper weight limit for the class.
        // 'delivery_class' specifies the associated delivery type/option for the class.
        return array(
            'light' => array('max_weight' => $options['asm_light_weight'], 'delivery_class' => 'standard'),
            'medium' => array('max_weight' => $options['asm_medium_weight'], 'delivery_class' => 'express'),
            'heavy' => array('max_weight' => $options['asm_heavy_weight'], 'delivery_class' => 'freight')
        );
    }
}
