<p align="center">
    <img src="https://www.pay.nl/uploads/1/brands/main_logo.png" />
</p>
<h1 align="center">PAY. GraphQL Extension for Magento 2</h1>
  
# Description

This module adds GraphQL functionality to the [PAY. Magento2 plugin](https://github.com/paynl/magento2-plugin)

- [Description](#description)
- [Requirements](#requirements)
- [Installation](#installation)
- [Create Authorization token](#create-authorization-token)
- [Usage And Examples](#usage-and-examples)
- [Queries](#queries)
- [Mutations](#mutations)
- [Types](#types)
- [Support](#support)


# Requirements
    PHP 7.2 or higher
    PAY. Magento2 plugin 2.4.0 or higher
    Tested up to Magento 2.4.3


# Installation

On your server's command line, navigate to the installation directory of Magento2 and enter the following commands:

```
composer require paynl/magento2-graphql-module
php bin/magento setup:upgrade
php bin/magento cache:clean
```

The plugin is now installed and ready for use.

# Create Authorization token
PAY. GraphQL requests require an ADMIN or INTEGRATION Authorization token to be used. 

**ADMIN Authorization token**<br/>
You can create an ADMIN Authorization token by making a POST to the following REST url:
http://mymagento.com/rest/all/V1/integration/admin/token?username=MyName&password=MyPassword

An ADMIN Authorization token has a default lifetime of 1 hour, in order to extend the duration you can change this setting in the magento2 backend.<br/>
In the backend navigate to `Stores` -> `Configuration` -> `Services` -> `OAuth` and then expand the `Access Token Expiration` tab, here you can change the duration of your ADMIN Authorization token.

**INTEGRATION Authorization token** (Magento 2.4.3 or lower only)<br/>
You can create an INTEGRATION token via the magento2 backend. 
- Navigate to System -> Integrations. 
- Then click "Add New Integration" at the top right of the screen.
- Enter a name for your new integration, and leave the other fields blank (they are optional). 
- Then click "Save" at the top right of the screen.
- Now in the integration-list, click on `Activate` on your newly made integration.
- Then click "Allow" at the top right of the screen.
- You will now be presented with several codes. Copy past the "Access Token" and Click Done.

# Usage and Examples
To see how to use the queries, please check the [samples folder](/samples). <br/>

This GraphQL library consists of queries, mutations and some other classes.
They are explained below.

# Queries

- **paynlGetTransaction**<br/>
Retrieve a PAY. transaction status based on the PAY order-ID.<br/>
This query expects `pay_order_id` as an argument and returns a [#paynlGetTransactionOutput](#paynlGetTransactionoutput)

# Mutations

- **paynlStartTransaction**</br>
Start a transaction based on the Magento2 order-ID. This will return a URL with the PAY. transaction.<br/>
This mutation expects `order_id` and optionally `return_url`. It will return type [#PaynlStartTransactionOutput](#paynlstarttransactionoutput)

- **paynlFinishTransaction**</br>
This mutation closes the Magento quote when the transaction is marked as successfull and also returns the PAY. transaction info. <br/>
As argument it expects the `pay_order_id` and returns type [#paynlGetTransactionOutput](#paynlGetTransactionoutput)

# Types

- **paynlGetTransactionOutput**

| Variable            | Type    | Description                                   |
| ------------------- | ------- | --------------------------------------------- |
| orderId             | String  | The PAY. order-ID.                            |
| state               | Int     | The state number.                             |
| stateName           | String  | The state name.                               |
| currency            | String  | The currency used in the payment              |
| amount              | String  | The amount in cents.                          |
| currenyAmount       | String  | The amount in cents in the currency.          |
| paidAmount          | String  | The paid amount in cents.                     |
| paidCurrenyAmount   | String  | The paid amount in cents in the currency.     |
| refundAmount        | String  | The refunded amount in cents.                 |
| refundCurrenyAmount | String  | The refunded amount in cents in the currency. |
| created             | String  | Date of creation.                             |
| orderNumber         | String  | The order number given by Magento.            |
| isSuccess           | Boolean | True, in case of a successfull payment.       |

- **PaynlStartTransactionOutput**

| Variable    | Type   | Description                              |
| ----------- | ------ | ---------------------------------------- |
| redirectUrl | String | The URL to redirect to the PAY. checkout.|

# Support
https://www.pay.nl

Contact us: support@pay.nl
