<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


class Transaction implements ResolverInterface
{
    private $transactionDataProvider;

    /**
     * @param DataProvider\StartTransaction $startTransactionRepository
     */
    public function __construct(
        DataProvider\Transaction $transactionDataProvider
    ) {
        $this->transactionDataProvider = $transactionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        return $this->transactionDataProvider->getTransactionData($args['pay_order_id']);
    }
}
