<?php
namespace DWCSM;

if (!defined('ABSPATH')) exit;

/**
 * Class AdminSettingsHandler handles the admin settings for the Dynamic Weight and Class Shipping Methods plugin.
 */
class AdminSettingsHandler {

    private $timeBasedWeightHandler;

    public function __construct() {
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
        add_action('woocommerce_settings_tabs_dwsm_settings_tab', [$this, 'settings_tab']);
        add_action('woocommerce_update_options_dwsm_settings_tab', [$this, 'update_settings']);
        add_action('woocommerce_admin_field_shipping_classes_field', [$this,'display_shipping_classes_checkboxes'], 10, 1);
        add_action('woocommerce_admin_field_time_rules_field', [$this, 'display_time_rules_field'], 10, 1);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        
        $this->timeBasedWeightHandler = new Handlers\TimeBasedWeightHandler();
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    }

    /**
     * Callback function for the settings section.
     */
    public function settings_section_callback() {
        echo '<p>' . __('This section is for setting up the weight limits for different shipping methods.', 'adjust-shipping-methods') . '</p>';
    }

    /**
     * Adds a new settings tab to the WooCommerce settings page.
     *
     * @param array $settings_tabs An array of existing settings tabs.
     * @return array An updated array of settings tabs.
     */
    public function add_settings_tab($settings_tabs) {
        $settings_tabs['dwsm_settings_tab'] = __('DWCSM Settings', 'dwsm-text-domain');
        return $settings_tabs;
    }

    /**
     * Displays the settings tab in the admin panel.
     *
     * @return void
     */
    public function settings_tab() {
        woocommerce_admin_fields($this->get_settings());
    }

    /**
     * Updates the settings for the Dynamic Weight and Class Shipping Methods plugin.
     */
    public function update_settings() {
        woocommerce_update_options($this->get_settings());
        
        // Handle time-based rules saving
        if (isset($_POST['time_rules']) && is_array($_POST['time_rules'])) {
            foreach ($_POST['time_rules'] as $method_id => $rules) {
                foreach ($rules as $rule) {
                    if (empty($rule['days'])) {
                        continue;
                    }
                    
                    $sanitized_rule = array(
                        'days' => array_map('sanitize_text_field', $rule['days']),
                        'start_hour' => intval($rule['start_hour']),
                        'end_hour' => intval($rule['end_hour']),
                        'min_weight' => floatval($rule['min_weight']),
                        'max_weight' => floatval($rule['max_weight'])
                    );
                    
                    $this->timeBasedWeightHandler->saveRule($method_id, $sanitized_rule);
                }
            }
        }
    }

    /**
     * Retrieves the settings for the Dynamic Weight and Class Shipping Methods plugin.
     *
     * @return array An array of settings for the plugin.
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

                $settings['time_rules_' . $method->instance_id] = array(
                    'name' => sprintf(__('Time-Based Weight Rules (%s)', 'DWCSM-text-domain'), $method->title),
                    'type' => 'time_rules_field',
                    'desc' => '',
                    'id'   => 'wc_dwcsm_time_rules_' . $method->instance_id,
                    'method_id' => $method->instance_id,
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

    /**
     * Displays the shipping classes checkboxes.
     *
     * @param mixed $value The value to display.
     */
    public function display_time_rules_field($value) {
        $method_id = $value['method_id'];
        $rules = $this->timeBasedWeightHandler->getRules($method_id);
        
        echo '<tr valign="top">';
        echo '<th scope="row" class="titledesc">';
        echo '<label for="' . esc_attr($value['id']) . '">' . esc_html($value['name']) . '</label>';
        echo '</th>';
        echo '<td class="forminp">';
        echo '<div id="time_rules_' . esc_attr($method_id) . '">';
        
        // Display existing rules
        if (!empty($rules)) {
            foreach ($rules as $index => $rule) {
                $this->display_single_time_rule($method_id, $index, $rule);
            }
        }
        
        // Add new rule button
        echo '<button type="button" class="button add_time_rule" data-method="' . esc_attr($method_id) . '">';
        echo __('Add Time Rule', 'DWCSM-text-domain');
        echo '</button>';
        
        // Template for new rules (hidden)
        echo '<div id="time_rule_template_' . esc_attr($method_id) . '" style="display:none;">';
        $this->display_single_time_rule($method_id, '__INDEX__', array());
        echo '</div>';
        
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        
        // Add JavaScript for handling rule additions and deletions
        $this->add_time_rules_javascript($method_id);
    }
    
