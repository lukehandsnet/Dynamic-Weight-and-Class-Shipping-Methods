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

## Author

- [Luke Hands](https://lukehands.net/)

## License

This project is licensed under the GPL-2.0 License.

## Acknowledgements

This plugin was developed as a collaborative effort and with the guidance provided by the community. It is currently a work in progress and in its infant stages, but contributions and feedback are welcome to improve its functionalities and features.
