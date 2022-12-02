<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;
use Paynl\Graphql\Helper\PayHelper;
use Paynl\Payment\Model\Paymentmethod\Paymentmethod;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class GetPayLink
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @param QuoteRepository $quoteRepository
     * @param OrderRepository $orderRepository
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        OrderRepository $orderRepository,
        PaymentHelper $paymentHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->paymentHelper   = $paymentHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $options
     * @return array
     * @throws GraphQlInputException
     */
    public function getPayLink($options)
    {
        $paylink = '';

        $order = $this->orderRepository->get($options['magento_order_id']);

        # Check if order has already been (partially) Paid.
        $orderTotal = round(floatval($order->getBaseGrandTotal()), 2);
        $orderDue = round(floatval($order->getBaseTotalDue()), 2);
        if ($orderDue == 0) {
            throw new GraphQlInputException(__('Order has already been Paid.'));
        } elseif ($orderDue != $orderTotal) {
            throw new GraphQlInputException(__('Order has already been partially Paid.'));
        }

        $quote = $this->quoteRepository->get($order->getQuoteId());
        $quote->setIsActive(true);
        $this->quoteRepository->save($quote);

        $payment = $order->getPayment();
        if (!empty($options['return_url'])) {
            $payment->setAdditionalInformation('returnUrl', $options['return_url']);
        }

        if ($payment->getMethod() != 'paynl_payment_paylink') {
            $payment->setMethod('paynl_payment_paylink');
            $payment->save();
            $order->save();
        }

        $methodInstance = $this->paymentHelper->getMethodInstance($payment->getMethod());
        if ($methodInstance instanceof Paymentmethod) {
            if (method_exists($methodInstance, 'setGraphqlVersion')) {
                $methodInstance->setGraphqlVersion(PayHelper::getVersion());
            }
            $paylink = $methodInstance->startTransaction($order);
        }

        return ['paylink' => $paylink];
    }
}
