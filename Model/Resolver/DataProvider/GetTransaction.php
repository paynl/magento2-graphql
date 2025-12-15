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
        // $paymentDetails = $transaction->getData()['paymentDetails'];
        $data = [
            'orderId' => $transaction->getOrderId(),
            'state'   => $transaction->getStatusCode(),
            'stateName' => $transaction->getStatusName(),
            'currency' => $transaction->getCurrency(),
            'amount' => $transaction->getAmount(),
            'currenyAmount' => $transaction->getAmount(),
            'paidAmount' => $transaction->getCapturedAmount()?->getValue() / 100,
            'paidCurrenyAmount' => $transaction->getCapturedAmount()?->getValue() / 100,
            'refundAmount' => $transaction->getAmountRefunded(),
            'refundCurrenyAmount' => $transaction->getAmountRefunded(),
            'created' => $transaction->getCreatedAt(),
            'orderNumber' => $transaction->getReference(),
        ];

        $data['amount'] = array('value' => $data['amount'], 'currency' => $data['currency']);
        $data['amountOriginal'] = array('value' => $data['currenyAmount'], 'currency' => $data['currency']);
        $data['amountPaid'] = array('value' => $data['paidAmount'], 'currency' => $data['currency']);
        $data['amountPaidOriginal'] = array('value' => $data['paidCurrenyAmount'], 'currency' => $data['currency']);
        $data['amountRefund'] = array('value' => $data['refundAmount'], 'currency' => $data['currency']);
        $data['amountRefundOriginal'] = array('value' => $data['refundCurrenyAmount'], 'currency' => $data['currency']);

        $data['isSuccess'] = ($transaction->isPaid() || $transaction->isAuthorized() || $transaction->isPending());

        return $data;
    }

    /**
     * @param string $payOrderId
     * @return \PayNL\Sdk\Model\Pay\PayOrder
     */
    public function getTransaction($payOrderId)
    {
        $transactionStatusRequest = new \PayNL\Sdk\Model\Request\TransactionStatusRequest($payOrderId);
        return $transactionStatusRequest->setConfig($this->config->getPayConfig())->start();
    }
}