<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;
use Paynl\Graphql\Helper\PayHelper;
use Paynl\Payment\Model\Paymentmethod\Paymentmethod;

class StartTransaction
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
    public function __construct(QuoteRepository $quoteRepository, OrderRepository $orderRepository, PaymentHelper $paymentHelper)
    {
        $this->quoteRepository = $quoteRepository;
        $this->paymentHelper = $paymentHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $options
     * @return array
     */
    public function startTransaction($options)
    {
        $redirectUrl = '';

        $order = $this->orderRepository->get($options['magento_order_id']);
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $quote->setIsActive(true);
        $this->quoteRepository->save($quote);

        $payment = $order->getPayment();
        if (!empty($options['return_url'])) {
            $payment->setAdditionalInformation('returnUrl', $options['return_url']);
        }

        $methodInstance = $this->paymentHelper->getMethodInstance($payment->getMethod());
        if ($methodInstance instanceof Paymentmethod) {
            if (method_exists($methodInstance, 'setGraphqlVersion')) {
                $methodInstance->setGraphqlVersion(PayHelper::getVersion());
            }
            $redirectUrl = $methodInstance->startTransaction($order);
        }

        return ['redirectUrl' => $redirectUrl];
    }
}
