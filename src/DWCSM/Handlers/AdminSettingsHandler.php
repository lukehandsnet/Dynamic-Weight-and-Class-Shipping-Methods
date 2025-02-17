<?php
declare(strict_types=1);

namespace DWCSM\Handlers;

/**
 * Handles the admin settings interface and options management
 */
class AdminSettingsHandler {
    private const OPTION_PREFIX = 'dwcsm_';
    private const SETTINGS_PAGE = 'wc-settings';
    private const SETTINGS_TAB = 'shipping';
    private const SETTINGS_SECTION = 'dwcsm_settings';

    /**
     * Initialize the handler
     */
    public function __construct() {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_filter('woocommerce_get_sections_shipping', [$this, 'addShippingSection']);
        add_action('woocommerce_settings_tabs_shipping', [$this, 'outputSettings']);
        add_action('woocommerce_update_options_shipping', [$this, 'saveSettings']);
    }

    /**
     * Register plugin settings
     */
    public function registerSettings(): void
    {
        register_setting(
            self::SETTINGS_PAGE,
            self::OPTION_PREFIX . 'settings',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitizeSettings']
            ]
        );
    }

    /**
     * Add settings section to WooCommerce shipping settings
     *
     * @param array $sections
     * @return array
     */
    public function addShippingSection(array $sections): array
    {
        $sections[self::SETTINGS_SECTION] = __('Dynamic Weight & Class', 'dwcsm');
        return $sections;
    }

    /**
     * Output the settings page content
     */
    public function outputSettings(): void
    {
        $currentSection = $this->getCurrentSection();
        if ($currentSection !== self::SETTINGS_SECTION) {
            return;
        }

        $settings = $this->getSettings();
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Save the settings
     */
    public function saveSettings(): void
    {
        $currentSection = $this->getCurrentSection();
        if ($currentSection !== self::SETTINGS_SECTION) {
            return;
        }

        WC_Admin_Settings::save_fields($this->getSettings());
    }

    /**
     * Get the current settings section
     *
     * @return string
     */
    private function getCurrentSection(): string
    {
        return isset($_GET['section']) ? sanitize_text_field($_GET['section']) : '';
    }

    /**
     * Get all shipping methods settings fields
     *
     * @return array
     */
    private function getSettings(): array
    {
        $settings = [
            [
                'title' => __('Dynamic Weight & Class Settings', 'dwcsm'),
                'type'  => 'title',
                'desc'  => __('Configure weight and shipping class restrictions for each shipping method.', 'dwcsm'),
                'id'    => self::OPTION_PREFIX . 'settings_section'
            ]
        ];

        $shippingMethods = WC()->shipping()->get_shipping_methods();
        foreach ($shippingMethods as $method) {
            $methodId = $method->id;
            $settings = array_merge($settings, [
                [
                    'title'    => sprintf(__('%s Settings', 'dwcsm'), $method->method_title),
                    'type'     => 'title',
                    'id'       => self::OPTION_PREFIX . $methodId . '_title'
                ],
                [
                    'title'    => __('Minimum Weight (kg)', 'dwcsm'),
                    'type'     => 'number',
                    'desc'     => __('Minimum cart weight for this shipping method', 'dwcsm'),
                    'id'       => self::OPTION_PREFIX . $methodId . '_min_weight',
                    'default'  => '0',
                    'custom_attributes' => [
                        'min'  => '0',
                        'step' => '0.01'
                    ]
                ],
                [
                    'title'    => __('Maximum Weight (kg)', 'dwcsm'),
                    'type'     => 'number',
                    'desc'     => __('Maximum cart weight for this shipping method', 'dwcsm'),
                    'id'       => self::OPTION_PREFIX . $methodId . '_max_weight',
                    'default'  => '',
                    'custom_attributes' => [
                        'min'  => '0',
                        'step' => '0.01'
                    ]
                ],
                [
                    'title'    => __('Allowed Shipping Classes', 'dwcsm'),
                    'type'     => 'multiselect',
                    'desc'     => __('Select shipping classes that can use this method', 'dwcsm'),
                    'id'       => self::OPTION_PREFIX . $methodId . '_allowed_classes',
                    'options'  => $this->getShippingClassesOptions(),
                    'default'  => []
                ],
                [
                    'type' => 'sectionend',
                    'id'   => self::OPTION_PREFIX . $methodId . '_section_end'
                ]
            ]);
        }

        $settings[] = [
            'type' => 'sectionend',
            'id'   => self::OPTION_PREFIX . 'settings_section_end'
        ];

        return $settings;
    }

    /**
     * Get shipping classes as options array
     *
     * @return array
     */
    private function getShippingClassesOptions(): array
    {
        $options = [];
        $shippingClasses = WC()->shipping()->get_shipping_classes();
        
        foreach ($shippingClasses as $class) {
            $options[$class->term_id] = $class->name;
        }
        
        return $options;
    }

    /**
     * Sanitize settings before saving
     *
     * @param array $settings
     * @return array
     */
    public function sanitizeSettings(array $settings): array
    {
        foreach ($settings as $key => $value) {
            if (strpos($key, '_min_weight') !== false || strpos($key, '_max_weight') !== false) {
                $settings[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            } elseif (strpos($key, '_allowed_classes') !== false) {
                $settings[$key] = array_map('absint', (array)$value);
            }
        }
        return $settings;
    }
}