<?php
namespace SCBCW;

if (!defined('ABSPATH')) exit;

class Admin_Settings_Handler {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function add_admin_menu() {
        add_options_page('Adjust Shipping Methods', 'Adjust Shipping Methods', 'manage_options', 'adjust_shipping_methods', [$this, 'options_page']);
    }

    public function settings_init() {
        register_setting('pluginPage', 'asm_settings');

        add_settings_section(
            'asm_pluginPage_section',
            __('Adjust the weight classes and corresponding delivery classes.', 'adjust-shipping-methods'),
            [$this, 'settings_section_callback'],
            'pluginPage'
        );
    }


    public function options_page() {
        // Check if form is submitted and nonce is valid
        if (isset($_POST['your_settings_nonce_name']) && wp_verify_nonce($_POST['your_settings_nonce_name'], 'save_your_settings')) {
            // Process and save settings
            $this->save_settings($_POST);
        }
        ?>
        <form action='' method='post'>
            <h2>Adjust Shipping Methods</h2>
            <?php
            // Nonce field
            wp_nonce_field('save_your_settings', 'your_settings_nonce_name');
            
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            
            // Display shipping methods and classes directly.
            $this->display_shipping_methods();
            
            submit_button();
            ?>
        </form>
        <?php
    }
    
    

    public function settings_section_callback() {
        echo '<p>' . __('This section is for setting up the weight limits for different shipping methods.', 'adjust-shipping-methods') . '</p>';
    }

    

    public function display_shipping_methods() {
        $shipping_zones = \WC_Shipping_Zones::get_zones();
        
        echo '<h3>Available Shipping Methods</h3>';
        
        foreach( $shipping_zones as $zone ) {
            echo '<div class="shipping-zone">';
            echo '<strong>' . esc_html($zone['zone_name']) . '</strong>';
            echo '<ul>';
            
            foreach( $zone['shipping_methods'] as $method ) {
                echo '<li>';
                echo '<div class="shipping-method">';
                echo '<strong>' . esc_html($method->title) . '</strong> - ';
                echo 'Min Weight: <input type="text" name="min_weight[' . esc_attr($method->id) . ']" value="" /> ';
                echo 'Max Weight: <input type="text" name="max_weight[' . esc_attr($method->id) . ']" value="" />';
                echo '</div>';
                
                // Display available classes with checkboxes
                $this->display_classes_with_checkboxes($method->id);
                
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    public function display_classes_with_checkboxes($method_id) {
        $shipping_classes = \WC()->shipping->get_shipping_classes();
        
        echo '<div class="shipping-classes">';
        echo '<strong>Available Classes:</strong> ';
        echo '<ul>';
        
        foreach ($shipping_classes as $class) {
            echo '<li>';
            echo '<input type="checkbox" id="class_' . esc_attr($class->term_id) . '_method_' . esc_attr($method_id) . '" name="shipping_classes[' . esc_attr($method_id) . '][]" value="' . esc_attr($class->term_id) . '" />';
            echo '<label for="class_' . esc_attr($class->term_id) . '_method_' . esc_attr($method_id) . '">' . esc_html($class->name) . '</label>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    private function save_settings($data) {
        // Validate and sanitize $data
        // Assume $data is an associative array with keys 'min_weight' and 'max_weight' for each shipping method and zone
        
        $sanitized_data = [];
    
        foreach ($data as $zone => $methods) {
            if (!is_array($methods)) continue; // Ensure methods is an array
    
            foreach ($methods as $method => $weights) {
                // Example validation: Ensure weights are numeric
                if (!is_numeric($weights['min_weight']) || !is_numeric($weights['max_weight'])) {
                    // Handle validation error, e.g., show an admin error message, log, etc.
                    add_settings_error('your_settings', 'invalid_weight', "Invalid weights for $method in $zone", 'error');
                    return; // Stop processing
                }
                
                // Example sanitization: Ensure weights are float values
                $sanitized_data[$zone][$method]['min_weight'] = floatval($weights['min_weight']);
                $sanitized_data[$zone][$method]['max_weight'] = floatval($weights['max_weight']);
            }
        }
    
        // Save settings using update_option
        update_option('your_option_name', $sanitized_data);
    }
    
}
