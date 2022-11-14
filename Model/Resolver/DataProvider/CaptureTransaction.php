<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver\DataProvider;

use Paynl\Payment\Model\Config;
use \Exception;

class CaptureTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $options
     * @return array
     */
    public function CaptureTransaction($options)
    {
        $result = false;
        try {
            $this->config->configureSDK();
            $capture = \Paynl\Transaction::capture($options['pay_order_id'], ($options['amount'] ?? null));
            if ($capture === true) {
                $message = 'PAY. has successfully captured the transaction.';
                $result = $capture;
            } else {
                $message = 'PAY. could not process this capture.';
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
