<?php
namespace DWCSM;
// Ensure direct access is blocked for security
if (!defined('ABSPATH')) exit;

/**
 * Class Admin_Settings_Handler
 *
 * Handles the administration settings page functionality.
 */
class AdminSettingsHandler {
    
    /**
     * Admin_Settings_Handler constructor.
     *
     * Hooks into WordPress to add actions.
     */
    public function __construct() {
        // Hook to add a settings page to the WordPress admin menu
        //add_action('admin_menu', [$this, 'add_admin_menu']);
        // Hook to initialize settings on the admin page
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
        add_action('woocommerce_settings_tabs_dwsm_settings_tab', [$this, 'settings_tab']);
        add_action('woocommerce_update_options_dwsm_settings_tab', [$this, 'update_settings']);
        add_action('woocommerce_admin_field_shipping_classes_field', [$this,'display_shipping_classes_checkboxes'], 10, 1);

    }

    

    /**
     * Callback function for settings section description.
     */
    public function settings_section_callback() {
        echo '<p>' . __('This section is for setting up the weight limits for different shipping methods.', 'adjust-shipping-methods') . '</p>';
    }

    /**
     * Displays available shipping methods and corresponding settings fields on the settings page.
     */
    public function display_shipping_methods() {
        // Retrieve shipping zones from WooCommerce
        $shipping_zones = \WC_Shipping_Zones::get_zones();
        // Retrieve previously saved settings from WP database
        $saved_settings = get_option('asm_plugin_settings', []);
        
        echo '<h3>Available Shipping Methods</h3>';
        
        // Iterate through each shipping zone
        foreach( $shipping_zones as $zone ) {
            echo '<div class="shipping-zone">';
            echo '<strong>' . esc_html($zone['zone_name']) . '</strong></br>';
            echo '<ul>';
            
            // Iterate through each shipping method in the current zone
            foreach( $zone['shipping_methods'] as $method ) {
                // Retrieve previously saved min and max weights
                $saved_min_weight = $saved_settings['min_weight'][$method->instance_id] ?? '';
                $saved_max_weight = $saved_settings['max_weight'][$method->instance_id] ?? '';

                echo '<li>';
                echo '<div class="shipping-method">';
                // Display shipping method title and input fields for min and max weights
                echo '<strong>' . esc_html($method->title) . '</strong> - ';
                echo 'Min Weight: <input type="text" name="min_weight[' . esc_attr($method->instance_id) . ']" value="' . esc_attr($saved_min_weight) . '" /> ';
                echo 'Max Weight: <input type="text" name="max_weight[' . esc_attr($method->instance_id) . ']" value="' . esc_attr($saved_max_weight) . '" />';
                echo '</div>';
                
                // Display available shipping classes with checkboxes
                $this->display_classes_with_checkboxes($method->title);
                
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    /**
     * Displays available shipping classes with checkboxes in the shipping method section.
     *
     * @param string $method_id ID of the shipping method.
     */
    public function display_classes_with_checkboxes($method_id) {
        // Retrieve shipping classes from WooCommerce
        $shipping_classes = \WC()->shipping->get_shipping_classes();
        // Retrieve previously saved settings
        $saved_settings = get_settings();
        
        echo '<div class="shipping-classes">';
        echo '<strong>Available Classes:</strong> ';
        echo '<ul>';
        
        // Iterate through each shipping class
        foreach ($shipping_classes as $class) {
            // Determine if the checkbox should be checked based on saved settings
            $is_checked = in_array($class->term_id, $saved_settings['shipping_classes'][$method_id] ?? []) ? 'checked' : '';
            
            echo '<li>';
            // Display checkbox and label for each shipping class
            echo '<input type="checkbox" id="class_' . esc_attr($class->term_id) . '_method_' . esc_attr($method_id) . '" name="shipping_classes[' . esc_attr($method_id) . '][]" value="' . esc_attr($class->term_id) . '" ' . $is_checked . ' />';
            echo '<label for="class_' . esc_attr($class->term_id) . '_method_' . esc_attr($method_id) . '">' . esc_html($class->name) . '</label>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    public function add_settings_tab($settings_tabs) {
        $settings_tabs['dwsm_settings_tab'] = __('DWCSM Settings', 'dwsm-text-domain');
        return $settings_tabs;
    }

    /**
     * Uses the WooCommerce admin fields API to output settings via the $settings array.
     */
    public function settings_tab() {
        woocommerce_admin_fields($this->get_settings());
    }

    /**
     * Use the WooCommerce options API to save settings via the $settings array.
     */
    public function update_settings() {
        
        woocommerce_update_options($this->get_settings());
    }

    /**
     * Constructs an array of settings to be displayed on the settings tab, utilizing WooCommerce's settings API.
     */
    public function get_settings() {
        $settings = array(
            'section_title' => array(
                'name'     => __('Shipping Adjustment Settings', 'DWCSM-text-domain'),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'DWCSM_settings_section_title'
            ),
        );
        
        // Retrieve shipping zones from WooCommerce
        $shipping_zones = \WC_Shipping_Zones::get_zones();
        // Retrieve previously saved settings from WP database
    
        // Iterate through each shipping zone
        foreach( $shipping_zones as $zone ) {
            $settings['zone_title_' . $zone['zone_id']] = array(
                'name' => sprintf(__('Zone: %s', 'DWCSM-text-domain'), $zone['zone_name']),
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_my_custom_zone_title_' . $zone['zone_id']
            );
            
            // Iterate through each shipping method in the current zone
            foreach( $zone['shipping_methods'] as $method ) {
                $settings['min_weight_' . $method->instance_id] = array(
                    'name' => sprintf(__('Min Weight (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'number',
                    'desc' => '',
                    'id'   => 'wc_my_custom_min_weight_' . $method->instance_id,
                    'css'  => '',
                    'default' => $saved_settings['min_weight'][$method->instance_id] ?? '',
                    'custom_attributes' => array(
                        'min'  => 0,
                        'step' => 0.1
                    )
                );
                $settings['max_weight_' . $method->instance_id] = array(
                    'name' => sprintf(__('Max Weight (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'number',
                    'desc' => '',
                    'id'   => 'wc_my_custom_max_weight_' . $method->instance_id,
                    'css'  => '',
                    'default' => $saved_settings['max_weight'][$method->instance_id] ?? '',
                    'custom_attributes' => array(
                        'min'  => 0,
                        'step' => 0.1
                    )
                );
                $settings['shipping_classes_' . $method->instance_id] = array(
                    'name' => sprintf(__('Shipping Classes (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'shipping_classes_field',
                    'desc' => '',
                    'id'   => 'wc_my_custom_shipping_classes_' . $method->instance_id,
                    'method_id' => $method->instance_id,
                    'default' => $saved_settings['shipping_classes'][$method->instance_id] ?? array(),
                );
            }
    
            $settings['section_end_zone_' . $zone['zone_id']] = array(
                'type' => 'sectionend',
                'id' => 'wc_my_custom_settings_section_end_zone_' . $zone['zone_id']
            );
        }
    
        $settings['section_end'] = array(
             'type' => 'sectionend',
             'id' => 'wc_my_custom_settings_section_end'
        );
        error_log(print_r($settings, true));
        return apply_filters('wc_my_custom_settings', $settings);
    }


    public function display_shipping_classes_checkboxes($value) {
        // this function is in new use
        $shipping_classes = WC()->shipping->get_shipping_classes();
        
        // Retrieve previously saved settings from WP database.
        // Note: Ensure 'get_settings' function is properly retrieving your saved settings.
        $saved_settings = $this->get_settings();  
        
        $saved_classes = $saved_settings['shipping_classes'][$value['method_id']] ?? array();
    
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

