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
        add_action('admin_menu', [$this, 'add_admin_menu']);
        // Hook to initialize settings on the admin page
        add_action('admin_init', [$this, 'settings_init']);
    }

    /**
     * Adds the settings page to the WordPress admin menu.
     */
    public function add_admin_menu() {
        // Add a new settings page under the "Settings" tab in WP Admin
        add_options_page('Adjust Shipping Methods', 'Adjust Shipping Methods', 'manage_options', 'adjust_shipping_methods', [$this, 'options_page']);
    }

    /**
     * Initializes settings by registering settings and adding settings section.
     */
    public function settings_init() {
        // Register the setting so WordPress understands which settings to store
        register_setting('pluginPage', 'asm_settings');

        // Add a new settings section within the settings page
        add_settings_section(
            'asm_pluginPage_section',
            __('Adjust the weight classes and corresponding delivery classes.', 'adjust-shipping-methods'),
            [$this, 'settings_section_callback'],
            'pluginPage'
        );
    }

    /**
     * Renders the options/settings page and handles the form submission.
     */
    public function options_page() {
        // Check for POST request and verify nonce for security
        if (isset($_POST['asm_settings_nonce']) && wp_verify_nonce($_POST['asm_settings_nonce'], 'save_your_settings')) {
            // Save settings on form submission
            $this->save_settings($_POST);
        }
        ?>
        <!-- HTML for the form on the settings page -->
        <form action='' method='post'>
            <h2>Adjust Shipping Methods</h2>
            <?php
            // Adds nonce to form for security
            wp_nonce_field('save_your_settings', 'asm_settings_nonce');
            
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            
            // Displays available shipping methods on the settings page
            $this->display_shipping_methods();
            
            // Add a submit button to the form
            submit_button();
            ?>
        </form>
        <?php
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
        $saved_settings = get_option('asm_plugin_settings', []);
        
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

    /**
     * Saves settings to the WordPress database.
     *
     * @param array $data The data from the form submission to save.
     */
    private function save_settings($data) {
        // Save settings using WordPress option API
        update_option('asm_plugin_settings', $data);
        // Log data for debugging purposes
        error_log(print_r($data, true));
    }
}

/**
 * Retrieves the title of a shipping method given its method ID.
 *
 * @param string $method_rate_id The method ID of the shipping method.
 * @return string|bool The title of the shipping method or false if the method ID is empty.
 */
function get_title_shipping_method_from_method_id( $method_rate_id = '' ){
    if( ! empty( $method_rate_id ) ){
        $method_key_id = str_replace( ':', '_', $method_rate_id ); // Formatting method ID
        $option_name = 'woocommerce_'.$method_key_id.'_settings'; // Constructing option name for retrieval
        return get_option( $option_name, true )['title']; // Retrieving the title of the shipping method
    } else {
        return false;
    }
}
