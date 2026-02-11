<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Paynl\Payment\Model\Config;
use Paynl\Payment\Model\PayPayment;
use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Model\Request\OrderStatusRequest;

use \Exception;

class CaptureTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderResourceInterface
     */
    private $orderResource;

    /**
     * @var OrderInterfaceFactory
     */
    private $orderFactory;

    /**
     * @var PayPayment
     */
    private $payPayment;

    /**
     * @param Config $config
     * @param OrderResourceInterface $orderResource
     * @param OrderInterfaceFactory $orderFactory
     * @param PayPayment $payPayment
     */
    public function __construct(
        Config $config,
        OrderResourceInterface $orderResource,
        OrderInterfaceFactory $orderFactory,
        PayPayment $payPayment

    ) {
        $this->config = $config;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->payPayment = $payPayment;
    }

    /**
     * @param array $options
     * @return array
     */
    public function CaptureTransaction($options)
    {
        $result = false;
        try {
            $payConfig = $this->config->getPayConfig();
            $orderCaptureRequest = new OrderCaptureRequest($options['pay_order_id']);
            $orderCaptureRequest->setConfig($payConfig);
            $orderCaptureRequest->start();

            $message = 'PAY. has successfully captured the transaction.';
            $result = true;

            if ($this->config->autoCaptureEnabled()) {
                $order = $this->orderFactory->create();
                $this->orderResource->load($order, $options['order_number'], OrderInterface::INCREMENT_ID);
                $order->addStatusHistoryComment(__('PAY. - Performed graphQL-capture. Result: ') . ($result ? 'Success' : 'Failed'))->save();
                # Whether capture failed or succeeded, we still might have to process paid order
                $payOrder = (new OrderStatusRequest($options['pay_order_id']))->setConfig($payConfig)->start();
                if ($payOrder->isPaid()) {
                    $this->payPayment->processPaidOrder($payOrder, $order);
                }
            }
        } catch (\Exception $e) {
            $message = strtolower($e->getMessage());
            if (substr($message, 0, 19) == '403 - access denied') {
                $message = 'PAY. could not authorize this capture. Errorcode: PAY-MAGENTO2-GRAPHQL-CAPTURE-001.';
            } else {
                $message = 'PAY. could not process this capture (' . $message . '). Errorcode: PAY-MAGENTO2-GRAPHQL-CAPTURE-002. Transaction: ' . $options['pay_order_id'];
            }
        }
        return ['result' => $result, 'message' => $message];
    }
}
