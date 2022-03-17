<?php

$graphQL = <<<Query
    mutation paynlFinishTransaction(\$pay_order_id: String!) {
        paynlFinishTransaction(pay_order_id: \$pay_order_id) {
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

# Vul de waardes in die in de graphQL gebruikt worden.
$vars = ["pay_order_id" => '1234567890'];

# Post de graphQL naar magento2 via CURL
curl 'http://magento.config/graphql' -H 'Authorization: Bearer 1234567890abcdefghijklmnopqrstuvwxyz' -H 'Content-Type: application/json'  --data-binary '{"query":"mutation paynlFinishTransaction($pay_order_id: String!) {\n paynlFinishTransaction(pay_order_id: $pay_order_id) {\n orderId\n state  \n stateName  \n currency  \n amount  \n currenyAmount \n paidAmount  \n paidCurrenyAmount\n refundAmount \n refundCurrenyAmount\n created\n orderNumber\n isSuccess\n }\n }","variables":"{\"pay_order_id\":\"1234567890\"}"}' 

