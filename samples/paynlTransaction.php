<?php

$url = "http://mymagento.com/graphql";

$headers = array();
$headers[] = 'Content-Type: application/json';

$query = <<<Query
query paynlTransaction(\$pay_order_id: String!) {
    paynlTransaction(pay_order_id: \$pay_order_id) {
        orderId
        state  
        stateName  
        currency  
        amount  
        currenyAmount 
        paidAmount  
        paidCurrenyAmount
        refundAmount 
        refundCurrenyAmount
        created
        orderNumber
        isSuccess
    }
}
Query;
$variables = ["pay_order_id" => "1234567890abcdefg"];

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
