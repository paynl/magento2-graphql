<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Paynl\Graphql\Model\CheckToken;

class GetTransaction implements ResolverInterface
{
    /**
     * @var DataProvider\GetTransaction
     */
    private $transactionDataProvider;

    /**
     * @param DataProvider\GetTransaction $transactionDataProvider
     */
    public function __construct(DataProvider\GetTransaction $transactionDataProvider)
    {
        $this->transactionDataProvider = $transactionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        return $this->transactionDataProvider->getTransactionData($args['pay_order_id']);
    }
}
