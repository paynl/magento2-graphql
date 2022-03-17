<?php

$graphQL = <<<Query
    mutation paynlStartTransaction(\$order_id: String!, \$return_url: String!) {
        paynlStartTransaction (order_id: $order_id, return_url: $return_url) {
            redirectUrl        
        }        
    }
Query;

# Vul de waardes in die in de graphQL gebruikt worden.
$vars = ["order_id" => '1', "return_url" => 'http://test.pay.nl/'];

# Post de graphQL naar magento2 via CURL
curl 'http://magento.config/graphql' -H 'Authorization: Bearer 1234567890abcdefghijklmnopqrstuvwxyz' -H 'Content-Type: application/json'  --data-binary '{"query":"mutation paynlStartTransaction($order_id: String!, $return_url: String!) {\n paynlStartTransaction (\n order_id: $order_id\n return_url: $return_url\n ) {\n redirectUrl\n }\n}","variables":"{\n \"order_id\":\"1\",\n \"return_url\":\"http://test.pay.nl/\"\n}"}'

