<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Payment\Helper\Data as PaymentHelper;
use Paynl\Graphql\Helper\PayHelper;
use Paynl\Payment\Model\Config;
use Magento\Store\Model\Store;

class GetPaymentMethods
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @param Config $config
     */
    public function __construct(Config $config, PaymentHelper $paymentHelper, Store $store)
    {
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        $this->store = $store;
    }

    /**
     * @return string[]
     */
    public function getPaymentMethods()
    {
        $paymentMethodList = $this->paymentHelper->getPaymentMethods();

        $activeMethods = [];
        $excludes[] = 'paynl_payment_paylink';

        foreach ($paymentMethodList as $methodCode => $value) {
            if (strpos($methodCode, 'paynl_') !== false && !in_array($methodCode, $excludes)) {
                $code = $this->store->getConfig('payment/' . $methodCode . '/payment_option_id');
                $active = $this->store->getConfig('payment/' . $methodCode . '/active');
                if (!empty($active)) {
                    $activeMethods[] = [
                        'name' => $methodCode,
                        'title' => $value['title'] ?? '',
                        'profileid' => $code
                    ];
                }
            }
        }

        return ['methods' => $activeMethods];
    }

}
