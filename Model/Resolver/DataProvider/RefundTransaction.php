<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use PayNL\Sdk\Model\Request\TransactionRefundRequest;

use \Exception;

class RefundTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SdkConfig
     */
    private $sdkConfig;

    /**
     * @var TransactionRefundRequest
     */
    private $transactionRefundRequest;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        TransactionRefundRequest $transactionRefundRequest
    ) {
        $this->config = $config;   
        $this->transactionRefundRequest = $transactionRefundRequest;
    }

    /**
     * @param array $options
     * @return array
     */
    public function RefundTransaction($options)
    {
        $result = false;
        try {       
            $transactionRefundRequest = new TransactionRefundRequest($options['pay_order_id']);
            $transactionRefundRequest
                ->setConfig($this->config->getPayConfig())
                ->setAmount($options['amount'])
                ->start();
            $message = 'PAY. has successfully refunded the transaction.';
            $result = true;
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
