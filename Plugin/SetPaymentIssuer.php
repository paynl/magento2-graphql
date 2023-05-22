<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Plugin;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class SetPaymentIssuer
{
    public function afterExecute(
        \Magento\QuoteGraphQl\Model\Cart\SetPaymentMethodOnCart $subject,
        $null,
        \Magento\Quote\Model\Quote $quote,
        array $paymentData
    ) {
        if (!empty($paymentData['pay_issuer'])) {
            try {
                $payment = $quote->getPayment();
                $payment->setAdditionalInformation('payment_option', $paymentData['pay_issuer']);
                $payment->save();
            } catch (\Exception $e) {
                throw new GraphQlInputException(__($e));
            }           
        }
    }
}
