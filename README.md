<p align="center">
    <img src="https://www.pay.nl/uploads/1/brands/main_logo.png" />
</p>
<h1 align="center">PAY. GraphQL Extension for Magento</h1>

# Description

This module adds GraphQL functionality for the [PAY. Magento2 plugin](https://github.com/paynl/magento2-plugin)

- [Description](#description)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage And Examples](#usage-and-examples)
- [Support](#support)

# Requirements

    PHP 7.2 or higher
    PAY. Magento2 plugin 3.5.2 or higher
    Tested up to Magento 2.4.6

# Installation

On your server's command line, navigate to the installation directory of Magento and enter the following commands:

```
composer require paynl/magento2-graphql
php bin/magento setup:upgrade
php bin/magento cache:clean
```

The plugin is now installed and ready for use.

# Usage and Examples

To see how to use the queries, please check our [GraphQL wiki](/wiki/). <br/>

This GraphQL library consists of queries, mutations and some other classes.
They are explained on the wiki.

# Support

https://www.pay.nl

Contact us: support@pay.nl
