<?php
namespace DWCSM\Tests;
use PHPUnit\Framework\TestCase;
use DWCSM\AdminSettingsHandler;
use WP_Mock;
use Mockery;


class AdminSettingsHandlerTest extends TestCase {

    public function setUp(): void {
        // Start the WP_Mock
        
        WP_Mock::setUp();
        
        
        
    }

    public function tearDown(): void {
        // Tear down WP_Mock
        WP_Mock::tearDown();
    }

    public function testExample() {
        $this->expectOutputString('Hello, World!');
        echo 'Hello, World!';
    }

    public function testAddAdminMenu() {
        
        WP_Mock::userFunction('add_action', [
            'times' => 0,
            'args' => ['admin_menu', [Mockery::any(), 'add_admin_menu']],
        ]);

        //add_options_page
        WP_Mock::userFunction('add_options_page', [
            'times' => 1,
            'args' => [
                'Adjust Shipping Methods',
                'Adjust Shipping Methods',
                'manage_options',
                'adjust_shipping_methods',
                Mockery::any(),
            ],
        ]);

        //register_setting('pluginPage', 'asm_settings')
        WP_Mock::userFunction('register_setting', [
            'times' => 0,
            'args' => [
                'pluginPage',
                'asm_settings'
            ],
        ]);
        $adminSettingsHandler = new AdminSettingsHandler();
        //$this->expectOutputString('some text');
        $adminSettingsHandler->add_admin_menu();
    }

    public function testSettingsInit() {
        WP_Mock::userFunction('register_setting', [
            'times' => 1,
            'args' => ['pluginPage', 'asm_settings']
        ]);

        WP_Mock::userFunction('add_settings_section', [
            'times' => 1, // expecting it will be called once
            'args' => [
                'asm_pluginPage_section', // section
                'Adjust the weight classes and corresponding delivery classes.', // title
                Mockery::any(),// callback
                'pluginPage' // page
            ]
        ]);

        $adminSettingsHandler = new AdminSettingsHandler();
        //$this->expectOutputString('register_setting was called');
        $adminSettingsHandler->settings_init();
    }

    // public function testOptionsPage() {
        
    //     WP_Mock::userFunction('submit_button', [
    //         'times' => 1 
    //     ]);
    //     WP_Mock::userFunction('settings_fields', [
    //         'times' => 1,
    //         'args' => [$this->anything()]
    //     ]);
    //     WP_Mock::userFunction('do_settings_sections', [
    //         'times' => 1,
    //         'args' => [$this->anything()]
    //     ]);

    //     WP_Mock::userFunction('wp_nonce_field', [
    //         'times' => 1,  // expecting the function will be called once
    //         'args' => [    // providing the expected arguments
    //             'save_your_settings', 'asm_settings_nonce'
    //         ]
    //     ]);
        
    //     WP_Mock::userFunction('settings_fields', [
    //         'times' => 1,  
    //         'args' => [
    //             'pluginPage'  // expected argument
    //         ]
    //     ]);
        
    //     WP_Mock::userFunction('do_settings_sections', [
    //         'times' => 1,
    //         'args' => [
    //             'pluginPage'
    //         ]
    //     ]);

        
    //     // WP_Mock::mockClass('WC_Shipping_Zones');
    //     // // If you have static method calls you want to test, define them like this:
    //     // WP_Mock::userFunction('WC_Shipping_Zones::get_zones', [
    //     //     'times' => 1,  // Expected number of times this function will be called.
    //     //     'return' => []  // Return value you want the method to return.
    //     // ]);
    //     WP_Mock::userFunction("WC_Shipping_Zones::get_zones", [
    //         'return' => []
    //     ]);

    //     $adminSettingsHandler = new AdminSettingsHandler();
    //     $this->expectOutputString('form was displayed');
    //     $adminSettingsHandler->options_page();
    // }

    // public function testSettingsSectionCallback() {
    //     $adminSettingsHandler = new AdminSettingsHandler();
    //     $this->expectOutputString('<p>This section is for setting up the weight limits for different shipping methods.</p>');
    //     $adminSettingsHandler->settings_section_callback();
    // }

    // public function testDisplayShippingMethods() {
        
    //     WP_Mock::userFunction('get_shipping_methods', [
    //         'times' => 1,
    //         'return' => []  // Return value should be what your actual function will return
    //     ]);
    

    //     $adminSettingsHandler = new AdminSettingsHandler();
    //     $this->expectOutputString('<h3>Available Shipping Methods</h3>');
    //     $adminSettingsHandler->display_shipping_methods();
    // }
}