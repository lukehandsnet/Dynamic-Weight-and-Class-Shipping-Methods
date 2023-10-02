<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function asm_add_admin_menu() {
    add_options_page('Adjust Shipping Methods', 'Adjust Shipping Methods', 'manage_options', 'adjust_shipping_methods', 'asm_options_page');
}

add_action('admin_menu', 'asm_add_admin_menu');

function asm_settings_init() {
    register_setting('pluginPage', 'asm_settings');
    add_settings_section(
        'asm_pluginPage_section',
        __('Adjust the weight classes and corresponding delivery classes.', 'adjust-shipping-methods'),
        'asm_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'asm_light_weight',
        __('Light Weight Limit', 'adjust-shipping-methods'),
        'asm_light_weight_render',
        'pluginPage',
        'asm_pluginPage_section'
    );

    add_settings_field(
        'asm_medium_weight',
        __('Medium Weight Limit', 'adjust-shipping-methods'),
        'asm_medium_weight_render',
        'pluginPage',
        'asm_pluginPage_section'
    );

    add_settings_field(
        'asm_heavy_weight',
        __('Heavy Weight Limit', 'adjust-shipping-methods'),
        'asm_heavy_weight_render',
        'pluginPage',
        'asm_pluginPage_section'
    );
}

add_action('admin_init', 'asm_settings_init');

function asm_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Adjust Shipping Methods</h2>
        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <?php
}

function asm_settings_section_callback() {
    echo '<p>This section is for setting up the weight limits for different shipping methods.</p>';
}

function asm_light_weight_render() {
    $options = get_option('asm_settings', array('asm_light_weight' => 5));
    ?>
    <input type='text' name='asm_settings[asm_light_weight]' value='<?php echo $options['asm_light_weight']; ?>'>
    <?php
}

function asm_medium_weight_render() {
    $options = get_option('asm_settings', array('asm_medium_weight' => 10));
    ?>
    <input type='text' name='asm_settings[asm_medium_weight]' value='<?php echo $options['asm_medium_weight']; ?>'>
    <?php
}

function asm_heavy_weight_render() {
    $options = get_option('asm_settings', array('asm_heavy_weight' => 15));
    ?>
    <input type='text' name='asm_settings[asm_heavy_weight]' value='<?php echo $options['asm_heavy_weight']; ?>'>
    <?php
}
