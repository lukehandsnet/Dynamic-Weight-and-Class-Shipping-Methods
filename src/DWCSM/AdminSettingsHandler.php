<?php
namespace DWCSM;

if (!defined('ABSPATH')) exit;

class AdminSettingsHandler {

    public function __construct() {
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
        add_action('woocommerce_settings_tabs_dwsm_settings_tab', [$this, 'settings_tab']);
        add_action('woocommerce_update_options_dwsm_settings_tab', [$this, 'update_settings']);
        add_action('woocommerce_admin_field_shipping_classes_field', [$this,'display_shipping_classes_checkboxes'], 10, 1);
    }

    public function settings_section_callback() {
        echo '<p>' . __('This section is for setting up the weight limits for different shipping methods.', 'adjust-shipping-methods') . '</p>';
    }

    public function add_settings_tab($settings_tabs) {
        $settings_tabs['dwsm_settings_tab'] = __('DWCSM Settings', 'dwsm-text-domain');
        return $settings_tabs;
    }

    public function settings_tab() {
        woocommerce_admin_fields($this->get_settings());
    }

    public function update_settings() {
        woocommerce_update_options($this->get_settings());
    }

    public function get_settings() {

        $settings = array(
            'section_title' => array(
                'name'     => __('Shipping Adjustment Settings', 'DWCSM-text-domain'),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'DWCSM_settings_section_title'
            ),
        );
        
        $shipping_zones = \WC_Shipping_Zones::get_zones();

        foreach( $shipping_zones as $zone ) {
            $settings['zone_title_' . $zone['zone_id']] = array(
                'name' => sprintf(__('Zone: %s', 'DWCSM-text-domain'), $zone['zone_name']),
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_dwcsm_zone_title_' . $zone['zone_id']
            );

            foreach( $zone['shipping_methods'] as $method ) {
                $min_weight_key = 'wc_dwcsm_min_weight_' . $method->instance_id;
                $max_weight_key = 'wc_dwcsm_max_weight_' . $method->instance_id;
                $shipping_classes_key = 'wc_dwcsm_shipping_classes_' . $method->instance_id;

                $settings['min_weight_' . $method->instance_id] = array(
                    'name' => sprintf(__('Min Weight (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'number',
                    'desc' => '',
                    'id'   => 'wc_dwcsm_min_weight_' . $method->instance_id,
                    'css'  => '',
                    'default' => get_option($min_weight_key, ''),
                    'custom_attributes' => array(
                        'min'  => 0,
                        'step' => 0.1
                    )
                );
                $settings['max_weight_' . $method->instance_id] = array(
                    'name' => sprintf(__('Max Weight (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'number',
                    'desc' => '',
                    'id'   => 'wc_dwcsm_max_weight_' . $method->instance_id,
                    'css'  => '',
                    'default' => get_option($max_weight_key, ''),
                    'custom_attributes' => array(
                        'min'  => 0,
                        'step' => 0.1
                    )
                );
                $settings['shipping_classes_' . $method->instance_id] = array(
                    'name' => sprintf(__('Shipping Classes (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'shipping_classes_field',
                    'desc' => '',
                    'id'   => 'wc_dwcsm_shipping_classes_' . $method->instance_id,
                    'method_id' => $method->instance_id,
                    'default' => get_option($shipping_classes_key, array()),
                );
            }
    
            $settings['section_end_zone_' . $zone['zone_id']] = array(
                'type' => 'sectionend',
                'id' => 'wc_dwcsm_settings_section_end_zone_' . $zone['zone_id']
            );
        }
    
        $settings['section_end'] = array(
             'type' => 'sectionend',
             'id' => 'wc_dwcsm_settings_section_end'
        );
        return apply_filters('wc_dwcsm_settings', $settings);
    }

    public function display_shipping_classes_checkboxes($value) {
        // this function is in new use
        $shipping_classes = WC()->shipping->get_shipping_classes();
        
        // Retrieve previously saved settings from WP database.
        // Note: Ensure 'get_settings' function is properly retrieving your saved settings.  
        $saved_classes = get_option($value['id'], array());
    
        echo '<tr valign="top">';
        echo '<th scope="row" class="titledesc">';
        echo '<label for="' . esc_attr($value['id']) . '">' . esc_html($value['name']) . '</label>';
        echo '</th>';
        echo '<td class="forminp">';
        echo '<fieldset>';
        
        foreach($shipping_classes as $class) {
            $checked = in_array($class->term_id, $saved_classes) ? ' checked="checked"' : '';
            echo '<label for="' . esc_attr($value['id']) . '_' . esc_attr($class->term_id) . '">';
            echo '<input name="' . esc_attr($value['id']) . '[]" id="' . esc_attr($value['id']) . '_' . esc_attr($class->term_id) . '" type="checkbox" style="" value="' . esc_attr($class->term_id) . '"' . $checked . ' /> ';
            echo esc_html($class->name);
            echo '</label><br />';
        }
        
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
    }
}

