<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use PayNL\Sdk\Model\Request\OrderVoidRequest;
use \Exception;

class VoidTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderVoidRequest
     */
    private $orderVoidRequest;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        OrderVoidRequest $orderVoidRequest
    ) {
        $this->config = $config;
        $this->orderVoidRequest = $orderVoidRequest;
    }

    /**
     * @param array $options
     * @return array
     */
    public function VoidTransaction($options)
    {
        $result = false;
        try {
            $orderVoidRequest = new OrderVoidRequest($options['pay_order_id']);
            $orderVoidRequest->setConfig($this->config->getPayConfig());
            $orderVoidRequest->start();
            
            $message = 'PAY. has successfully voided the transaction.';
            $result = true;            
        } catch (\Exception $e) {
            $message = strtolower($e->getMessage());
            if (substr($message, 0, 19) == '403 - access denied') {
                $message = 'PAY. could not authorize this void. Errorcode: PAY-MAGENTO2-GRAPHQL-VOID-001.';
            } else {
                $message = 'PAY. could not process this void (' . $message . '). Errorcode: PAY-MAGENTO2-GRAPHQL-VOID-002. Transaction: ' . $options['pay_order_id'];
            }
        }
        return ['result' => $result, 'message' => $message];
    }
}
