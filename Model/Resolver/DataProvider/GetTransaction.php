<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;

class GetTransaction
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var array
     */
    public $whitelist = [
        'orderId', 'state', 'stateName', 'currency', 'amount', 'currenyAmount', 'paidAmount',
        'paidCurrenyAmount', 'refundAmount', 'refundCurrenyAmount', 'created', 'orderNumber'
    ];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $payOrderId
     * @return array
     */
    public function getTransactionData($payOrderId)
    {
        $transaction = $this->getTransaction($payOrderId);
        $paymentDetails = $transaction->getData()['paymentDetails'];
        $data = array_intersect_key($paymentDetails, array_flip($this->whitelist));

        $data['amount'] = array('value' => $paymentDetails['amount'], 'currency' => $paymentDetails['currency']);
        $data['amountOriginal'] = array('value' => $paymentDetails['currenyAmount'], 'currency' => $paymentDetails['currency']);
        $data['amountPaid'] = array('value' => $paymentDetails['paidAmount'], 'currency' => $paymentDetails['currency']);
        $data['amountPaidOriginal'] = array('value' => $paymentDetails['paidCurrenyAmount'], 'currency' => $paymentDetails['currency']);
        $data['amountRefund'] = array('value' => $paymentDetails['refundAmount'], 'currency' => $paymentDetails['currency']);
        $data['amountRefundOriginal'] = array('value' => $paymentDetails['refundCurrenyAmount'], 'currency' => $paymentDetails['currency']);

        $data['isSuccess'] = ($transaction->isPaid() || $transaction->isAuthorized() || $transaction->isPending());

        return $data;
    }

    /**
     * @param string $payOrderId
     * @return \Paynl\Transaction::status
     */
    public function getTransaction($payOrderId)
    {
        \Paynl\Config::setApiToken($this->config->getApiToken());
        return \Paynl\Transaction::status($payOrderId);
    }
}
