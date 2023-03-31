<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
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
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;
    /**
     * @var searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param QuoteRepository $quoteRepository
     * @param OrderRepository $orderRepository
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(QuoteRepository $quoteRepository, OrderRepository $orderRepository, PaymentHelper $paymentHelper, OrderRepositoryInterface $orderRepositoryInterface, SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->quoteRepository = $quoteRepository;
        $this->paymentHelper = $paymentHelper;
        $this->orderRepository = $orderRepository;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param order $order
     * @param string $returnUrl
     * @return array
     * @throws GraphQlInputException
     */
    private function startTransactionUrl($order, $returnUrl)
    {
        $redirectUrl = '';
        # Check if order has already been (partially) Paid.
        $orderTotal = round(floatval($order->getBaseGrandTotal()), 2);
        $orderDue = round(floatval($order->getBaseTotalDue()), 2);
        if ($orderDue == 0) {
            throw new GraphQlInputException(__('Order has already been paid.'));
        } elseif ($orderDue != $orderTotal) {
            throw new GraphQlInputException(__('Order has already been partially paid.'));
        }

        $payment = $order->getPayment();
        if (!empty($returnUrl)) {
            $payment->setAdditionalInformation('returnUrl', $returnUrl);
        }

        $methodInstance = $this->paymentHelper->getMethodInstance($payment->getMethod());
        if ($methodInstance instanceof Paymentmethod) {
            if (method_exists($methodInstance, 'setGraphqlVersion')) {
                $methodInstance->setGraphqlVersion(PayHelper::getVersion());
            }
            $redirectUrl = $methodInstance->StartTransaction($order);
        }
        return $redirectUrl;
    }

    /**
     * This function is deprecated, but remains available to keep the old way of starting a transaction through GrapQL working
     * For the new way please use Magento's PlaceOrder Mutation.
     * 
     * @param array $options
     * @return array
     * @throws GraphQlInputException
     */
    public function startTransaction($options)
    {
        $order = $this->orderRepository->get($options['magento_order_id']);

        $quote = $this->quoteRepository->get($order->getQuoteId());
        $quote->setIsActive(true);
        $this->quoteRepository->save($quote);

        $returnUrl = (!empty($options['return_url'])) ? $options['return_url'] : '';
        $redirectUrl = $this->startTransactionUrl($order, $returnUrl);

        return ['redirectUrl' => $redirectUrl];
    }

    /**
     * @param string $orderId
     * @param string $returnUrl
     * @return array
     * @throws GraphQlInputException
     */
    public function placeOrder($orderId, $returnUrl)
    {
        $order = $this->getOrderByIncrementId($orderId);
        $redirectUrl = $this->startTransactionUrl($order, $returnUrl);
        return $redirectUrl;
    }

    /**
     * Get order by increment id.
     * @param string $incrementId
     * @return OrderInterface|null Returns resulting order, null if not found.
     */
    private function getOrderByIncrementId($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orderList = $this->orderRepositoryInterface->getList($searchCriteria)->getItems();

        if (empty($orderList)) {
            return null;
        }

        return array_values($orderList)[0];
    }
}
