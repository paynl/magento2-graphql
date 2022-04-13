<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use \Exception;

class RefundTransaction
{
    public function __construct(
        Config $config
    ) {
        $this->config   = $config;
    }

    public function RefundTransaction($options)
    {
        $message = '';
        $result = 0;
        try {
            $this->config->configureSDK();
            $refund = \Paynl\Transaction::refund($options['pay_order_id'], $options['amount'])->getData();            
            if (isset($refund['request']['result']) && $refund['request']['result']) {
                $message = $refund['description'] ?? '';
                $result = $refund['request']['result'];
            } elseif (isset($refund['request']['errorId']) && $refund['request']['errorId']) {
                throw new Exception($refund['request']['errorMessage'] ?? '');
            } else {
                $message = 'PAY. could not process this refund.';
            }
        } catch (\Exception $e) { 
            $message = strtolower($e->getMessage());
            if (substr($message, 0, 19) == '403 - access denied') {
                $message = 'PAY. could not authorize this refund. Errorcode: PAY-MAGENTO2-GRAPHQL-REFUND-001.';
            } else {
                $message = 'PAY. could not process this refund (' . $message . '). Errorcode: PAY-MAGENTO2-GRAPHQL-REFUND-002. Transaction: ' . $options['pay_order_id'];
            }
        }
        return ['result' => $result, 'message' => $message];
    }
}
