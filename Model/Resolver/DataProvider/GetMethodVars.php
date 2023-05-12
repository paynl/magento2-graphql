<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Payment\Helper\Data as PaymentHelper;
use Paynl\Payment\Model\Config;
use Paynl\Payment\Model\Paymentmethod\Paymentmethod;

class GetMethodVars
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var Config
     */
    private $paynlConfig;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Config $paynlConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Config $paynlConfig
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->paynlConfig = $paynlConfig;
    }

    /**
     * @param string $method
     * @return string|null
     */
    public function getLogo($method)
    {
        $logo = $this->paynlConfig->getIconUrl($method);
        if (!empty($logo)) {
            return $logo;
        }
        return null;
    }

    /**
     * @param string $method
     * @return array|null
     */
    public function getIssuers($method)
    {
        $methodInstance = $this->paymentHelper->getMethodInstance($method);
        if ($methodInstance instanceof Paymentmethod) {
            if (method_exists($methodInstance, 'getPaymentOptions')) {
                $issuers = $methodInstance->getPaymentOptions();
                if (!empty($issuers)) {
                    return $issuers;
                }
            }
        }
        return null;
    }
}
