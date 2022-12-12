<?php

require_once('config.php');
require_once('helper.php');

# Define your BASE_URL in config.php
$urlGraphql = BASE_URL . "/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

# Send the Authorization Token as a header, in order to create an access token please refer to the README.md
try {
    $magentoAuthorizationToken = getAdminToken();
} catch (Exception $exception) {
    showcontent('Exception: ' . $exception);
}

$headers[] = 'Authorization: Bearer ' . $magentoAuthorizationToken;

$query = <<<Query
query paynlGetPaymentMethods {
    paynlGetPaymentMethods {
        methods {
            name
            title
            profileid
        }
    }
}
Query;

$data = json_encode(['query' => $query]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlGraphql);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if ($result !== false) {
    showcontent(json_decode($result, true));
}