<?php

require_once 'config.php';
require_once 'helper.php';

# Define your BASE_URL in config.php
$urlGraphql = BASE_URL . "/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

$query = <<<Query
mutation paynlRestoreCart(\$cart_id: String!) {
    paynlRestoreCart(cart_id: \$cart_id) {
      cart {
          email
          billing_address {
              city
              country {
                  code
                  label
              }
              firstname
              lastname
              postcode
              region {
                  code
                  label
              }
              street
              telephone
          }
          shipping_addresses {
              firstname
              lastname
              street
              city
              region {
                  code
                  label
              }
              country {
                  code
                  label
              }
              telephone
              available_shipping_methods {
                  amount {
                      currency
                      value
                  }
                  available
                  carrier_code
                  carrier_title
                  error_message
                  method_code
                  method_title
                  price_excl_tax {
                      value
                      currency
                  }
                  price_incl_tax {
                      value
                      currency
                  }
              }
              selected_shipping_method {
                  amount {
                      value
                      currency
                  }
                  carrier_code
                  carrier_title
                  method_code
                  method_title
              }
          }
          items {
              id
              product {
                  name
                  sku
              }
              quantity
          }
          available_payment_methods {
              code
              title
          }
          selected_payment_method {
              code
              title
          }
          applied_coupons {
              code
          }
          prices {
              grand_total {
                  value
                  currency
              }
          }
      }
    }
  }
Query;
$variables = ["cart_id" => "1234567890abcdefghijk"];

$data = json_encode(['query' => $query, 'variables' => $variables]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlGraphql);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if ($result !== false) {
    print_r(json_decode($result, true));
}
