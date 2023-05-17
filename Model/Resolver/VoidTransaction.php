<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Paynl\Graphql\Model\CheckToken;

class VoidTransaction implements ResolverInterface
{
    /**
     * @var DataProvider\VoidTransaction
     */
    private $voidTransactionDataProvider;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param DataProvider\VoidTransaction $voidTransactionDataProvider
     */
    public function __construct(DataProvider\VoidTransaction $voidTransactionDataProvider, AuthorizationInterface $authorization)
    {
        $this->voidTransactionDataProvider = $voidTransactionDataProvider;
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        if (!$this->authorization->isAllowed('Paynl_Payment::graphql_void')) {
            throw new GraphQlAuthorizationException(__('User not authorized.'));
        }
        return $this->voidTransactionDataProvider->VoidTransaction($args);
    }
}
