# Dynamic Weight and Class Shipping Methods WordPress Plugin

## Overview

Dynamic Weight and Class Shipping Methods is a WooCommerce plugin designed to enable e-commerce administrators to dynamically control and tailor shipping options based on cart characteristics. This plugin offers a blend of flexibility and precision, enabling administrators to define shipping method availability and restrictions based on the cart's total weight and associated shipping classes, ensuring that the provided shipping options are both accurate and optimized for various logistical needs.

## Features

- Dynamic shipping method filtering based on cart weight
- Shipping class-based restrictions
- Minimum and maximum weight thresholds for each shipping method
- Easy-to-use admin interface integrated with WooCommerce settings
- Support for multiple shipping classes per method
- Real-time cart validation
- Type-safe implementation with PHP 7.4+ features

## Requirements

- PHP 7.4 or higher
- WordPress 5.6 or higher
- WooCommerce 5.0 or higher
- Composer (for development)

## Installation

### Via Composer (Recommended)

```bash
composer require lukehandsnet/dynamic-weight-and-class-shipping-methods
```

### Manual Installation

1. Download the latest release from the repository
2. Extract the plugin to your `wp-content/plugins/` directory
3. Activate the plugin through the WordPress admin interface

## Configuration

1. Navigate to WooCommerce → Settings → Shipping
2. Click on the "Dynamic Weight & Class" tab
3. Configure settings for each shipping method:
   - Set minimum and maximum weights
   - Select allowed shipping classes
   - Save changes

## Development

### Setup

```bash
# Clone the repository
git clone https://github.com/lukehandsnet/Dynamic-Weight-and-Class-Shipping-Methods.git
cd Dynamic-Weight-and-Class-Shipping-Methods

# Install dependencies
composer install

# Run tests
composer test

# Run code quality checks
composer check
```

### Running Tests

```bash
# Run PHPUnit tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/ShippingMethodHandlerTest.php

# Run with coverage report
./vendor/bin/phpunit --coverage-html coverage
```

### Code Quality

The project uses several tools to maintain code quality:

- PHPUnit for testing
- PHP_CodeSniffer for coding standards
- PHPStan for static analysis

Run all checks with:

```bash
composer check
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the GPL-2.0 License - see the [LICENSE.md](LICENSE.md) file for details.

## Author

[Luke Hands](https://lukehands.net/shipping)

## Acknowledgements

Special thanks to the WordPress and WooCommerce communities for their continued support and feedback. This plugin is continuously evolving thanks to the valuable input from users and contributors.