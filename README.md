<p align="center">
    <img src="https://www.pay.nl/uploads/1/brands/main_logo.png" />
</p>
<h1 align="center">PAY. Magento2 Graphql Module</h1>
  
# Description

This module adds GraphQL support for the [PAY. Magento2 plugin](https://github.com/paynl/magento2-plugin)


- [Description](#description)
- [Requirements](#requirements)
- [Installation](#installation)
- [Update instructions](#update-instructions)
- [Queries](#queries)
- [Mutations](#mutations)
- [Types](#types)
- [Usage](#usage)
- [Support](#support)



# Requirements

    PHP 7.2 or higher
    PAY. Magento2 plugin 2.4.0 or higher
    Tested up to Magento 2.4.3


# Installation
### Installing

In command line, navigate to the installation directory of Magento2

Enter the following commands:

```
composer require paynl/magento2-plugin
php bin/magento setup:upgrade
php bin/magento cache:clean
```

The plugin is now installed


# Queries

## paynlTransaction

Get the PAY. transaction status based on the PAY order_id which can be found in the PAY. backend.<br/>
This query expects `pay_order_id` as an argument. It will return [#PaynlTransactionOutput](#paynltransactionoutput)

# Mutations

## paynlStartTransaction

Start a transaction based on the Magento2 order_id, this will return a url with the PAY. transaction.<br/>
This mutation expects `order_id` and optionally `return_url`. It will return [#PaynlStartTransactionOutput](#paynlstarttransactionoutput)

## paynlFinishTransaction

The paynlFinishTransaction mutation closes the Magento2 quote if the transaction is marked as successfull and returns the PAY. transaction status. <br/>
This mutation expects the `pay_order_id`. It will return [#PaynlTransactionOutput](#paynltransactionoutput)

# Types

## PaynlTransactionOutput

| Variable            | Type    | Description                                   |
| ------------------- | ------- | --------------------------------------------- |
| orderId             | String  | The Pay order id.                             |
| state               | Int     | The State number.                             |
| stateName           | String  | The State name.                               |
| currency            | String  | The currency used to pay.                     |
| amount              | String  | The amount in cents.                          |
| currenyAmount       | String  | The amount in cents in the curency.           |
| paidAmount          | String  | The paid amount in cents.                     |
| paidCurrenyAmount   | String  | The paid amount in cents in the curency.      |
| refundAmount        | String  | The refunded amount in cents.                 |
| refundCurrenyAmount | String  | The refunded amount in cents in the curency.  |
| created             | String  | Created at date.                              |
| orderNumber         | String  | The Order increment id for the Magento order. |
| isSuccess           | Boolean | Was the payment successfull.                  |

## PaynlStartTransactionOutput

| Variable    | Type   | Description                              |
| ----------- | ------ | ---------------------------------------- |
| redirectUrl | String | The url to redirect to the pay checkout. |


# Usage

To see how to use the queries, please check the samples folder. 

# Support
https://www.pay.nl

Contact us: support@pay.nl