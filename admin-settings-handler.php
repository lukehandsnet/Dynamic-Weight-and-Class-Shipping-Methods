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

        $this->add_settings_field('asm_light_weight', __('Light Weight Limit', 'adjust-shipping-methods'), 'render_light_weight_setting');
        $this->add_settings_field('asm_medium_weight', __('Medium Weight Limit', 'adjust-shipping-methods'), 'render_medium_weight_setting');
        $this->add_settings_field('asm_heavy_weight', __('Heavy Weight Limit', 'adjust-shipping-methods'), 'render_heavy_weight_setting');
    }

    private function add_settings_field($id, $title, $callback) {
        add_settings_field(
            $id,
            $title,
            [$this, $callback],
            'pluginPage',
            'asm_pluginPage_section'
        );
    }

    public function options_page() {
        ?>
        <form action='options.php' method='post'>
            <h2>Adjust Shipping Methods</h2>
            <?php
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            submit_button();

            // Display shipping methods and classes.
            $this->display_shipping_methods();
            $this->display_shipping_classes();
            ?>
        </form>
        <?php
    }

    public function settings_section_callback() {
        echo '<p>' . __('This section is for setting up the weight limits for different shipping methods.', 'adjust-shipping-methods') . '</p>';
    }

    public function render_light_weight_setting() {
        $this->render_input('asm_light_weight', 5);
    }

    public function render_medium_weight_setting() {
        $this->render_input('asm_medium_weight', 10);
    }

    public function render_heavy_weight_setting() {
        $this->render_input('asm_heavy_weight', 15);
    }

    private function render_input($name, $default = '') {
        $options = get_option('asm_settings', array($name => $default));
        ?>
        <input type='text' name='asm_settings[<?php echo $name; ?>]' value='<?php echo $options[$name]; ?>'>
        <?php
    }

    public function display_shipping_methods() {
        $shipping_methods = \WC()->shipping->get_shipping_methods();
        echo '<h3>Available Shipping Methods</h3>';
        echo '<ul>';
        foreach ($shipping_methods as $method) {
            echo '<li>' . esc_html($method->get_title()) . '</li>';
        }
        echo '</ul>';
    }

    public function display_shipping_classes() {
        $shipping_classes = \WC()->shipping->get_shipping_classes();
        echo '<h3>Available Shipping Classes</h3>';
        echo '<ul>';
        foreach ($shipping_classes as $class) {
            echo '<li>' . esc_html($class->name) . '</li>';
        }
        echo '</ul>';
    }
}


