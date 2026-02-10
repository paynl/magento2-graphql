<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use PayNL\Sdk\Model\Request\OrderStatusRequest;

class GetTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param string $payOrderId
     * @return array
     */
    public function getTransactionData($payOrderId)
    {
        $payOrder = $this->getTransaction($payOrderId);

        $data['orderId'] = $payOrderId;
        $data['state'] = $payOrder->getStatusCode();
        $data['stateName'] = $payOrder->getStatusName();
        $data['amount'] = array('value' => $payOrder->getAmount(), 'currency' => $payOrder->getCurrency());
        $data['amountRefund'] = array('value' => $payOrder->getAmountRefunded(), 'currency' => $payOrder->getCurrency());
        $data['created'] = $payOrder->getCreatedAt();
        $data['orderNumber'] = $payOrder->getExtra1();
        $data['isSuccess'] = ($payOrder->isPaid() || $payOrder->isAuthorized() || $payOrder->isPending());

        return $data;
    }

    /**
     * @param string $payOrderId
     * @return \OrderStatusRequest
     */
    public function getTransaction($payOrderId)
    {
        $payOrder = (new OrderStatusRequest($payOrderId))->setConfig($this->config->getPayConfig())->start();
        return $payOrder;
    }
}
