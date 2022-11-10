<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use \Exception;

class VoidTransaction
{
    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config   = $config;
    }

    /**
     * @param array $options
     * @return array
     */
    public function VoidTransaction($options)
    {
        $message = '';
        $result = false;
        try {
            $this->config->configureSDK();            
            $void = \Paynl\Transaction::void($options['pay_order_id']);    
            if ($void === true) {
                $message = 'PAY. has successfully voided the transaction.';
                $result = $void;
            } else {
                $message = 'PAY. could not process this void.';
            }
        } catch (\Exception $e) { 
            $message = strtolower($e->getMessage());
            if (substr($message, 0, 19) == '403 - access denied') {
                $message = 'PAY. could not authorize this void. Errorcode: PAY-MAGENTO2-GRAPHQL-VOID-001.';
            } else {
                $message = 'PAY. could not process this void (' . $message . '). Errorcode: PAY-MAGENTO2-VOID-GRAPHQL-002. Transaction: ' . $options['pay_order_id'];
            }
        }
        return ['result' => $result, 'message' => $message];
    }
}
