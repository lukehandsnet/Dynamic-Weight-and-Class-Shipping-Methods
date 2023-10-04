# Shipping Class By Cart Weight WordPress Plugin

**Status: Work In Progress - Infant Stage**

This WordPress plugin adjusts available shipping methods based on the cart weight in a WooCommerce store. It offers flexibility to manage different weight classes and to assign corresponding delivery classes. It allows admins to easily set weight limits for different shipping methods.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Files Structure](#files-structure)
- [Settings](#settings)
- [Author](#author)
- [License](#license)

## Installation

1. Download the plugin files.
2. Extract the plugin files to your `wp-content/plugins` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Once installed and activated, the plugin will automatically adjust the available shipping methods based on the total weight of the cart. The settings page allows the admin to define weight limits for different shipping methods.

## Files Structure

The plugin is structured into multiple files for modularity:

1. **shipping-class-by-cart-weight.php:** The main plugin file that includes other necessary files and initializes the plugin.
2. **admin-menu-settings.php:** Handles the admin menu creation, settings registration, and renders the settings page.
3. **shipping-methods.php:** Contains the logic to adjust the available shipping methods based on the cart weight.

## Settings

The plugin settings can be accessed from the WordPress admin panel where three weight classes can be defined:
- **Light Weight Limit:** Set the maximum weight for the light weight class.
- **Medium Weight Limit:** Set the maximum weight for the medium weight class.
- **Heavy Weight Limit:** Set the maximum weight for the heavy weight class.

Each weight class can be associated with a delivery class (e.g., standard, express, freight).
## To-Do List: Shipping Class By Cart Weight WordPress Plugin

#### Development & Code Refactoring
- [X] **Split Code into Files:** Modularize the existing code for enhanced readability and maintenance.
    - [X] Create separate PHP files for functions related to settings, admin menu, and shipping methods adjustments.
    - [ ] Ensure all files are well-documented with comments for clarity and future reference.
  
- [X] **Class-based Refactoring:** Transition function-based code to class-based structure to encapsulate related functionalities and promote OOP practices.
  
- [ ] **Security & Validation:** 
    - [ ] Implement thorough data validation for settings inputs to prevent potential issues.
    - [ ] Implement nonces for form submissions to enhance security.

- [ ] **Code Optimization:** 
    - [ ] Review and optimize loops and condition checks.
    - [ ] Optimize data retrieval and storage methods.

#### Feature Development
- [ ] **Enhanced Weight Class Management:**
    - [ ] Develop UI to dynamically add, edit, or remove weight classes.
    - [ ] Allow for customization of delivery class names.

- [ ] **Customer Notifications:**
    - [ ] Implement customer notification/alert for shipping method adjustments based on weight.

- [ ] **Localization:**
    - [ ] Implement localization/internationalization practices to make the plugin translatable.

- [ ] **Multi-shipment Handling:**
    - [ ] Implement handling and calculation for shipments that may need to be split due to weight constraints.

#### Testing
- [ ] **Unit Testing:**
    - [ ] Implement unit tests to validate all functionalities under various scenarios.
    - [ ] Ensure accurate retrieval and application of shipping methods based on defined weight classes.

- [ ] **Integration Testing:**
    - [ ] Test compatibility with various versions of WordPress and WooCommerce.
    - [ ] Test with various themes to ensure UI/UX consistency.

- [ ] **User Testing:**
    - [ ] Conduct user testing for UI/UX and usability validation.
    - [ ] Gather feedback to identify areas for improvement or enhancement.

#### Documentation & User Guide
- [ ] **Plugin Documentation:**
    - [ ] Create comprehensive documentation detailing setup, configuration, and troubleshooting.
    - [ ] Include screenshots and step-by-step guides for various functionalities.

- [ ] **Developer Documentation:**
    - [ ] Detail development decisions, code structure, and extendability options for other developers.
    - [ ] Ensure all code is well-commented and follows WordPress coding standards.

#### Marketing & Release Preparation
- [ ] **Website/Landing Page:**
    - [ ] Create a landing page or website for the plugin to provide details, documentation, and download options.

- [ ] **Promotion:**
    - [ ] Create promotional material, blog posts, or news releases to announce the plugin.

- [ ] **WordPress Repository Submission:**
    - [ ] Prepare and submit the plugin to the WordPress plugin repository for wider accessibility.

#### Continuous Improvement & Support
- [ ] **User Feedback Collection:**
    - [ ] Establish channels for collecting user feedback and reviews.

- [ ] **Support:**
    - [ ] Create support forums or channels to assist users with queries and issues.
    - [ ] Develop a FAQ section based on encountered issues and queries.

- [ ] **Updates & Maintenance:**
    - [ ] Periodically review and update the plugin to ensure compatibility with the latest WordPress and WooCommerce versions.
    - [ ] Implement enhancements and additional features based on user feedback.

## Author

- [Luke Hands](https://lukehands.net/)

## License

This project is licensed under the GPL-2.0 License.

## Acknowledgements

This plugin was developed as a collaborative effort and with the guidance provided by the community. It is currently a work in progress and in its infant stages, but contributions and feedback are welcome to improve its functionalities and features.
