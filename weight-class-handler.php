<?php
namespace SCBCW;

class Weight_Class_Handler {
    public function get_weight_classes() {
        $options = get_option('asm_settings', array('asm_light_weight' => 5, 'asm_medium_weight' => 10, 'asm_heavy_weight' => 15));
        return array(
            'light' => array('max_weight' => $options['asm_light_weight'], 'delivery_class' => 'standard'),
            'medium' => array('max_weight' => $options['asm_medium_weight'], 'delivery_class' => 'express'),
            'heavy' => array('max_weight' => $options['asm_heavy_weight'], 'delivery_class' => 'freight')
        );
    }
}
