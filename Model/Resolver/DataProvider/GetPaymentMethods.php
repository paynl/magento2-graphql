<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Magento\Framework\App\CacheInterface;
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
     * @var CacheInterface
     */
    private $cache;


    /**
     * @param Config $config
     */
    public function __construct(Config $config, PaymentHelper $paymentHelper, Store $store, CacheInterface $cache)
    {
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        $this->store = $store;
        $this->cache = $cache;
    }

    /**
     * @return array[]
     */
    public function getPaymentMethods()
    {
        $paymentMethodList = $this->paymentHelper->getPaymentMethods();

        $activeMethods = [];
        $excludes[] = 'paynl_payment_paylink';
        $issuers = $this->getIssuers();

        foreach ($paymentMethodList as $methodCode => $value) {
            if (strpos($methodCode, 'paynl_') !== false && !in_array($methodCode, $excludes)) {
                $active = $this->store->getConfig('payment/' . $methodCode . '/active');
                if (!empty($active)) {
                    $activeMethods[] = [
                        'name' => $methodCode,
                        'title' => $value['title'] ?? '',
                        'profileid' => $this->store->getConfig('payment/' . $methodCode . '/payment_option_id'),
                        'brandid' => $this->config->brands[$methodCode] ?? '-1',
                        'issuers' => ($methodCode == 'paynl_payment_ideal') ? $issuers : []
                    ];
                }
            }
        }

        return ['methods' => $activeMethods];
    }

    /**
     * @return array|mixed
     */
    private function getIssuers()
    {
        $cache = $this->cache;
        $storeId = $this->store->getId();
        $cacheName = 'paynl_banks_graphql_' . $storeId;

        $banksJson = $cache->load($cacheName);

        if (!empty($banksJson)) {
            $issuers = json_decode($banksJson, true);
        } else {
            $this->config->setStore($this->store);
            $this->config->configureSDK();
            $issuers = \Paynl\Paymentmethods::getBanks();
            $cache->save(json_encode($issuers), $cacheName);
        }

        $new_issuers = [];
        foreach ($issuers as $issuer) {
            unset($issuer['image']);
            $new_issuers[] = $issuer;
        }

        return $new_issuers;
    }

}
