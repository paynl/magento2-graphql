<?php

# Change the following URL to your Magento webshop and extend with '/graphql':
$url = "http://mymagento.com/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

$query = <<<Query
mutation paynlStartTransaction(\$magento_order_id: String!, \$return_url: String) {
    paynlStartTransaction(magento_order_id: \$magento_order_id, return_url: \$return_url) {
        redirectUrl
    }
}
Query;
$variables = ["magento_order_id" => "10", "return_url" => "http://mywebsite.nl/finish"];

$data = json_encode(['query' => $query, 'variables' => $variables]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if ($result !== false) {
    print_r(json_decode($result, true));
}
