<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\AuthorizationInterface;
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
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param DataProvider\RefundTransaction $refundTransactionDataProvider
     */
    public function __construct(DataProvider\RefundTransaction $refundTransactionDataProvider, AuthorizationInterface $authorization)
    {
        $this->refundTransactionDataProvider = $refundTransactionDataProvider;
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        if (!$this->authorization->isAllowed('Paynl_Payment::graphql_refund')) {
            throw new GraphQlAuthorizationException(__('User not authorized.'));
        }
        return $this->refundTransactionDataProvider->RefundTransaction($args);
    }
}
