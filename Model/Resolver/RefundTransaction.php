<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Paynl\Graphql\Model\CheckToken;

class RefundTransaction implements ResolverInterface
{
    /**
     * @var DataProvider\RefundTransaction
     */
    private $refundTransactionDataProvider;

    /**
     * @param DataProvider\RefundTransaction $refundTransactionDataProvider
     */
    public function __construct(DataProvider\RefundTransaction $refundTransactionDataProvider)
    {
        $this->refundTransactionDataProvider = $refundTransactionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        return $this->refundTransactionDataProvider->RefundTransaction($args);
    }
}
