<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;

class PayLink
{
    public function __construct(
        QuoteRepository $quoteRepository,
        OrderRepository $orderRepository,
        PaymentHelper $paymentHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->paymentHelper   = $paymentHelper;
        $this->orderRepository = $orderRepository;
    }

    public function getPayLink($options)
    {
        $paylink = '';

        $order = $this->orderRepository->get($options['order_id']);
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
        if ($methodInstance instanceof \Paynl\Payment\Model\Paymentmethod\Paymentmethod) {
            $paylink = $methodInstance->startTransaction($order);
        }

        return ['paylink' => $paylink];
    }
}
