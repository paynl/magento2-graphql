<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Paynl\Graphql\Model\CheckToken;

class GetPaymentMethods implements ResolverInterface
{
    /**
     * @var DataProvider\GetPaymentMethods
     */
    private $methodsProvider;

    /**
     * @param DataProvider\GetPaymentMethods $methodsProvider
     */
    public function __construct(DataProvider\GetPaymentMethods $methodsProvider)
    {
        $this->methodsProvider = $methodsProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        return $this->methodsProvider->getPaymentMethods();
    }
}
