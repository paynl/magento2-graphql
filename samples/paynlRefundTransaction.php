<?php

require_once('config.php');
require_once('helper.php');

# Change the following URL to your Magento webshop and extend with '/graphql':
$urlGraphql = BASE_URL . "/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

# Send the Authorization Token as a header, in order to create an access token please refer to the README.md
try {
    $magentoAuthorizationToken = getAdminToken();
} catch (Exception $exception) {
    exit('Exception: ' . $exception);
}

$headers[] = 'Authorization: Bearer ' . $magentoAuthorizationToken;

$query = <<<Query
mutation paynlRefundTransaction(\$pay_order_id: String!, \$amount: String!) {
    paynlRefundTransaction(pay_order_id: \$pay_order_id, amount: \$amount) {
        paylink
    }
}
Query;
$variables = ["pay_order_id" => "1234567890abcdefg", "amount" => "1.23"];

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
