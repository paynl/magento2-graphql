<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Sales\Api\Data\OrderInterface;
use Paynl\Payment\Model\Config;
use Paynl\Payment\Model\PayPayment;
use Paynl\Result\Transaction\Transaction;
use \Exception;

class CaptureTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var PayPayment
     */
    private $payPayment;

    /**
     * @param Config $config
     * @param OrderInterface $order
     * @param PayPayment $payPayment
     */
    public function __construct(
        Config $config,
        OrderInterface $order,
        PayPayment $payPayment
    ) {
        $this->config = $config;
        $this->order = $order;
        $this->payPayment = $payPayment;
    }

    /**
     * @param array $options
     * @return array
     */
    public function CaptureTransaction($options)
    {

        $result = false;
        $bCaptureResult = false;
        try {
            $this->config->configureSDK();
            $bCaptureResult = \Paynl\Transaction::capture($options['pay_order_id'], null);
            if ($bCaptureResult === true) {
                $message = 'PAY. has successfully captured the transaction.';
                $result = $bCaptureResult;
            } else {
                $message = 'PAY. could not process this capture.';
            }

            if ($this->config->autoCaptureEnabled()) {
                $order = $this->order->load($options['order_id']);
                $order->addStatusHistoryComment(__('PAY. - Performed graphQL-capture. Result: ') . ($bCaptureResult ? 'Success' : 'Failed'))->save();
                # Whether capture failed or succeeded, we still might have to process paid order
                $transaction = \Paynl\Transaction::get($options['pay_order_id']);
                if ($transaction->isPaid()) {
                    $this->payPayment->processPaidOrder($transaction, $order);
                }
            }
        } catch (\Exception $e) {
            $message = strtolower($e->getMessage());
            if (substr($message, 0, 19) == '403 - access denied') {
                $message = 'PAY. could not authorize this capture. Errorcode: PAY-MAGENTO2-GRAPHQL-CAPTURE-001.';
            } else {
                $message = 'PAY. could not process this capture (' . $message . '). Errorcode: PAY-MAGENTO2-CAPTURE-GRAPHQL-002. Transaction: ' . $options['pay_order_id'];
            }
        }
        return ['result' => $result, 'message' => $message];
    }
}
