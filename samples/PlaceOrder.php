<?php

require_once 'config.php';
require_once 'helper.php';

# Define your BASE_URL in config.php
$urlGraphql = BASE_URL . "/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

$query = <<<Query
mutation (\$cart_id: String!, \$return_url: String) {
    placeOrder(input: {cart_id: \$cart_id, pay_return_url: \$return_url}) {
        order {
            order_number
            pay_redirect_url
        }
    }
}
Query;
$variables = ["cart_id" => "1234567890abcdefghijk", "return_url" => "http://mywebsite.nl/finish"];

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
