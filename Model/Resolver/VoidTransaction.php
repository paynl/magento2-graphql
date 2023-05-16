<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Paynl\Graphql\Model\CheckToken;

class VoidTransaction implements ResolverInterface
{
    /**
     * @var DataProvider\VoidTransaction
     */
    private $voidTransactionDataProvider;

    /**
     * @param DataProvider\VoidTransaction $voidTransactionDataProvider
     */
    public function __construct(DataProvider\VoidTransaction $voidTransactionDataProvider)
    {
        $this->voidTransactionDataProvider = $voidTransactionDataProvider;
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
