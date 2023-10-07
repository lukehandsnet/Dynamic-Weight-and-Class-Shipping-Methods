use PHPUnit\Framework\TestCase;
use DWCSM\Admin_Settings_Handler;

class AdminSettingsHandlerTest extends TestCase {
    public function testAddAdminMenu() {
        $adminSettingsHandler = new Admin_Settings_Handler();
        $this->expectOutputString('add_options_page was called');
        $adminSettingsHandler->add_admin_menu();
    }

    public function testSettingsInit() {
        $adminSettingsHandler = new Admin_Settings_Handler();
        $this->expectOutputString('register_setting was called');
        $adminSettingsHandler->settings_init();
    }

    public function testOptionsPage() {
        $adminSettingsHandler = new Admin_Settings_Handler();
        $this->expectOutputString('form was displayed');
        $adminSettingsHandler->options_page();
    }

    public function testSettingsSectionCallback() {
        $adminSettingsHandler = new Admin_Settings_Handler();
        $this->expectOutputString('<p>This section is for setting up the weight limits for different shipping methods.</p>');
        $adminSettingsHandler->settings_section_callback();
    }

    public function testDisplayShippingMethods() {
        $adminSettingsHandler = new Admin_Settings_Handler();
        $this->expectOutputString('<h3>Available Shipping Methods</h3>');
        $adminSettingsHandler->display_shipping_methods();
    }
}