    private function display_single_time_rule($method_id, $index, $rule) {
        echo '<div class="time_rule" data-index="' . esc_attr($index) . '">';
        echo '<h4>' . __('Time Rule', 'DWCSM-text-domain') . '</h4>';
        
        // Days of week
        echo '<p><label>' . __('Days:', 'DWCSM-text-domain') . '</label><br>';
        $days = array(
            'monday' => __('Monday', 'DWCSM-text-domain'),
            'tuesday' => __('Tuesday', 'DWCSM-text-domain'),
            'wednesday' => __('Wednesday', 'DWCSM-text-domain'),
            'thursday' => __('Thursday', 'DWCSM-text-domain'),
            'friday' => __('Friday', 'DWCSM-text-domain'),
            'saturday' => __('Saturday', 'DWCSM-text-domain'),
            'sunday' => __('Sunday', 'DWCSM-text-domain')
        );
        foreach ($days as $day_value => $day_label) {
            $checked = isset($rule['days']) && in_array($day_value, $rule['days']) ? 'checked' : '';
            echo '<label><input type="checkbox" name="time_rules[' . esc_attr($method_id) . '][' . esc_attr($index) . '][days][]" value="' . esc_attr($day_value) . '" ' . $checked . '> ' . esc_html($day_label) . '</label> ';
        }
        echo '</p>';
        
        // Time range
        echo '<p>';
        echo '<label>' . __('Start Hour:', 'DWCSM-text-domain') . '</label> ';
        echo '<select name="time_rules[' . esc_attr($method_id) . '][' . esc_attr($index) . '][start_hour]">';
        for ($i = 0; $i < 24; $i++) {
            $selected = isset($rule['start_hour']) && $rule['start_hour'] == $i ? 'selected' : '';
            echo '<option value="' . esc_attr($i) . '" ' . $selected . '>' . sprintf('%02d:00', $i) . '</option>';
        }
        echo '</select>';
        
        echo ' <label>' . __('End Hour:', 'DWCSM-text-domain') . '</label> ';
        echo '<select name="time_rules[' . esc_attr($method_id) . '][' . esc_attr($index) . '][end_hour]">';
        for ($i = 0; $i < 24; $i++) {
            $selected = isset($rule['end_hour']) && $rule['end_hour'] == $i ? 'selected' : '';
            echo '<option value="' . esc_attr($i) . '" ' . $selected . '>' . sprintf('%02d:00', $i) . '</option>';
        }
        echo '</select>';
        echo '</p>';
        
        // Weight limits
        echo '<p>';
        echo '<label>' . __('Min Weight:', 'DWCSM-text-domain') . '</label> ';
        echo '<input type="number" name="time_rules[' . esc_attr($method_id) . '][' . esc_attr($index) . '][min_weight]" value="' . esc_attr($rule['min_weight'] ?? '') . '" step="0.1" min="0"> ';
        
        echo '<label>' . __('Max Weight:', 'DWCSM-text-domain') . '</label> ';
        echo '<input type="number" name="time_rules[' . esc_attr($method_id) . '][' . esc_attr($index) . '][max_weight]" value="' . esc_attr($rule['max_weight'] ?? '') . '" step="0.1" min="0">';
        echo '</p>';
        
        // Delete button
        echo '<button type="button" class="button remove_time_rule">' . __('Remove Rule', 'DWCSM-text-domain') . '</button>';
        echo '<hr>';
        echo '</div>';
    }
    
    private function add_time_rules_javascript($method_id) {
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            var method_id = '<?php echo esc_js($method_id); ?>';
            
            // Add new rule
            $('.add_time_rule[data-method="' + method_id + '"]').on('click', function() {
                var template = $('#time_rule_template_' + method_id).html();
                var new_index = $('.time_rule').length;
                template = template.replace(/__INDEX__/g, new_index);
                $(this).before(template);
            });
            
            // Remove rule
            $('#time_rules_' + method_id).on('click', '.remove_time_rule', function() {
                $(this).closest('.time_rule').remove();
            });
        });
        </script>
        <?php
    }

    public function display_shipping_classes_checkboxes($value) {
        $shipping_classes = WC()->shipping->get_shipping_classes();
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

